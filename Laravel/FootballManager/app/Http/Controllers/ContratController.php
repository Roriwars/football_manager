<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Contrat;
use DB;

class ContratController extends Controller
{
    public function getJoueursAvecContrat($idClub){
        $listJoueur = DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->where('contrats.idClub',$idClub)->get();
        return $listJoueur;
    }

    public function getJoueursSansContrat(){
        $listJoueur = DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->whereNull('contrats.idJoueur')->get();
        return $listJoueur;
    }

    public function lierJoueurContrat(Request $request){
        $result = $request->all();
        $idClub=$result['idClub'];
        $prixJoueur=$result['prixJoueur'];
        $dureeContrat=$result['dureeContrat'];
        DB::table('contrats')->insert(
            ['idJoueur' => $result['idJoueur'], 'idClub' => $idClub,'dureeAnneesContrat' => $dureeContrat, 'salaire'=> $prixJoueur]
        );
        $note=0;
        $i=0;
        if(DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->where('contrats.idClub',$idClub)->exists()) {
            $notesJoueur=DB::select('select noteGlobale from joueurs, contrats where joueurs.id=contrats.idJoueur and contrats.idClub='.$idClub);
            foreach ($notesJoueur as $noteJoueur){
                $note+=$noteJoueur->noteGlobale;
                $i++;
            }
            $note=$note/$i;
        }
        DB::table('clubs')->where('id', $idClub)->update(['noteAbsolue' => $note, 'noteInstantannee' => $note]);
        DB::table('clubs')->where('id', $idClub)->decrement('budget', $prixJoueur);
        DB::table('joueurs')->where('id', $result['idJoueur'])->update(['joue' => 'Banc']);
    }

    public function vendreJoueur(Request $request){
        $result = $request->all();
        $idClub=$result['idClub'];
        $prixJoueur=$result['prixJoueur'];
        $idJoueur=$result['idJoueur'];
        DB::table('contrats')->where('idJoueur', $idJoueur)->delete();
        DB::table('joueurs')->where('id', $result['idJoueur'])->update(['joue' => '0']);
        $noteAttaque=0;
        $nbAttaquant=0;
        $nbDefenseur=0;
        $nbMilieu=0;
        $noteDefense=0;
        $noteMilieu=0;
        if(DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->where([['contrats.idClub',$idClub],['joue', '!=' ,'Banc']])->exists()) {
            $joueurs = DB::select('SELECT joueurs.* FROM joueurs,clubs,contrats WHERE joueurs.id=contrats.idJoueur AND contrats.idClub=clubs.id AND joueurs.joue!="0" and joueurs.joue!="Banc" and clubs.isMain="1"');
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
            if($nbDefenseur!=0) {
                $noteDefense /= $nbDefenseur;
            }
            if($nbAttaquant!=0) {
                $noteAttaque /= $nbAttaquant;
            }
            if($nbMilieu!=0) {
                $noteMilieu /= $nbMilieu;
            }
        }
        $noteFormation=($noteDefense+$noteMilieu+$noteAttaque)/3;
        DB::table('clubs')->where('id', $idClub)->update(['noteFormation' => $noteFormation],['noteAttaque'=>$noteAttaque],['noteDefense'=>$noteDefense]);
        $note=0;
        $i=0;
        if(DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->where('contrats.idClub',$idClub)->exists()) {
            $notesJoueur=DB::select('select noteGlobale from joueurs, contrats where joueurs.id=contrats.idJoueur and contrats.idClub='.$idClub);
            foreach ($notesJoueur as $noteJoueur){
                $note+=$noteJoueur->noteGlobale;
                $i++;
            }
            $note=$note/$i;
        }
        DB::table('clubs')->where('id', $idClub)->update(['noteAbsolue' => $note, 'noteInstantannee' => $note]);
        DB::table('clubs')->where('id', $idClub)->increment('budget', $prixJoueur);

    }

    public function remplirClubJoueur(){
        $clubs = DB::table('clubs')->select('id')->whereNull('isMain')->get();
        foreach ($clubs as $club) {
            for ($i = 0; $i < 3; $i++){
                $joueur = DB::table('joueurs')->leftJoin('contrats', 'joueurs.id', '=', 'contrats.idJoueur')->whereNull('contrats.idJoueur')->inRandomOrder()->first();
                $contrat = new Contrat();
                $contrat->idClub = $club->id;
                $contrat->idJoueur = $joueur->id;
                $contrat->dureeAnneesContrat = 3;
                $contrat->salaire = 20;
                DB::table('clubs')->where('id', $club->id)->decrement('budget', 20);
                DB::table('joueurs')->where('id', $joueur->id)->update(['joue' => 'Banc']);
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
                DB::table('clubs')->where('id', $club->id)->update(['noteFormation' => $noteFormation],['noteAttaque'=>$noteAttaque],['noteDefense'=>$noteDefense]);
            }
        }
    }
}
