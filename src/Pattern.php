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
    private $lastExtract = null;

    public function __construct($chiffrageIniFile = null)
    {
        if (empty($chiffrageIniFile) || !preg_match('#\.ini$#i', $chiffrageIniFile)) {
            $chiffrageIniFile = __DIR__.'/../config/chiffrage.ini';
        } elseif (!is_readable($chiffrageIniFile)) {
            throw new InvalidArgumentException("The iniFile {$chiffrageIniFile} does not exist or is not readable.");
        }

        $conf = parse_ini_file($chiffrageIniFile, true);
        foreach ($conf as $name => $cfg) {
            if (isset($this->rules[$cfg['tag']])) {
                throw new InvalidArgumentException("The tag {$cfg['tag']} already exist (rule {$name}).");
            }

            $this->rules[$cfg['tag']] = [
                'name' => $name,
                'time' => $cfg['time'],
                'add' => $cfg['additional'],
            ];
        }
    }

    /**
     * Check the pattern
     *
     * @param string &$pattern Format: £[a-z][a-z][0-9]{0,2}
     *
     * @return bool
     */
    public function isValid(&$pattern)
    {
        $this->lastExtract = null; // reset previous check

        if (empty($pattern)) {
            throw new InvalidArgumentException('The pattern is empty');
        } elseif (!preg_match('#^'. self::REGEXP .'$#i', $pattern, $extract)) {
            throw new InvalidArgumentException("The pattern {$pattern} is invalid.");
        } elseif (!isset($this->rules[$extract[1]])) {
            throw new InvalidArgumentException("The rule {$extract[1]} doesn't exist.");
        }

        // Difficulty: Value by default
        if (empty($extract[3])) {
            $extract[3] = 0;
        }

        $this->lastExtract = $extract;

        return true;
    }

    /**
     * Calculate the time in hours from a pattern.
     *
     * @param string &$pattern
     *
     * @return int
     */
    public function calcul(&$pattern)
    {
        // Check pattern
        try {
            $this->isValid($pattern);

        } catch (InvalidArgumentException $exception) {
            return null;
        }


        // example: £BA2 mean Block Article, difficulté 2, evaluate time: 3h+(0.5*2) = 4h
        $extract = $this->lastExtract;

        $calcul = $this->rules[$extract[1]]['time'];
        $calcul += ($this->rules[$extract[1]]['add'] * $extract[3]);

        return $calcul;
    }
}
