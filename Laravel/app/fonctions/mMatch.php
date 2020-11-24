<?php
require_once('mClub.php');
/**
	 * Classe match pour le projet de simulation de foot
 	* @author PAB
 	* @version 0.1
 */
class Match{
	static $nbr_match = 0; // nombre de match
	private $_id_match; // id du match
	public $_equipes; // les 2 équipes dans un tableau
	public $_resutat; // equipe qui a gagné
	public $score; // score du match sous forme de tableau

	# constructeur
	function __construct($args)
	{
		Match::$nbr_match += 1;
		$this->_id_match = Match::$nbr_match;
		$this->_equipes = $args['equipes'];
		$this->score = [0,0];		
	}

	# affrontement entre 2 equipes
	function play()
	{
		echo "<br><strong>Début du Match </strong><br>";
		$resultat =0 ;
		# gestion des 4 périodes
		for ($i=1; $i <= 4; $i++) {
			# gestion endurance de l equipe
			$this->_equipes[0]->runMatch($i);
			$this->_equipes[1]->runMatch($i);
			# mise à jour de la note equipe			
			$note0 = $this->_equipes[0]->_noteFormation;
			$note1 = $this->_equipes[1]->_noteFormation;
			# gestion de l'aléatoire du foot avec bonus du home 
			$note0 = rand($note0-10,$note0+10);
			$note1 = rand($note1-15,$note1+5);
			echo "<br> période $i $note0 $note1";
			if ($note0 === $note1) {
				$resultat += 0;
			}
			elseif ($note0 > $note1) {
				$resultat += 1; 
			}
			else{
				$resultat -=1;
			}
		}
		if ($resultat === 0){
			$this->_resutat = 'N';
			$this->_equipes[0]->_points += 1;
			$this->_equipes[1]->_points += 1;
		}
		elseif ($resultat > 0) {
			$this->_resutat = 'V';
			$this->_equipes[0]->_points += 3;
		}
		else{
			$this->_resutat = 'D';
			$this->_equipes[1]->_points += 3;
		}
		# gestion du score en fonction du résultat
		$this->score($this->_resutat,$resultat);
		echo "<br> Resultat : ".$this->_resutat."<br>";
		echo "Score : ".$this->score[0]."-".$this->score[1]."<br>";
		# gestion de la phase de repos
		$this->_equipes[0]->repos();
		$this->_equipes[1]->repos();
		# gestion de la billetterie 80 / 20 
		$this->_equipes[0]->_budget += floor($this->_equipes[0]->_stade['capacite']*0.008);
		$this->_equipes[1]->_budget += floor($this->_equipes[0]->_stade['capacite']*0.002);
	}
	
	# gestion du score
	function score($resultat,$domination)
	{
		$butA = $this->score[0];
		$butB = $this->score[1];
		$domination = abs($domination);
		if ($resultat == 'N')
		{
			$butA = $butB = rand(0,$domination+rand(0,3));
		}
		elseif ($resultat == 'V')
		{
			$noteAttEquipe1 = $this->_equipes[0]->_noteAttaque;
			$noteDefEquipe2 = $this->_equipes[1]->_noteDefense;
			$chance2buts = $noteAttEquipe1 - $noteDefEquipe2;
			if ($chance2buts < 1){
				$chance2buts = 0;
			}
			elseif ($chance2buts < 5){
				$chance2buts = 2;
			}
			else
			{
				$chance2buts = 4;
			}
			$butA = rand(1,$domination+$chance2buts);
			$butB = rand(0,$butA-1);			
		}
		else {
			$noteAttEquipe2 = $this->_equipes[1]->_noteAttaque;
			$noteDefEquipe1 = $this->_equipes[0]->_noteDefense;
			$chance2buts = $noteAttEquipe2 - $noteDefEquipe1;
			if ($chance2buts < 1){
				$chance2buts = 0;
			}
			elseif ($chance2buts < 5){
				$chance2buts = 2;
			}
			else
			{
				$chance2buts = 4;
			}
			$butB = rand(1,$domination+$chance2buts);
			$butA = rand(0,$butB-1);
		}
		$this->score = [$butA,$butB];
	} 

	function __toString()
	{
		return ucfirst($this->_equipes[0]->_nom) .' vs '. ucfirst($this->_equipes[1]->_nom); 
	}	
}