<?php

namespace App\Http\Controllers;

use App\Contrat;
use App\Joueur;
use App\Club;
use App\Ville;
use App\Stade;
use DB;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    public function init(){
        DB::table('contrats')->delete();
        DB::table('classements')->delete();
        DB::table('joueurs')->delete();
        DB::table('club_domiciles')->delete();
        DB::table('club_exterieurs')->delete();
        DB::table('matches')->delete();
        DB::table('competitions')->delete();
        DB::table('clubs')->delete();
        DB::table('villes')->delete();
        DB::table('stades')->delete();
        buildClub(10);
        buildJoueur(200);
        lierClubJoueur();
    }

    public function index(){
        return Club::all();
    }

    public function show($nomClub){
        return Club::find($nomClub);
    }

    public function chooseClub(Request $request, $nomClub){
        DB::update('update clubs set isMain=null where isMain IS NOT NULL');
        $club=Club::findOrFail($nomClub);
        $club->update($request->all());
    }

    public function getInfoClub(Request $request){
        $idClub=$request->all();
        $idClub=$idClub['idClub'];
        $infoClub=DB::select('select * from clubs where id='.$idClub);
        $joueurs=DB::select('select joueurs.* from joueurs, contrats where joueurs.id=contrats.idJoueur and joueurs.joue!="Banc" and contrats.idClub='.$idClub);
        $return = array('nomClub'=>$infoClub[0]->nomClub, 'noteClub'=>($infoClub[0]->noteInstantannee+$infoClub[0]->noteFormation)/2, 'joueurs'=>$joueurs);
        return $return;
    }

    public function update(Request $request, $nomClub){
        $club=Club::findOrFail($nomClub);
        $club->update($request->all());
        return $club;
    }

    public function isMainClub(){
        $clubIsMain=DB::table('clubs')->whereNotNull('isMain')->count();
        return $clubIsMain;
    }

    public function monClub(){
        $monClub=DB::table('clubs')->where('isMain','1')->get();
        return $monClub;
    }

    public function newTactique(Request $request){
        $donnees=$request->all();
        $formation=$donnees['formation'];
        $joueurs=$donnees['joueurs'];
        $idJoueurs=DB::select('SELECT joueurs.id FROM joueurs,clubs,contrats WHERE joueurs.id=contrats.idJoueur AND contrats.idClub=clubs.id AND clubs.isMain="1"');
        foreach ($idJoueurs as $idJoueur) {
            DB::update('update joueurs set joue="0" where id='.$idJoueur->id);
        }
        switch ($formation){
            case "[1,1,2]":
                DB::update('update joueurs set joue="Attaquant" where id='.$joueurs[0]);
                DB::update('update joueurs set joue="Attaquant" where id='.$joueurs[1]);
                DB::update('update joueurs set joue="Milieu" where id='.$joueurs[2]);
                DB::update('update joueurs set joue="Defenseur" where id='.$joueurs[3]);
                DB::update('update joueurs set joue="Gardien" where id='.$joueurs[4]);
                break;
            case "[1,2,1]":
                DB::update('update joueurs set joue="Attaquant" where id='.$joueurs[0]);
                DB::update('update joueurs set joue="Milieu" where id='.$joueurs[1]);
                DB::update('update joueurs set joue="Milieu" where id='.$joueurs[2]);
                DB::update('update joueurs set joue="Defenseur" where id='.$joueurs[3]);
                DB::update('update joueurs set joue="Gardien" where id='.$joueurs[4]);
                break;
            case "[2,1,1]":
                DB::update('update joueurs set joue="Attaquant" where id='.$joueurs[0]);
                DB::update('update joueurs set joue="Milieu" where id='.$joueurs[1]);
                DB::update('update joueurs set joue="Defenseur" where id='.$joueurs[2]);
                DB::update('update joueurs set joue="Defenseur" where id='.$joueurs[3]);
                DB::update('update joueurs set joue="Gardien" where id='.$joueurs[4]);
                break;
        }
        DB::update('update clubs set formation="'.$formation.'" where isMain="1"');
        $idJoueurs=DB::select('SELECT joueurs.id FROM joueurs,clubs,contrats WHERE joueurs.id=contrats.idJoueur AND contrats.idClub=clubs.id AND clubs.isMain="1" and joueurs.joue="0"');
        foreach ($idJoueurs as $idJoueur) {
            DB::update('update joueurs set joue="Banc" where id='.$idJoueur->id);
        }
        $noteAttaque=0;
        $nbAttaquant=0;
        $nbDefenseur=0;
        $nbMilieu=0;
        $noteDefense=0;
        $noteMilieu=0;
        $joueurs=DB::select('SELECT joueurs.* FROM joueurs,clubs,contrats WHERE joueurs.id=contrats.idJoueur AND contrats.idClub=clubs.id AND clubs.isMain="1" and joueurs.joue!="0" and joueurs.joue!="Banc"');
        foreach ($joueurs as $joueur){
            if($joueur->joue=="Attaquant"){
                $nbAttaquant++;
                $noteAttaque+=$joueur->noteAttaque;
            }
            if($joueur->joue=="Milieu"){
                $nbMilieu++;
                $noteMilieu+=$joueur->noteMilieu;
            }
            if($joueur->joue=="Defenseur"){
                $nbDefenseur++;
                $noteDefense+=$joueur->noteDefense;
            }
            if($joueur->joue=="Gardien"){
                $nbDefenseur++;
                $noteDefense+=$joueur->noteGardien;
            }
        }
        $noteDefense/=$nbDefenseur;
        $noteAttaque/=$nbAttaquant;
        $noteFormation=($noteDefense+$noteMilieu+$noteAttaque)/3;
        DB::table('clubs')->where('isMain','1')->update(['noteFormation' => $noteFormation,'noteAttaque'=>$noteAttaque,'noteDefense'=>$noteDefense]);
    }
}

define ('VILLE',array('Ajaccio','Bastia','Borgo','Bastellica','Vizzavona','Vico','Venaco',
    'Vivario','Peri','Tavera','Tasso','Biguglia','Porreta','Baleone','Lumio','Calvi','Ile Rousse','Ponte-Novu','Ponte-Leccia','Montegrosso','Zilia','Calenzana','Monticello','Lavatoggio'));

define ('CLUB',array('SC','SEC','AC','FC','Real','O','AS','Rayo','CF','GFC','EDF','GDF','AXA','Inter','FFF','ASS','AU','CCU','CNU','BNP'));

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
            $stade->capacite = rand(1000,10000);
            $stade->save();
        }
        //Club
        $nomClub = rand(0,count($array_nomClub)-1);
        $nomClub = $array_nomClub[$nomClub];
        unset($array_nomClub[$nomClub]);
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
        $forme=100;
        $noteGardien=($placement+$arret+$arret+$tacle)/4;
        $noteDefense=($placement+$tacle+$passe+$tacle)/4;
        $noteMilieu=($placement+$passe+$technique+$vitesse)/4;
        $noteAttaque=($tir+$tir+$vitesse+$technique)/4;

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
        $joueur->postePredilection=creerPoste();
        $joueur->noteGlobale=($tir+$passe+$technique+$placement+$vitesse+$tacle+$arret+$endurance)/8;
        $joueur->noteInstantanee=($tir+$passe+$technique+$placement+$vitesse+$tacle+$arret+$endurance)/8;
        $newPrix=0;
        switch ($joueur->postePredilection){
            case "Attaquant":
                $newPrix=(($joueur->noteGlobale+$joueur->noteAttaque)/2)*100;
                break;
            case "Milieu":
                $newPrix=(($joueur->noteGlobale+$joueur->noteMilieu)/2)*100;
                break;
            case "Defenseur":
                $newPrix=(($joueur->noteGlobale+$joueur->noteDefenseur)/2)*100;
                break;
            case "Gardien":
                $newPrix=(($joueur->noteGlobale+$joueur->noteGardien)/2)*100;
                break;
        }
        $joueur->prix=$newPrix;
        $joueur->save();
    }
}

function lierClubJoueur(){
    $clubs = DB::select('select * from clubs');
    foreach ($clubs as $club){
        $joueurGardien=DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->where('postePredilection','Gardien')->whereNull('contrats.idJoueur')->inRandomOrder()->first();
        $contrat = new Contrat();
        $contrat->idClub = $club->id;
        $contrat->idJoueur = $joueurGardien->id;
        $contrat->dureeAnneesContrat = rand(1,5);
        $contrat->salaire = 20;
        DB::table('clubs')->where('id', $club->id)->decrement('budget', 20);
        DB::table('joueurs')->where('id', $joueurGardien->id)->update(['joue' => 'Gardien']);
        $contrat->save();
        $joueurDefenseur=DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->where('postePredilection','Defenseur')->whereNull('contrats.idJoueur')->inRandomOrder()->first();
        $contrat = new Contrat();
        $contrat->idClub = $club->id;
        $contrat->idJoueur = $joueurDefenseur->id;
        $contrat->dureeAnneesContrat = rand(1,5);
        $contrat->salaire = 20;
        DB::table('clubs')->where('id', $club->id)->decrement('budget', 20);
        DB::table('joueurs')->where('id', $joueurDefenseur->id)->update(['joue' => 'Defenseur']);
        $contrat->save();
        for($i= 0; $i<2 ; $i++) {
            $joueurMilieu=DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->where('postePredilection','Milieu')->whereNull('contrats.idJoueur')->inRandomOrder()->first();
            $contrat = new Contrat();
            $contrat->idClub = $club->id;
            $contrat->idJoueur = $joueurMilieu->id;
            $contrat->dureeAnneesContrat = rand(1,5);
            $contrat->salaire = 20;
            DB::table('clubs')->where('id', $club->id)->decrement('budget', 20);
            DB::table('joueurs')->where('id', $joueurMilieu->id)->update(['joue' => 'Milieu']);
            $contrat->save();
        }
        $joueurAttaquant=DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->where('postePredilection','Attaquant')->whereNull('contrats.idJoueur')->inRandomOrder()->first();
        $contrat = new Contrat();
        $contrat->idClub = $club->id;
        $contrat->idJoueur = $joueurAttaquant->id;
        $contrat->dureeAnneesContrat = rand(1,5);
        $contrat->salaire = 20;
        DB::table('clubs')->where('id', $club->id)->decrement('budget', 20);
        DB::table('joueurs')->where('id', $joueurAttaquant->id)->update(['joue' => 'Attaquant']);
        $contrat->save();

        $noteAttaque=0;
        $nbAttaquant=0;
        $nbDefenseur=0;
        $nbMilieu=0;
        $noteDefense=0;
        $noteMilieu=0;
        $joueurs=DB::select('SELECT joueurs.* FROM joueurs,clubs,contrats WHERE joueurs.id=contrats.idJoueur AND contrats.idClub=clubs.id AND joueurs.joue!="0" and joueurs.joue!="Banc"');
        foreach ($joueurs as $joueur){
            if($joueur->joue=="Attaquant"){
                $nbAttaquant++;
                $noteAttaque+=$joueur->noteAttaque;
            }
            if($joueur->joue=="Milieu"){
                $nbMilieu++;
                $noteMilieu+=$joueur->noteMilieu;
            }
            if($joueur->joue=="Defenseur"){
                $nbDefenseur++;
                $noteDefense+=$joueur->noteDefense;
            }
            if($joueur->joue=="Gardien"){
                $nbDefenseur++;
                $noteDefense+=$joueur->noteGardien;
            }
        }
        $noteDefense/=$nbDefenseur;
        $noteAttaque/=$nbAttaquant;
        $noteMilieu/=$nbMilieu;
        $noteFormation=($noteDefense+$noteMilieu+$noteAttaque)/3;
        DB::table('clubs')->where('id', $club->id)->update(['noteFormation' => $noteFormation,'noteAttaque'=>$noteAttaque,'noteDefense'=>$noteDefense]);
        $note=0;
        $i=0;
        $notesJoueur=DB::select('select noteGlobale from joueurs, contrats where joueurs.id=contrats.idJoueur and contrats.idClub='.$club->id);
        foreach ($notesJoueur as $noteJoueur){
            $note+=$noteJoueur->noteGlobale;
            $i++;
        }
        $note=$note/$i;
        DB::table('clubs')->where('id', $club->id)->update(['noteAbsolue' => $note, 'noteInstantannee' => $note]);
    }
}

function creerPoste(){
    $nb=rand(0,3);
    switch ($nb){
        case 0:
            $poste = new Gardien();
            break;
        case 1:
            $poste = new Defenseur();
            break;
        case 2:
            $poste = new Milieu();
            break;
        case 3:
            $poste = new Attaquant();
            break;
    }
    return $poste->getBonusPoste();
}

abstract class PostePref{
    abstract public function getBonusPoste();
}

class Gardien extends PostePref{

    public function getBonusPoste()
    {
        return "Gardien";
    }
}
class Defenseur extends PostePref{

    public function getBonusPoste()
    {
        return "Defenseur";
    }
}
class Milieu extends PostePref{

    public function getBonusPoste()
    {
        return "Milieu";
    }
}
class Attaquant extends PostePref{

    public function getBonusPoste()
    {
        return "Attaquant";
    }
}

