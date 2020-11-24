<?php

namespace App\Http\Controllers;

use App\Contrat;
use App\Joueur;
use App\Match;
use App\ClubDomicile;
use App\ClubExterieur;
use App\Competition;
use Illuminate\Http\Request;
use App\Classement;
use DB;

class CompetitionController extends Controller
{
    public function init(){
        DB::table('classements')->delete();
        DB::table('club_domiciles')->delete();
        DB::table('club_exterieurs')->delete();
        DB::table('matches')->delete();
        DB::table('competitions')->delete();
        $competition=new Competition();
        $competition->nomCompetition="M1DFS Ligue One";
        $competition->statut="en cours";
        $competition->save();
        $clubs = DB::select('select id from clubs');
        $competition = DB::select('select id from competitions where nomCompetition="M1DFS Ligue One"');
        foreach ($clubs as $club){
            $classement=new Classement();
            $classement->idCompetition=$competition[0]->id;
            $classement->idClub=$club->id;
            $classement->save();
        }
        genererMatchs($competition[0]->id);
    }

    public function get(){
        if(DB::table('competitions')->select('id')->exists()) {
            $competMax=DB::table('competitions')->max('id');
            $competition = DB::table('competitions')->select('nomCompetition', 'statut')->where('id', $competMax)->get();
            $compet = array("id" => $competMax, "nomCompetition" => $competition[0]->nomCompetition, "statut" => $competition[0]->statut);
            return $compet;
        }
    }

    public function finCompet(){
        $competMax=DB::table('competitions')->max('id');
        $competition=DB::table('competitions')->select('nomCompetition','statut')->where('id',$competMax)->get();
        if($competition[0]->statut=="en cours"){
            DB::table('competitions')->where('id',$competMax)->update(['statut'=>"fini"]);
            $classement=DB::select('select * from classements where classements.idCompetition='.$competMax.' order by classements.points DESC');
            $premier=$classement[0]->idClub;
            $milieux=array($classement[1]->idClub,$classement[2]->idClub,$classement[3]->idClub,$classement[4]->idClub);
            DB::table('clubs')->where('id',$premier)->increment('reputation',2);
            for($i=0;$i<sizeof($milieux);$i++){
                DB::table('clubs')->where('id',$milieux[$i])->increment('reputation',1);
            }
            nextYear();
        }
    }

    public function saisonSuivante(){
        remplirClub();
        $competition=new Competition();
        $competition->nomCompetition="M1DFS Ligue One";
        $competition->statut="en cours";
        $competition->save();
        $clubs = DB::select('select id from clubs');
        $competition = DB::select('select max(id) as idMax from competitions where nomCompetition="M1DFS Ligue One"');
        $id = $competition[0]->idMax;
        foreach ($clubs as $club){
            $classement=new Classement();
            $classement->idCompetition=$id;
            $classement->idClub=$club->id;
            $classement->save();
        }
        genererMatchs($id);
        $noteAttaque=0;
        $nbAttaquant=0;
        $nbDefenseur=0;
        $nbMilieu=0;
        $noteDefense=0;
        $noteMilieu=0;
        $joueurs=DB::select('SELECT joueurs.* FROM joueurs,clubs,contrats WHERE joueurs.id=contrats.idJoueur AND contrats.idClub=clubs.id AND joueurs.joue!="0" and joueurs.joue!="Banc" and clubs.isMain="1"');
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
        DB::table('clubs')->where('isMain', "1")->update(['noteFormation' => $noteFormation],['noteAttaque'=>$noteAttaque],['noteDefense'=>$noteDefense]);
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

define('PRENOM',array('Adan','Adrien','Alain','Alex','Alexandre','Antoine','Antony','Bastien','Bilal','Brian','Brice','Benjamin','Ben','Charles','Carles','Cedric','Damien','David','Dave','Kevin','Quentin','Fabien','Francois','Fred','Frederic','Goerge','Gerard','Gauthier','Greg','Gregory','Gregoire','Jean','Kayl','Louis','Loris','Michel','Morgan','Mathieu','Mataé','Nicolas','Paul','Philippe','Pierre','Jacques','Quentin','Rachid','Amed','Hamed','Luke','Han','Ken','Shiryu','Luffy','Zoro','Sanji','Chopper','Gabriel','Seraphin','Raphael','Ralph','Stephane','Serguei','Tangui','Francisco','Daniel','Robin'));

define('NOM',array('Casanova','Mancini','Mecev','Luciani','Mariani','Michelozzi','Agostini','Martinez','Morazzani','Colonna','Mattei','Muntoni','Paoli','Mondoloni','Nicolas','Bartoli','Natalini','Paolini','Rossi','Orsini','Paris','Albertini','Orsoni','Peres','Filippi','Palazzo','Perez','Cesari','Serra','Pieri','Moreau','Antoniotti','Pietri','Poggi','Appietto','Platel','Angeli','Battesti','Popo','Carlotti','Bertrand','Renucci','Graziani','Costa','Rousier','Leca','Cucchi','Roussel','Marcaggi','Fancello','Santer','Nicolai','Maroselli','Santucci','Santoni','Micheli','Scarbonchi','Thomas','Michel','Simon','Cardi','Moracchini','Tamburini','Acquaviva','Ottavy','Tasso','Andreani','Peretti','Vincenti','Astolfi','Pinheiro','Werle','Castelli','Paoli','Angelini','Cipriani','Raffalli','Agnelot','Garcia','Sauli','Arrighi','Grimaldi','Serreri','Barbier','Guidoni','Torre','Bazzali','Lucchini','Martini','Belingard'));

function buildJoueur($nbr,$array_prenom=PRENOM,$array_nom=NOM){
    for ($i=0; $i < $nbr; $i++) {
        # NOM
        $nom = rand(0,count($array_nom)-1);
        $nom = $array_nom[$nom];
        # PRENOM
        $prenom = rand(0,count($array_prenom)-1);
        $prenom = $array_prenom[$prenom];
        # AGE
        $age = 17;
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

function nextYear(){
    DB::table('joueurs')->increment('age');
    $joueurs=DB::table('joueurs')->get();
    foreach ($joueurs as $joueur){
        if(DB::table('contrats')->where('idJoueur',$joueur->id)->exists()){
            DB::table('contrats')->where('idJoueur',$joueur->id)->decrement('dureeAnneesContrat');
            $dureeAnneesContrat=DB::table('contrats')->select('dureeAnneesContrat')->where('idJoueur',$joueur->id)->get();
            if($dureeAnneesContrat[0]->dureeAnneesContrat<=0){
                DB::table('contrats')->where('idJoueur',$joueur->id)->delete();
                DB::table('joueurs')->where('id',$joueur->id)->update(['joue'=>'0']);
            }
        }
        if($joueur->age > 38){
            DB::table('contrats')->where('idJoueur',$joueur->id)->delete();
            DB::table('joueurs')->where('id',$joueur->id)->delete();
        }
        if ($joueur->age > 30)
        {
            DB::table('joueurs')->where('id',$joueur->id)->decrement('endurance',10);
        }
        elseif ($joueur->age > 27)
        {
            DB::table('joueurs')->where('id',$joueur->id)->decrement('endurance',2);
        }
        else {
            DB::table('joueurs')->where('id',$joueur->id)->increment('endurance',2);
        }
        /*if(DB::table('contrats')->where('idJoueur',$joueur->id)->exists()){
            $contrat=DB::table('contrats')->select('dureeAnneesContrat','salaire')->where('idJoueur',$joueur->id)->get();
            $newPrix=0;
            switch ($joueur->postePredilection){
                case "Attaquant":
                    $newPrix=(($joueur->noteGlobale+$joueur->noteAttaque)/2+$contrat[0]->dureeAnneesContrat+$contrat[0]->salaire)*100;
                    break;
                case "Milieu":
                    $newPrix=(($joueur->noteGlobale+$joueur->noteMilieu)/2+$contrat[0]->dureeAnneesContrat+$contrat[0]->salaire)*100;
                    break;
                case "Defenseur":
                    $newPrix=(($joueur->noteGlobale+$joueur->noteDefenseur)/2+$contrat[0]->dureeAnneesContrat+$contrat[0]->salaire)*100;
                    break;
                case "Gardien":
                    $newPrix=(($joueur->noteGlobale+$joueur->noteGardien)+$contrat[0]->dureeAnneesContrat+$contrat[0]->salaire)*100;
                    break;
            }
            DB::table('joueurs')->where('id',$joueur->id)->update(['prix' => $newPrix]);
        }else {
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
            DB::table('joueurs')->where('id', $joueur->id)->update(['prix' => $newPrix]);
        }*/
    }
    $nbJoueurs=DB::select('select count(id) as nbJoueur from joueurs');
    $nbJoueurs=200-$nbJoueurs[0]->nbJoueur;
    buildJoueur($nbJoueurs);
}

function genererMatchs($idCompetition){
    $arrayTeam = DB::table('clubs')->get();
    $nbEquipe = count($arrayTeam);
    $nbDay = '';
    if ($nbEquipe % 2 != 0){
        $nbDay = $nbEquipe;
    }else{
        $nbDay = $nbEquipe - 1;
    }
    $row1 = array();
    $row2 = array();
    for($i=0; $i<floor($nbEquipe/2); $i++){
        array_push($row1, $arrayTeam[$i]->id);
    }
    for($i=$nbEquipe/2; $i<$nbEquipe; $i++){
        array_push($row2, $arrayTeam[$i]->id);
    }
    $poolCount = count($row1);
    $listMatch = array();
    for($i=0; $i<$nbDay; $i++){
        for($j=0; $j<$poolCount; $j++){
            array_push($listMatch, array($row1[$j],$row2[$j]));
        }
        if($nbEquipe % 2 != 0){
            array_push($listMatch, array($row2[$poolCount], '∅'));
        }
        array_push($row2, array_pop($row1));
        array_splice($row1, 1, 0, array_shift($row2));
    }
    $nbMatch = count($listMatch);
    $jour = 0;
    //echo("<table><tr><td>MATCH ALLER</td></tr>");
    for($i=0; $i<$nbMatch; $i++) {
        $clubDomicile = new ClubDomicile();
        $clubExterieur = new ClubExterieur();
        $clubDomicile->idClub = $listMatch[$i][0];
        $clubExterieur->idClub = $listMatch[$i][1];
        $match = new Match();
        $match->idCompetition = $idCompetition;
        $recette = DB::select("select capacite from stades inner join clubs on stades.nomStade=clubs.nomStade where clubs.id=" . $clubDomicile->idClub);
        $reputationDomicile = DB::table('clubs')->select('reputation')->where('id', $clubDomicile->idClub)->get();
        $reputationExterieur = DB::table('clubs')->select('reputation')->where('id', $clubExterieur->idClub)->get();
        $recetteFinal = ($recette[0]->capacite) + $reputationDomicile[0]->reputation + $reputationExterieur[0]->reputation;
        $match->recette = $recetteFinal;
        if ($i % ($nbEquipe / 2) == 0) {
            //echo("<tr><td>Jour " . $jour . "</td>");
            $jour++;
        }
        $match->date = $jour;
        $match->save();
        $idMatch=DB::select('SELECT id FROM matches ORDER BY id DESC LIMIT 1');
        $clubDomicile->idMatch=$idMatch[0]->id;
        $clubExterieur->idMatch=$idMatch[0]->id;
        $clubDomicile->save();
        $clubExterieur->save();
        //echo("<td>" . $match[$i][0] . " vs " . $match[$i][1] . "</td>");
    }
    //echo("<tr><td>MATCH RETOUR</td></tr>");
    for($i=0; $i<$nbMatch; $i++){
        $clubDomicile=new ClubDomicile();
        $clubExterieur=new ClubExterieur();
        $clubDomicile->idClub=$listMatch[$i][1];
        $clubExterieur->idClub=$listMatch[$i][0];
        $match=new Match();
        $match->idCompetition=$idCompetition;
        $recette=DB::select("select capacite from stades inner join clubs on stades.nomStade=clubs.nomStade where clubs.id=".$clubDomicile->idClub);
        $reputationDomicile=DB::table('clubs')->select('reputation')->where('id',$clubDomicile->idClub)->get();
        $reputationExterieur=DB::table('clubs')->select('reputation')->where('id',$clubExterieur->idClub)->get();
        $recetteFinal=($recette[0]->capacite)+$reputationDomicile[0]->reputation+$reputationExterieur[0]->reputation;
        $match->recette=$recetteFinal;
        if($i % ($nbEquipe/2) == 0){
            //echo("<tr><td>Jour " . $jour . "</td>");
            $jour++;
        }
        $match->date = $jour;
        $match->save();
        $idMatch=DB::select('SELECT id FROM matches ORDER BY id DESC LIMIT 1');
        $clubDomicile->idMatch=$idMatch[0]->id;
        $clubExterieur->idMatch=$idMatch[0]->id;
        $clubDomicile->save();
        $clubExterieur->save();
        //echo("<td>" . $match[$i][1] . " vs " . $match[$i][0] . "</td>");
    }
    //echo("</table>");
}

function remplirClub(){
    $clubs = DB::table('clubs')->select('id')->whereNull('isMain')->get();
    foreach ($clubs as $club) {
        $nbJoueurClub=DB::select('select count(id) as nbJoueurs from joueurs,contrats where joueurs.id=contrats.idJoueur and contrats.idClub='.$club->id);
        $nbJoueurs=$nbJoueurClub[0]->nbJoueurs;
        if($nbJoueurs<5) {
            $nbJoueursAttaquant = DB::select('select count(id) as nbJoueurs from joueurs,contrats where joueurs.id=contrats.idJoueur and joueurs.postePredilection="Attaquant" and contrats.idClub=' . $club->id);
            $nbJoueursAttaquant = $nbJoueursAttaquant[0]->nbJoueurs;
            $nbJoueursDefenseur = DB::select('select count(id) as nbJoueurs from joueurs,contrats where joueurs.id=contrats.idJoueur and joueurs.postePredilection="Defenseur" and contrats.idClub=' . $club->id);
            $nbJoueursDefenseur = $nbJoueursDefenseur[0]->nbJoueurs;
            $nbJoueursMilieu = DB::select('select count(id) as nbJoueurs from joueurs,contrats where joueurs.id=contrats.idJoueur and joueurs.postePredilection="Milieu" and contrats.idClub=' . $club->id);
            $nbJoueursMilieu = $nbJoueursMilieu[0]->nbJoueurs;
            $nbJoueursGardien = DB::select('select count(id) as nbJoueurs from joueurs,contrats where joueurs.id=contrats.idJoueur and joueurs.postePredilection="Gardien" and contrats.idClub=' . $club->id);
            $nbJoueursGardien = $nbJoueursGardien[0]->nbJoueurs;
            if($nbJoueursAttaquant<1) {
                $joueur = DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->where('joueurs.postePredilection', 'Attaquant')->whereNull('contrats.idJoueur')->inRandomOrder()->first();
                $contrat = new Contrat();
                $contrat->idClub = $club->id;
                $contrat->idJoueur = $joueur->id;
                $contrat->dureeAnneesContrat = rand(1,5);
                $contrat->salaire = $joueur->prix;
                DB::table('clubs')->where('id', $club->id)->decrement('budget', $contrat->salaire);
                DB::table('joueurs')->where('id', $joueur->id)->update(['joue' => 'Attaquant']);
                $contrat->save();
            }
            if($nbJoueursMilieu<2) {
                for ($i = 0; $i < (2 - $nbJoueursMilieu); $i++){
                    $joueur = DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->where('joueurs.postePredilection', 'Milieu')->whereNull('contrats.idJoueur')->inRandomOrder()->first();
                    $contrat = new Contrat();
                    $contrat->idClub = $club->id;
                    $contrat->idJoueur = $joueur->id;
                    $contrat->dureeAnneesContrat = rand(1,5);
                    $contrat->salaire = $joueur->prix;
                    DB::table('clubs')->where('id', $club->id)->decrement('budget', $contrat->salaire);
                    DB::table('joueurs')->where('id', $joueur->id)->update(['joue' => 'Milieu']);
                    $contrat->save();
                }
            }
            if($nbJoueursDefenseur<1) {
                $joueur = DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->where('joueurs.postePredilection', 'Defenseur')->whereNull('contrats.idJoueur')->inRandomOrder()->first();
                $contrat = new Contrat();
                $contrat->idClub = $club->id;
                $contrat->idJoueur = $joueur->id;
                $contrat->dureeAnneesContrat = rand(1,5);
                $contrat->salaire = $joueur->prix;
                DB::table('clubs')->where('id', $club->id)->decrement('budget', $contrat->salaire);
                DB::table('joueurs')->where('id', $joueur->id)->update(['joue' => 'Defenseur']);
                $contrat->save();
            }
            if($nbJoueursGardien<1) {
                $joueur = DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->where('joueurs.postePredilection', 'Gardien')->whereNull('contrats.idJoueur')->inRandomOrder()->first();
                $contrat = new Contrat();
                $contrat->idClub = $club->id;
                $contrat->idJoueur = $joueur->id;
                $contrat->dureeAnneesContrat = rand(1,5);
                $contrat->salaire = $joueur->prix;
                DB::table('clubs')->where('id', $club->id)->decrement('budget', $contrat->salaire);
                DB::table('joueurs')->where('id', $joueur->id)->update(['joue' => 'Gardien']);
                $contrat->save();
            }
            $noteAttaque = 0;
            $nbAttaquant = 0;
            $nbDefenseur = 0;
            $nbMilieu = 0;
            $noteDefense = 0;
            $noteMilieu = 0;
            $joueurs = DB::select('SELECT joueurs.* FROM joueurs,contrats WHERE joueurs.id=contrats.idJoueur AND contrats.idClub='.$club->id.' AND joueurs.joue!="0" and joueurs.joue!="Banc"');
            foreach ($joueurs as $joueur) {
                if ($joueur->joue == "Attaquant") {
                    $nbAttaquant++;
                    $noteAttaque += $joueur->noteAttaque;
                }
                if ($joueur->joue == "Milieu") {
                    $nbMilieu++;
                    $noteMilieu += $joueur->noteMilieu;
                }
                if ($joueur->joue == "Defenseur") {
                    $nbDefenseur++;
                    $noteDefense += $joueur->noteDefense;
                }
                if ($joueur->joue == "Gardien") {
                    $nbDefenseur++;
                    $noteDefense += $joueur->noteGardien;
                }
            }
            $noteDefense /= $nbDefenseur;
            $noteAttaque /= $nbAttaquant;
            $noteMilieu /= $nbMilieu;
            $noteFormation = ($noteDefense + $noteMilieu + $noteAttaque) / 3;
            DB::table('clubs')->where('id', $club->id)->update(['noteFormation' => $noteFormation], ['noteAttaque' => $noteAttaque], ['noteDefense' => $noteDefense]);
        }
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
