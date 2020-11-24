<?php
require_once('mMatch.php');
/**
	 * programme pricipal pour le projet de simulation de foot
 	* @author PAB
 	* @version 0.1
 */
define ('PRENOM',array('Adan','Adrien','Alain','Alex','Alexandre','Antoine','Antony','Bastien',
'Bilal','Brian','Brice','Benjamin','Ben','Charles','Carles','Cedric','Damien','David','Dave','Kevin','Quentin',
'Fabien','Francois','Fred','Frederic','Goerge','Gerard','Gauthier','Greg','Gregory','Gregoire',
'Jean','Kayl','Louis','Loris','Michel','Morgan','Mathieu','Mataé','Nicolas','Paul','Philippe',
'Pierre','Jacques','Quentin','Rachid','Amed','Hamed','Luke','Han','Ken','Shiryu','Luffy',
'Zoro','Sanji','Chopper','Gabriel','Seraphin','Raphael','Ralph','Stephane','Serguei','Tangui'));

define ('VILLE',array('Ajaccio','Bastia','Borgo','Bastellica','Vizzavona','Vico','Venaco',
'Vivario','Peri','Tavera','Tasso','Biguglia','Porreta','Baleone','Lumio','Calvi','Ile Rousse','Ponte-Novu','Ponte-Leccia','Montegrosso','Zilia','Calenzana','Monticello','Lavatoggio'));

define ('CLUB',array('SC','SEC','AC','FC','Real','O','AS','Rayo','CF','GFC','EDF','GDF','AXA','Inter','FFF','ASS','AU','CCU','CNU','BNP','Park'));

define('STADE',array('city','terrain vague','stade','parking','cour','préau','plage','potager','stadium', ));

function buildJoueur($nbr,$array_prenom=PRENOM)
{
	$arrayJoueur = array();
	for ($i=0; $i < $nbr; $i++) {
		# NOM
		$nom = '';
		$longeurMot = rand(5,9);
		for ($j=0; $j <= $longeurMot; $j++) { 
		 	$nom .= chr(rand(97,122));
		 } 
		# PRENOM 
		$prenom = rand(0,count($array_prenom)-1);
		$prenom = $array_prenom[$prenom];
		# AGE 
		$age = rand(17,34);
		
		# POSITION
		$position = rand(0,3);
		if ($position === 0) {
			$gk0 = 70; $at0 = 0; $ml0 = 0; $df0 = 40;
			$gk1 = 100; $at1 = 20; $ml1 = 20; $df1 = 80;
		}
		elseif ($position === 1) {
			$gk0 = 0; $at0 = 0; $ml0 = 25; $df0 = 70;
			$gk1 = 10; $at1 = 20; $ml1 = 55; $df1 = 100;
		}
		elseif ($position ===2) {
			$gk0 = 0; $at0 = 45; $ml0 = 70; $df0 = 45;
			$gk1 = 10; $at1 = 80; $ml1 = 100; $df1 = 60;
		}
		else{
			$gk0 = 0; $at0 = 70; $ml0 = 30; $df0 = 00;
			$gk1 = 10; $at1 = 100; $ml1 = 70; $df1 = 30;			
		}
		# COULOIR
		$couloir = rand(0,2);
		# STATS
		$stat = array(
			'tir'=>rand($at0,$at1), 
			'passe'=>rand($ml0,$ml1), 
			'technique'=>rand(40,100), 
			'placement'=>rand(40,100), 
			'vitesse'=>rand(40,100), 
			'tacle'=>rand($df0,$df1), 
			'arret'=>rand($gk0,$gk1), 
			'endurance'=>rand(60,100));
		$params = array('nom'=>$nom,'prenom'=>$prenom,'age'=>$age,'position'=>$position,'couloir'=>$couloir,'stat'=>$stat);
		$arrayJoueur[$i] = new Joueur($params);
	}
	return $arrayJoueur;
}

function buildClub($nbr,$array_ville=VILLE,$array_nomClub=CLUB,$array_nomStade=STADE)
{
	$arrayClub = array();
	for ($i=0; $i < $nbr; $i++) { 
		# ville
		$nomVille = rand(0,count($array_ville)-1);
		$nomVille = $array_ville[$nomVille];
		$ville = array('ville'=>$nomVille,'attractivite'=>rand(60,100)); 
		# stade
        $nomStade = rand(0,count($array_nomStade)-1);
		$nomStade = $array_nomStade[$nomStade];
		$nomStade = $nomStade.' de '.$nomVille;
		$stade = array('nom'=>$nomStade,'capacite'=>rand(8000,80000)); 
		# nom
		$nomClub = rand(0,count($array_nomClub)-1);
		$nomClub = $array_nomClub[$nomClub];
		$nomClub = $nomClub .' de ' . $nomVille;		
		$params = array('nom'=>$nomClub,'ville'=>$ville,'stade'=>$stade,'effectif'=>[],'equipe'=>[]);
		$arrayClub[$i] = new Club($params);
	}
	return $arrayClub;
}

function main()
{
	echo '<p class="title">Liste des clubs générés</p>';
	# CLUB
	$arrayClub = array();
	$arrayClub = buildClub(10);
    echo '<div class="table-container has-text-centered"><table class="table is-fullwidth"><thead><tr>';
    echo '<th>Club</th><th>Ville</th><th>Stade</th>';
    echo'</tr></thead>';
	foreach ($arrayClub as $key => $value) {
		echo $value;
	}
    echo '</table></div>';
	/*# 10 joueurs
	$arrayJoueur = array();
	$arrayJoueur = buildJoueur(10);
	foreach ($arrayJoueur as $key => $value) {
		echo $value;
		echo '<br>';
	}
	# Ajout dans l'équipe 1
	$arrayClub[0]->setEffectif($arrayJoueur);
	$arrayClub[0]->setFormation([2,1,1]);
	echo $arrayClub[0]->prinTeam();
	# 10 joueurs
	$arrayJoueur = array();
	$arrayJoueur = buildJoueur(10);
	foreach ($arrayJoueur as $key => $value) {
		echo $value;
		echo '<br>';
	}
	# Ajout dans l'équipe 2
	$arrayClub[1]->setEffectif($arrayJoueur);
	$arrayClub[1]->setFormation([1,2,1]);
	echo $arrayClub[1]->prinTeam();
	# Match
	$params = array("equipes"=>[$arrayClub[0],$arrayClub[1]]);
	$journee1 = new Match($params);
	echo $journee1;
	$journee1->play();*/
}

main();

?>