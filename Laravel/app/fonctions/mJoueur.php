<?php
/**
	* Classe joueur pour le projet de simulation de foot
 	* @author PAB
 	* @version 0.1
 */
class Joueur{
	const CODEPOSTES = array('GK'=>0,'DG'=>1,'DC'=>2,'DD'=>3,'MG'=>4,'MC'=>5,'MD'=>6,'AG'=>7,'AC'=>8,'AD'=>9);
	const POSITION = array(0=>'gardien',1=>'defense',2=>'milieu',3=>'attaque');
	const COULOIR = array(0=>'gauche',1=>'centre',2=>'droit');
	static $nbr_joueur = 0; // nombre de joueur
	private $_id_joueur; // id du joueur
	public $_nom;
	public $_prenom;
	public $_age;
	public $_contrat; //{'club'=>'name','duree'=>5,'salaire'=>20}
	public $_pposition; // attaque, milieu, defense, gardien
	public $_pcouloir; // droit, gauche, centre
	private $_stat; // {'tir'=>100, 'passe'=>100, 'technique'=>100, 'placement'=>100, 'vitesse'=>100, 'tacle'=>100, 'arret'=>100, 'endurance'=>100}
	public $_noteG; // somme des notes
	public $_forme; // etat de forme
	public $_prix; // prix du joueur

	# constructeur de la classe
	function __construct($args)
	{
		Joueur::$nbr_joueur += 1;
		$this->_id_joueur = Joueur::$nbr_joueur;
		$this->_nom = $args['nom'];
		$this->_prenom = $args['prenom'];
		$this->_age = $args['age'];
		$this->_pposition = self::POSITION[$args['position']];
		$this->_pcouloir = self::COULOIR[$args['couloir']];
		$this->posteName();
		$this->_stat = $args['stat'];
		$this->_tmpStat = $args['stat'];
		$this->_forme = 100;
		$this->notesBuild();
		$this->newContrat();
		$this->_prix = 0;
	}

	# getter id
	function getId()
	{
		return $this->_id_joueur;
	}

	# création d'un contrat
	function newContrat($nameClub='libre',$duree=0,$salaire=0)
	{
		$contrat = array('club'=>$nameClub,'duree'=>$duree,'salaire'=>$salaire);
		$this->_contrat = $contrat;
		$this->_prix = $this->_noteG + $this->_contrat['duree'] + $this->_contrat['salaire'];
	}

	# fonction de RAZ des notes
	function repos()
	{
		$this->_tmpStat = $this->_stat;
		$this->_forme = 99;
		$this->notesBuild();
	}
	# calcul des notes
	private function notesBuild()
	{
		$this->attaque = floor(array_sum([$this->_stat['tir'],$this->_stat['tir'],$this->_stat['vitesse'],$this->_stat['technique']])/4);
		$this->milieu = floor(array_sum([$this->_stat['passe'],$this->_stat['endurance'],$this->_stat['technique'],$this->_stat['placement']])/4);
		$this->defense = floor(array_sum([$this->_stat['tacle'],$this->_stat['placement'],$this->_stat['tacle'],$this->_stat['passe']])/4);
		$this->gardien = floor(array_sum([$this->_stat['arret'],$this->_stat['placement'],$this->_stat['arret'],$this->_stat['tacle']])/4);
		$somme = 0;
		foreach ($this->_tmpStat as $key => $value) {
			$somme += $value;
		}
		$this->_noteG = floor($somme / 8);
	}

	# gestion du nom du poste
	function posteName()
	{ // gerer GK
		$name = $this->_pposition[0];
		if ($name === 'g'){
			$name .= 'k';
		}else{
			$name .= $this->_pcouloir[0];
		}
		$this->poste = strtoupper($name);
		$this->posteMatch = $this->poste;
	}

	# gestion des notes du joueurs en fonction de son positionnement
	function notesPoste($poste='GK')
	{ // check for Goal and DD
		//echo "-----poste : ".$this->poste ."". $poste;
		if ($this->posteMatch === $poste)
		{
			return 1;
		}
		elseif (stripos($this->posteMatch,$poste[0])) 
		{ // same line
			return 1/2;
		}
		elseif (stripos($this->posteMatch,$poste[1])) 
		{ // same raw
			return 1/4;
		}
		else
		{
			return 1/10;
		}
	}

	# gestion des périodes de match pour diminuer les notes
	function runMatch($time=1,$poste='GK')
	{
		if ($this->_stat['endurance']<70){
			$attenuation = [0,8,18,35,65];
		}
		elseif($this->_stat['endurance']<80){
			$attenuation = [0,7,15,30,60];
		}
		elseif($this->_stat['endurance']<90){
			$attenuation = [0,6,12,25,50];
		}
		else{
			$attenuation = [0,5,10,20,40];
		}
		
		if ($time===1)
		{
			$this->_forme -= $attenuation[$time]; // ajouter un facteur lié à l'endurance
		}
		elseif ($time===2) {
			$this->_forme -= $attenuation[$time];
		}
		elseif ($time===3) {
			$this->_forme -= $attenuation[$time];
		}
		else{
			$this->_forme -= $attenuation[$time];
		}
		if ($this->_forme <= 0){$this->_forme = 5;}
		$coeff = $this->notesPoste($poste);
		#echo "-----coef : ".$coeff;
		#echo "-----forme : ".$this->_forme;
		foreach ($this->_stat as $key => $value) {
			#echo "-----forme : ". $this->_forme ."<br>";
			#echo "-----coef : ". $coeff ."<br>";
			#echo "-----value : ". $value ."<br>";
			#echo "-----values : ". ($value*$coeff+$this->_forme)/2 ."<br>";
			$this->_tmpStat[$key] = ($value*$coeff+$this->_forme)/2;
		}
		$this->notesBuild();
	}

	# getter et setter
	function action($arg,$value)
	{
		if (isset($value))
		{
			$this->{$arg} = $value;
		}
		else
		{
			return $this->{$arg};
		} 
	}

	# gestion de la fin de saison age + 1, contrat -1, endurance -1
	function nextYear()
	{
		$this->_age +=1;
		$this->contrat['duree'] -= 1;
		if ($this->_age > 38)
		{
			$this->contrat['duree'] = 0;
			$this->contrat['salaire'] = 0;
			$this->contrat['club'] = 'retraite';
		}
		if ($this->contrat['duree'] <= 0)
		{
			$this->contrat['duree'] = 0;
			$this->contrat['salaire'] = 0;
			$this->contrat['club'] = 'libre';			
		}
		# gestion de l'age
		if ($this->_age > 30)
		{
			$this->stat['endurance'] -=10; 
		}
		elseif ($this->_age > 27)
		{
			$this->stat['endurance'] -=2;
		}
		else {
			$this->stat['endurance'] +=2;
		}
		$this->_prix = $this->_noteG + $this->_contrat['duree'] + $this->_contrat['salaire'];
		
	}

	# affichage
	function __toString()
	{
		$output = '<table>';
		$output .= '<tr><td> Prénom : '.ucfirst($this->_prenom).'</td><td> Nom : '.ucfirst($this->_nom).'</td></tr>';
		$output .= '<tr><td> Age : '.$this->_age.'</td><td> Note : '.$this->_noteG.'</td></tr>';
		$output .= '<tr><td> Club : '.ucfirst($this->_contrat['club']).'</td><td> Contrat : '.$this->_contrat['duree'].' années</td></tr>';
		$output .= '<tr><td> Position : '.ucfirst($this->_pposition).'</td><td> Couloir : '.ucfirst($this->_pcouloir).'</td></tr>';
		foreach ($this->_stat as $key => $value) {
			$output .= '<tr><td>'.ucfirst($key).'</td><td>'.$value.'</td></tr>';
		}
		$output .= '<tr><td> Note Gardien : </td><td>'.$this->gardien.'</td></tr>';
		$output .= '<tr><td> Note Défenseur : </td><td>'.$this->defense.'</td></tr>';
		$output .= '<tr><td> Note Milieu : </td><td>'.$this->milieu.'</td></tr>';
		$output .= '<tr><td> Note Attaquant : </td><td>'.$this->attaque.'</td></tr>';
		$output .= '<tr><td> Forme : '.$this->_forme.'</td><td> ID : '.$this->_id_joueur.'</td></tr>';
		$output .= '</table>';
		return $output;
	}
}
/*
echo "<br>test<br>";
$stat = array('tir'=>100, 'passe'=>100, 'technique'=>100, 'placement'=>100, 'vitesse'=>100, 'tacle'=>100, 'arret'=>100, 'endurance'=>100);
$parms = array('nom'=>'toto','prenom'=>'toto','age'=>20,'position'=>3,'couloir'=>2,'stat'=>$stat);
$objeTest = new Joueur($parms);
echo $objeTest;
//print_r($objeTest->_stat);
*/

