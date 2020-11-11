<?php

namespace App\Http\Controllers;

use Redirect;
use Illuminate\Http\Request;
use App\Club;
use App\Ville;
use App\Stade;
use App\Joueur;
use App\Contrat;
use DB;

class HomePageController extends Controller
{
    public function main(){
        $clubs = DB::select('select * from clubs order by nomClub');
        $joueurs = DB::select('select * from joueurs order by nom, prenom, age');
        if($clubs!=null){

        }else{
            buildClub(10);
            $clubs = DB::select('select * from clubs order by nomClub');
        }
        if($joueurs!=null){

        }else{
            buildJoueur(200);
            $joueurs = DB::select('select * from joueurs order by nom, prenom, age');
            lierClubJoueur();
        }
        return view('welcome',[
            'clubs'=>$clubs,
            'joueurs'=>$joueurs
        ]);
    }

    public function init(){
        $clubs = DB::select('select * from clubs order by nomClub');
        $joueurs = DB::select('select * from joueurs order by nom, prenom, age');
        if($clubs!=null){

        }else{
            buildClub(10);
        }
        if($joueurs!=null){

        }else{
            buildJoueur(200);
            lierClubJoueur();
        }
        $clubs= DB::select('SELECT distinct clubs.nomClub, joueurs.nom, joueurs.prenom from clubs, contrats, joueurs where clubs.nomClub=contrats.nomClub and contrats.idJoueur=joueurs.id ORDER BY clubs.nomClub');
        return $clubs;
    }

    public function start(){
        $main=DB::table('clubs')->select('nomClub')->where('isMain','1')->get();

        if($main=='1'){
            return view('/jouer');
        }else{
            return Redirect::action('StartController@liste');
        }
    }
}


define ('VILLE',array('Ajaccio','Bastia','Borgo','Bastellica','Vizzavona','Vico','Venaco',
'Vivario','Peri','Tavera','Tasso','Biguglia','Porreta','Baleone','Lumio','Calvi','Ile Rousse','Ponte-Novu','Ponte-Leccia','Montegrosso','Zilia','Calenzana','Monticello','Lavatoggio'));

define ('CLUB',array('SC','SEC','AC','FC','Real','O','AS','Rayo','CF','GFC','EDF','GDF','AXA','Inter','FFF','ASS','AU','CCU','CNU','BNP','Park'));

define('STADE',array('city','terrain vague','stade','parking','cour','préau','plage','potager','stadium', ));

define('PRENOM',array('Adan','Adrien','Alain','Alex','Alexandre','Antoine','Antony','Bastien','Bilal','Brian','Brice','Benjamin','Ben','Charles','Carles','Cedric','Damien','David','Dave','Kevin','Quentin','Fabien','Francois','Fred','Frederic','Goerge','Gerard','Gauthier','Greg','Gregory','Gregoire','Jean','Kayl','Louis','Loris','Michel','Morgan','Mathieu','Mataé','Nicolas','Paul','Philippe','Pierre','Jacques','Quentin','Rachid','Amed','Hamed','Luke','Han','Ken','Shiryu','Luffy','Zoro','Sanji','Chopper','Gabriel','Seraphin','Raphael','Ralph','Stephane','Serguei','Tangui','Francisco','Daniel','Robin'));

define('NOM',array('Casanova','Mancini','Mecev','Luciani','Mariani','Michelozzi','Agostini','Martinez','Morazzani','Colonna','Mattei','Muntoni','Paoli','Mondoloni','Nicolas','Bartoli','Natalini','Paolini','Rossi','Orsini','Paris','Albertini','Orsoni','Peres','Filippi','Palazzo','Perez','Cesari','Serra','Pieri','Moreau','Antoniotti','Pietri','Poggi','Appietto','Platel','Angeli','Battesti','Popo','Carlotti','Bertrand','Renucci','Graziani','Costa','Rousier','Leca','Cucchi','Roussel','Marcaggi','Fancello','Santer','Nicolai','Maroselli','Santucci','Santoni','Micheli','Scarbonchi','Thomas','Michel','Simon','Cardi','Moracchini','Tamburini','Acquaviva','Ottavy','Tasso','Andreani','Peretti','Vincenti','Astolfi','Pinheiro','Werle','Castelli','Paoli','Angelini','Cipriani','Raffalli','Agnelot','Garcia','Sauli','Arrighi','Grimaldi','Serreri','Barbier','Guidoni','Torre','Bazzali','Lucchini','Martini','Belingard'));

function buildClub($nbr,$array_ville=VILLE,$array_nomClub=CLUB,$array_nomStade=STADE){
    for ($i=0; $i < $nbr; $i++) {
        //Ville
        $nomVille = rand(0,count($array_ville)-1);
        $nomVille = $array_ville[$nomVille];
        $nomExiste=DB::table('villes')->where('nomVille',$nomVille)->value('nomVille');
        if($nomExiste==null){
            $ville = new Ville();
            $ville->nomVille=$nomVille;
            $ville->attractivite = rand(60,100);
            $ville->save();
        }
        //Stade
        $nomStade = rand(0,count($array_nomStade)-1);
        $nomStade = $array_nomStade[$nomStade];
        $nomStade = $nomStade.' de '.$nomVille;
        $nomExiste=DB::table('stades')->where('nomStade',$nomStade)->value('nomStade');
        if($nomExiste==null){
            $stade = new Stade();
            $stade->nomStade = $nomStade;
            $stade->capacite = rand(8000,80000);
            $stade->save();
        }
        //Club
        $nomClub = rand(0,count($array_nomClub)-1);
        $nomClub = $array_nomClub[$nomClub];
        $nomClub = $nomClub .' de ' . $nomVille;
        $club = new Club();
        $club->nomClub=$nomClub;
        $club->reputation=0;
        $club->budget=200;
        $club->formation='[1,2,1]';
        $club->noteAbsolue=0;
        $club->noteFormation=0;
        $club->noteInstantannee=0;
        $club->nomStade=$nomStade;
        $club->nomVille=$nomVille;
        $club->save();
    }
}

function buildJoueur($nbr,$array_prenom=PRENOM,$array_nom=NOM){
    $arrayJoueur = array();
	for ($i=0; $i < $nbr; $i++) {
		# NOM
		$nom = rand(0,count($array_nom)-1);
		$nom = $array_nom[$nom];
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
		# STATS
        $tir=rand($at0,$at1);
        $passe=rand($ml0,$ml1);
        $technique=rand(40,100);
        $placement=rand(40,100);
        $vitesse=rand(40,100);
        $tacle=rand($df0,$df1);
        $arret=rand($gk0,$gk1);
        $endurance=rand(60,100);
        $forme=rand(60,100);
		$noteGardien=($placement+$arret+$arret+$tacle)/4;
		$noteDefense=($placement+$tacle+$passe+$tacle)/4;
		$noteMilieu=($placement+$passe+$technique+$vitesse)/4;
		$noteAttaque=($tir+$tir+$vitesse+$technique)/4;
        $max=$noteGardien;
        $postePredilection='Gardien';
        if($max<$noteDefense){
            $max=$noteDefense;
            $postePredilection='Defenseur';
        }
        if($max<$noteMilieu){
            $max=$noteMilieu;
            $postePredilection='Milieu';
        }
        if($max<$noteAttaque){
            $max=$noteAttaque;
            $postePredilection='Attaquant';
        }

        $joueur = new Joueur();
        $joueur->nom=$nom;
        $joueur->prenom=$prenom;
        $joueur->age=$age;
        $joueur->noteGardien=$noteGardien;
        $joueur->noteDefense=$noteDefense;
        $joueur->noteAttaque=$noteAttaque;
        $joueur->noteMilieu=$noteMilieu;
        $joueur->tir=$tir;
        $joueur->passe=$passe;
        $joueur->technique=$technique;
        $joueur->placement=$placement;
        $joueur->vitesse=$vitesse;
        $joueur->tacle=$tacle;
        $joueur->arret=$arret;
        $joueur->forme=$forme;
        $joueur->endurance=$endurance;
        $joueur->postePredilection=$postePredilection;
        $joueur->noteGlobale=$tir+$passe+$technique+$placement+$vitesse+$tacle+$arret+$endurance;
        $joueur->noteInstantanee=$tir+$passe+$technique+$placement+$vitesse+$tacle+$arret+$endurance;
        $joueur->save();
	}
}

function lierClubJoueur(){
    $nbJoueur = 1;
    $clubs = DB::select('select * from clubs');
    foreach ($clubs as $club){
        for($i= 0; $i<5 ; $i++){
            $contrat = new Contrat();
            $contrat->nomClub =$club->nomClub;
            $contrat->idJoueur = $nbJoueur;
            $contrat->dateFin = date( 'y-m-d', strtotime('+1 year'));
            $contrat->salaire=20;
            DB::table('clubs')->where('nomClub', $club->nomClub)->decrement('budget',20);
            $contrat->save();
            $nbJoueur++;
        }
    }
}
