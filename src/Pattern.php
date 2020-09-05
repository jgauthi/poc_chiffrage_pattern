<?php
/*****************************************************************************************************
 * @name Chiffrage Pattern
 * @note: Returns from a tag, a number of hours
 * @author Jgauthi <github.com/jgauthi>
 * @version 1.0

 ******************************************************************************************************/
namespace Jgauthi\Tools\Chiffrage;

use InvalidArgumentException;

class Pattern
{
    const REGEXP = '\£([a-z])([a-z])([0-9]{1,2})?';

    private $rules = [];
    private $last_extract = null;

    public function __construct($chiffrageIniFile = null)
    {
        if (empty($chiffrageIniFile) || !preg_match('#\.ini$#i', $chiffrageIniFile)) {
            $chiffrageIniFile = __DIR__.'/../config/chiffrage.ini';
        } elseif (!is_readable($chiffrageIniFile)) {
            throw new InvalidArgumentException("Le fichier {$chiffrageIniFile} n'existe pas ou n'est pas accessible en lecture.");
        }

        $conf = parse_ini_file($chiffrageIniFile, true);
        foreach ($conf as $name => $cfg) {
            if (isset($this->rules[$cfg['tag']])) {
                throw new InvalidArgumentException("Le tag {$cfg['tag']} existe déjà (rule {$name}).");
            }

            $this->rules[$cfg['tag']] = [
                'name' => $name,
                'time' => $cfg['time'],
                'add' => $cfg['additional'],
            ];
        }
    }

    /**
     * Check le pattern indiqué.
     *
     * @param string &$pattern Format: £[a-z][a-z][0-9]{0,2}
     *
     * @return bool
     */
    public function is_valid(&$pattern)
    {
        $this->last_extract = null; // reset check précédent

        if (empty($pattern)) {
            throw new InvalidArgumentException('Le pattern indiqué est vide.');
        } elseif (!preg_match('#^'. self::REGEXP .'$#i', $pattern, $extract)) {
            throw new InvalidArgumentException("Le pattern {$pattern} est invalide.");
        } elseif (!isset($this->rules[$extract[1]])) {
            throw new InvalidArgumentException("La règle {$extract[1]} n'existe pas.");
        }

        // Difficulté: Valeur par défaut
        if (empty($extract[3])) {
            $extract[3] = 0;
        }

        $this->last_extract = $extract;

        return true;
    }

    /**
     * Calcul le temps en heure à partir d'un pattern.
     *
     * @param string &$pattern
     *
     * @return int
     */
    public function calcul(&$pattern)
    {
        // Check pattern
        try {
            $this->is_valid($pattern);

        } catch (InvalidArgumentException $exception) {
            return null;
        }


        // exemple: £BA2 signifie Block Article, difficulté 2, temps estimé: 3h+(0.5*2) = 4h
        $extract = $this->last_extract;

        $calcul = $this->rules[$extract[1]]['time'];
        $calcul += ($this->rules[$extract[1]]['add'] * $extract[3]);

        return $calcul;
    }
}
