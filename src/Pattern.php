<?php
/*****************************************************************************************************
 * @name Chiffrage Pattern
 * @note: Returns from a tag, a number of hours
 * @author Jgauthi <github.com/jgauthi>
 * @version 1.0

 ******************************************************************************************************/
namespace Jgauthi\Tools\Chiffrage;

class Pattern
{
    private $rules = [];
    private $last_extract = null;
    private $regexp = '\£([a-z])([a-z])([0-9]{1,2})?';

    public function __construct($chiffrageIniFile = null)
    {
        if (empty($chiffrageIniFile) || !preg_match('#\.ini$#i', $chiffrageIniFile)) {
            $chiffrageIniFile = __DIR__.'/../config/chiffrage.ini';
        } elseif (!is_readable($chiffrageIniFile)) {
            user_error("Le fichier {$chiffrageIniFile} n'existe pas ou n'est pas accessible en lecture.");
            return false;
        }

        $conf = parse_ini_file($chiffrageIniFile, true);
        foreach ($conf as $name => $cfg) {
            if (isset($this->rules[$cfg['tag']])) {
                user_error("Le tag {$cfg['tag']} existe déjà (rule {$name}).");
                continue;
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
            return !user_error('Le pattern indiqué est vide.');
        } elseif (!preg_match("#^{$this->regexp}$#", strtolower($pattern), $extract)) {
            return !user_error("Le pattern {$pattern} est invalide.");
        } elseif (!isset($this->rules[$extract[1]])) {
            return !user_error("La règle {$extract[1]} n'existe pas.");
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
        if (!$this->is_valid($pattern)) {
            return null;
        }

        // exemple: £BA2 signifie Block Article, difficulté 2, temps estimé: 3h+(0.5*2) = 4h
        $extract = $this->last_extract;

        $calcul = $this->rules[$extract[1]]['time'];
        $calcul += ($this->rules[$extract[1]]['add'] * $extract[3]);

        return $calcul;
    }

    /**
     * Retourne l'expression regulière pour être utilisé en-dehors de la class.
     *
     * @return string
     */
    public function get_regexp()
    {
        return $this->regexp;
    }
}
