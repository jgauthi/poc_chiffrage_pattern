<?php
/*****************************************************************************************************
  * @name Chiffrage Pattern
  * @note: Retourne à partir un tag, un nombre d'heure
  * @author Jérémy Gauthier <github.com/jgauthi>
  * @version 1.0

******************************************************************************************************/

namespace Jgauthi\Tools\Chiffrage;

class Pattern
{
  private $rules = array();
  private $last_extract = null;
  private $regexp = '\£([a-z])([a-z])([0-9]{1,2})?';

	public function __construct()
	{
		// Récupération du fichier de conf
		$conf = parse_ini_file(__DIR__.'/config/chiffrage.ini', true);

		// Définition des règles
		foreach($conf as $name => $cfg)
		{
			if(isset($this->rules[ $cfg['tag'] ]))
			{
				user_error("Le tag {$cfg['tag']} existe déjà (rule {$name}).");
				continue;
			}

			$this->rules[ $cfg['tag'] ] = array
			(
				'name'	=>	$name,
				'time'	=>	$cfg['time'],
				'add'	=>	$cfg['additionnal']
			);
		}
	}

	/**
	 * Check le pattern indiqué
	 * @param  string  &$pattern Format: £[a-z][a-z][0-9]{0,2}
	 * @return boolean
	 */
	public function is_valid(&$pattern)
	{
		$this->last_extract = null; // reset check précédent

		if(empty($pattern))
			return !user_error("Le pattern indiqué est vide.");

		elseif(!preg_match("#^{$this->regexp}$#", strtolower($pattern), $extract))
			return !user_error("Le pattern {$pattern} est invalide.");

		elseif(!isset($this->rules[ $extract[1] ]))
			return !user_error("La règle {$extract[1]} n'existe pas.");

		// Difficulté: Valeur par défaut
		if(empty($extract[3]))
			$extract[3] = 0;


		$this->last_extract = $extract;
		return true;
	}

	/**
	 * Calcul le temps en heure à partir d'un pattern
	 * @param  string &$pattern
	 * @return int
	 */
	public function calcul(&$pattern)
	{
		// Check pattern
		if(!$this->is_valid($pattern))
			return null;

		// exemple: £BA2 signifie Block Article, difficulté 2, temps estimé: 3h+(0.5*2) = 4h
		$extract = $this->last_extract;

		$calcul  = $this->rules[ $extract[1] ]['time'];
		$calcul += ($this->rules[ $extract[1] ]['add'] * $extract[3]);

		return $calcul;
	}

	/**
	 * Retourne l'expression regulière pour être utilisé en-dehors de la class
	 * @return string
	 */
	public function get_regexp()
	{
		return $this->regexp;
	}
}

?>