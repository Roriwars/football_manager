<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class MatchController extends Controller
{
    public function getMatches(Request $request){
        $request=$request->all();
        $idCompet=$request['idCompetition'];
        $jour=$request['jour'];
        $matchs = DB::table('club_exterieurs')
            ->join('matches', 'club_exterieurs.idMatch', '=', 'matches.id')
            ->join('clubs', 'club_exterieurs.idClub', '=', 'clubs.id')
            ->select('matches.*', 'clubs.id as idClubExterieur', 'clubs.nomClub as nomClubExterieur', 'club_exterieurs.nbBut as nbButExterieur')
            ->where([['matches.idCompetition',$idCompet],['matches.date',$jour]])
            ->orderBy('matches.id')
            ->get();
        $clubDomicile = DB::table('club_domiciles')
            ->join('matches', 'club_domiciles.idMatch', '=', 'matches.id')
            ->join('clubs', 'club_domiciles.idClub', '=', 'clubs.id')
            ->select('clubs.id as idClubDomicile', 'clubs.nomClub as nomClubDomicile', 'club_domiciles.nbBut as nbButDomicile', 'clubs.nomStade as stadeDomicile')
            ->where([['matches.idCompetition',$idCompet],['matches.date',$jour]])
            ->orderBy('matches.id')
            ->get();
        $i=0;
        foreach ($matchs as $match){
            $match->nomClubDomicile=$clubDomicile[$i]->nomClubDomicile;
            $match->nbButDomicile=$clubDomicile[$i]->nbButDomicile;
            $match->idClubDomicile=$clubDomicile[$i]->idClubDomicile;
            $match->stadeDomicile=$clubDomicile[$i]->stadeDomicile;
            $i++;
        }
        $mesJoueurs=DB::table('joueurs')
            ->leftJoin('contrats','joueurs.id','=','contrats.idJoueur')
            ->join('clubs', 'contrats.idClub', '=', 'clubs.id')
            ->where('clubs.isMain','1')
            ->select('joueurs.*','contrats.dureeAnneesContrat')
            ->get();
        $monClub=DB::table('clubs')->where('isMain','1')->get();
        $classement=DB::select('select clubs.nomClub,classements.* from clubs,classements where clubs.id=classements.idClub and classements.idCompetition='.$idCompet.' order by classements.points DESC');
        return array($matchs,$monClub,$mesJoueurs,$classement);
    }

    public function getJour($idCompet){
        $journee = DB::table('matches')->orderBy('date')->select('date as jour','quartTemps')->where([['quartTemps','<',4],['idCompetition',$idCompet]])->limit(1)->get();
        $journeeMax = DB::table('matches')->where([['quartTemps','<',4],['idCompetition',$idCompet]])->max('date');
        if(DB::table('matches')->orderBy('date')->select('date as jour','quartTemps')->where([['quartTemps','<',4],['idCompetition',$idCompet]])->doesntExist()){
            return 'vide';
        }else{
            return array("jour"=>$journee[0]->jour,"jourMax"=>$journeeMax,"quartTemps"=>$journee[0]->quartTemps);
        }
    }

    public function jouerQuartTemps(Request $request){
        $result=$request->all();
        $idCompet=$result['idCompetition'];
        $jour=$result['jour'];
        $matches=DB::select('select * from matches where idCompetition='.$idCompet.' and date='.$jour);
        foreach ($matches as $match){
            $resultat=0;
            $clubDomicile=DB::select('select clubs.* from clubs,club_domiciles where clubs.id=club_domiciles.idClub and club_domiciles.idMatch='.$match->id);
            $clubExterieur=DB::select('select clubs.* from clubs,club_exterieurs where clubs.id=club_exterieurs.idClub and club_exterieurs.idMatch='.$match->id);
            $noteDomicile=($clubDomicile[0]->noteInstantannee+$clubDomicile[0]->noteFormation)/2;
            $noteExterieur=($clubExterieur[0]->noteInstantannee+$clubExterieur[0]->noteFormation)/2;
            $noteDomicile=rand($noteDomicile-100,$noteDomicile+10);
            $noteExterieur=rand($noteExterieur-150,$noteExterieur+50);
            if($noteExterieur===$noteDomicile){
                $resultat+=0;
            }elseif($noteDomicile>$noteExterieur){
                $resultat+=1;
            }else{
                $resultat-=1;
            }
            score($resultat,$match->id,$clubDomicile[0]->id,$clubExterieur[0]->id);
            fatigue($match->quartTemps,$clubDomicile[0]->id);
            fatigue($match->quartTemps,$clubExterieur[0]->id);
        }
        DB::table('matches')->where([['idCompetition',$idCompet],['date',$jour]])->increment('quartTemps');
    }

    public function jouerMatch(Request $request){
        $result=$request->all();
        $idCompet=$result['idCompetition'];
        $jour=$result['jour'];
        $matches=DB::select('select * from matches where idCompetition='.$idCompet.' and date='.$jour);
        foreach ($matches as $match){
            for($i=0;$i<4;$i++){
                $resultat=0;
                $clubDomicile=DB::select('select clubs.* from clubs,club_domiciles where clubs.id=club_domiciles.idClub and club_domiciles.idMatch='.$match->id);
                $clubExterieur=DB::select('select clubs.* from clubs,club_exterieurs where clubs.id=club_exterieurs.idClub and club_exterieurs.idMatch='.$match->id);
                $noteDomicile=($clubDomicile[0]->noteInstantannee+$clubDomicile[0]->noteFormation)/2;
                $noteExterieur=($clubExterieur[0]->noteInstantannee+$clubExterieur[0]->noteFormation)/2;
                $noteDomicile=rand($noteDomicile-100,$noteDomicile+10);
                $noteExterieur=rand($noteExterieur-150,$noteExterieur+50);
                if($noteExterieur===$noteDomicile){
                    $resultat+=0;
                }elseif($noteDomicile>$noteExterieur){
                    $resultat+=1;
                }else{
                    $resultat-=1;
                }
                score($resultat,$match->id,$clubDomicile[0]->id,$clubExterieur[0]->id);
                fatigue($match->quartTemps,$clubDomicile[0]->id);
                fatigue($match->quartTemps,$clubExterieur[0]->id);
                DB::table('matches')->where([['idCompetition',$idCompet],['id',$match->id]])->increment('quartTemps');
            }
        }
    }

    public function jouerSaison(Request $request){
        $result=$request->all();
        $idCompet=$result['idCompetition'];
        $matches=DB::select('select * from matches where quartTemps<4 and idCompetition='.$idCompet);
        foreach ($matches as $match){
            $quartTempsRestant=4-($match->quartTemps);
            for($i=0;$i<$quartTempsRestant;$i++){
                $resultat=0;
                $clubDomicile=DB::select('select clubs.* from clubs,club_domiciles where clubs.id=club_domiciles.idClub and club_domiciles.idMatch='.$match->id);
                $clubExterieur=DB::select('select clubs.* from clubs,club_exterieurs where clubs.id=club_exterieurs.idClub and club_exterieurs.idMatch='.$match->id);
                $noteDomicile=($clubDomicile[0]->noteInstantannee+$clubDomicile[0]->noteFormation)/2;
                $noteExterieur=($clubExterieur[0]->noteInstantannee+$clubExterieur[0]->noteFormation)/2;
                $noteDomicile=rand($noteDomicile-100,$noteDomicile+10);
                $noteExterieur=rand($noteExterieur-150,$noteExterieur+50);
                if($noteExterieur===$noteDomicile){
                    $resultat+=0;
                }elseif($noteDomicile>$noteExterieur){
                    $resultat+=1;
                }else{
                    $resultat-=1;
                }
                score($resultat,$match->id,$clubDomicile[0]->id,$clubExterieur[0]->id);
                fatigue($match->quartTemps,$clubDomicile[0]->id);
                fatigue($match->quartTemps,$clubExterieur[0]->id);
                DB::table('matches')->where([['idCompetition',$idCompet],['id',$match->id]])->increment('quartTemps');
            }
            $clubDomicile = DB::select('select clubs.* from clubs,club_domiciles where clubs.id=club_domiciles.idClub and club_domiciles.idMatch=' . $match->id);
            $clubExterieur = DB::select('select clubs.* from clubs,club_exterieurs where clubs.id=club_exterieurs.idClub and club_exterieurs.idMatch=' . $match->id);
            finMatch($idCompet, $match->id, $clubDomicile[0]->id, $clubExterieur[0]->id);
        }
    }

    public function finMatchs(Request $request){
        $result=$request->all();
        $idCompet=$result['idCompetition'];
        $jour=$result['jour'];
        $matches=DB::select('select * from matches where idCompetition='.$idCompet.' and date='.$jour);
        $quartTemps=DB::select('SELECT min(quartTemps) as quartTemps FROM matches WHERE idCompetition='.$idCompet.' AND DATE='.$jour);
        if(($quartTemps[0]->quartTemps)>3){
            foreach ($matches as $match) {
                $clubDomicile = DB::select('select clubs.* from clubs,club_domiciles where clubs.id=club_domiciles.idClub and club_domiciles.idMatch=' . $match->id);
                $clubExterieur = DB::select('select clubs.* from clubs,club_exterieurs where clubs.id=club_exterieurs.idClub and club_exterieurs.idMatch=' . $match->id);
                finMatch($idCompet, $match->id, $clubDomicile[0]->id, $clubExterieur[0]->id);
            }
        }
    }
}

function fatigue($quartTemps,$idClub){
    $joueurs=DB::select('select joueurs.* from joueurs, contrats where contrats.idJoueur=joueurs.id and joueurs.joue<>"Banc" and contrats.idClub='.$idClub);
    foreach($joueurs as $joueur){
        if ($joueur->endurance<70){
            $attenuation = [8,18,35,65];
        }
        elseif($joueur->endurance<80){
            $attenuation = [7,15,30,60];
        }
        elseif($joueur->endurance<90){
            $attenuation = [6,12,25,50];
        }
        else{
            $attenuation = [5,10,20,40];
        }
        $notePoste=1;
        if($joueur->joue=='Gardien'){
            $notePoste=$joueur->noteGardien;
        }elseif($joueur->joue=='Defenseur'){
            $notePoste=$joueur->noteDefense;
        }elseif($joueur->joue=='Milieu'){
            $notePoste=$joueur->noteMilieu;
        }elseif($joueur->joue=='Attaquant'){
            $notePoste=$joueur->noteAttaque;
        }
        $joueur->forme-=$attenuation[$quartTemps];
        if ($joueur->forme <= 0) $joueur->forme = 5;
        $noteInstantanee=(($joueur->noteGlobale+$notePoste)/2)*($joueur->forme/100);
        DB::table('joueurs')->where('id',$joueur->id)->update(['noteInstantanee' => $noteInstantanee],['forme' => $joueur->forme]);
    }
    $note=0;
    $i=0;
    $notesJoueur=DB::select('select noteInstantanee from joueurs, contrats where joueurs.id=contrats.idJoueur and contrats.idClub='.$idClub);
    foreach ($notesJoueur as $noteJoueur){
        $note+=$noteJoueur->noteInstantanee;
        $i++;
    }
    $note=$note/$i;
    DB::table('clubs')->where('id', $idClub)->update(['noteInstantannee' => $note]);
}

function score($result,$idMatch,$idClubDomicile,$idClubExterieur){
    if($result==0){
        $nbBut=rand(0,rand(0,3));
        DB::table('club_domiciles')->where([['idClub',$idClubDomicile],['idMatch',$idMatch]])->increment('nbBut',$nbBut);
        DB::table('club_exterieurs')->where([['idClub',$idClubExterieur],['idMatch',$idMatch]])->increment('nbBut',$nbBut);
    }elseif($result>0){
        $noteAttaque=DB::select('select noteAttaque from clubs where id='.$idClubDomicile);
        $noteDefense=DB::select('select noteDefense from clubs where id='.$idClubExterieur);
        $chanceButs=($noteAttaque[0]->noteAttaque)-($noteDefense[0]->noteDefense);
        if ($chanceButs < 1){
            $chanceButs = 0;
        }
        elseif ($chanceButs < 5){
            $chanceButs = 2;
        }
        else
        {
            $chanceButs = 4;
        }
        DB::table('club_domiciles')->where('idMatch',$idMatch)->where('idClub',$idClubDomicile)->increment('nbBut',rand(1,$chanceButs+1));
        DB::table('club_exterieurs')->where('idMatch',$idMatch)->where('idClub',$idClubExterieur)->increment('nbBut',rand(0,$chanceButs));
    }else{
        $noteDefense=DB::select('select noteDefense from clubs where id='.$idClubDomicile);
        $noteAttaque=DB::select('select noteAttaque from clubs where id='.$idClubExterieur);
        $chanceButs=($noteAttaque[0]->noteAttaque)-($noteDefense[0]->noteDefense);
        if ($chanceButs < 1){
            $chanceButs = 0;
        }
        elseif ($chanceButs < 5){
            $chanceButs = 2;
        }
        else
        {
            $chanceButs = 4;
        }
        DB::table('club_domiciles')->where('idMatch',$idMatch)->where('idClub',$idClubDomicile)->increment('nbBut',rand(0,$chanceButs));
        DB::table('club_exterieurs')->where('idMatch',$idMatch)->where('idClub',$idClubExterieur)->increment('nbBut',rand(1,$chanceButs+1));
    }
}

function finMatch($idCompet,$idMatch,$idClubDomicile,$idClubExterieur){
    $nbButDomicile=DB::table('club_domiciles')->select('nbBut')->where('idMatch',$idMatch)->where('idClub',$idClubDomicile)->get();
    $nbButExterieur=DB::table('club_exterieurs')->select('nbBut')->where('idMatch',$idMatch)->where('idClub',$idClubExterieur)->get();
    $recette=DB::select('select matches.recette from matches, club_domiciles,club_exterieurs where matches.id=club_domiciles.idMatch and club_domiciles.idClub='.$idClubDomicile);
    $recette=$recette[0]->recette;
    if($nbButDomicile[0]->nbBut==$nbButExterieur[0]->nbBut){
        DB::table('classements')->where('idCompetition',$idCompet)->where('idClub',$idClubDomicile)->increment('points');
        DB::table('classements')->where('idCompetition',$idCompet)->where('idClub',$idClubDomicile)->increment('nbNuls');
        DB::table('classements')->where('idCompetition',$idCompet)->where('idClub',$idClubExterieur)->increment('points');
        DB::table('classements')->where('idCompetition',$idCompet)->where('idClub',$idClubExterieur)->increment('nbNuls');
        DB::table('clubs')->where('id',$idClubDomicile)->increment('budget',$recette*0.5);
        DB::table('clubs')->where('id',$idClubExterieur)->increment('budget',$recette*0.5);
    }elseif($nbButDomicile>$nbButExterieur){
        DB::table('classements')->where('idCompetition',$idCompet)->where('idClub',$idClubDomicile)->increment('points',3);
        DB::table('classements')->where('idCompetition',$idCompet)->where('idClub',$idClubDomicile)->increment('nbVictoires');
        DB::table('classements')->where('idCompetition',$idCompet)->where('idClub',$idClubExterieur)->increment('nbDefaites');
        DB::table('clubs')->where('id',$idClubDomicile)->increment('budget',$recette*0.7);
        DB::table('clubs')->where('id',$idClubExterieur)->increment('budget',$recette*0.3);
    }else{
        DB::table('classements')->where('idCompetition',$idCompet)->where('idClub',$idClubExterieur)->increment('points',3);
        DB::table('classements')->where('idCompetition',$idCompet)->where('idClub',$idClubExterieur)->increment('nbVictoires');
        DB::table('classements')->where('idCompetition',$idCompet)->where('idClub',$idClubDomicile)->increment('nbDefaites');
        DB::table('clubs')->where('id',$idClubDomicile)->increment('budget',$recette*0.3);
        DB::table('clubs')->where('id',$idClubExterieur)->increment('budget',$recette*0.7);
    }
    $joueurs=DB::select('select joueurs.id from joueurs,contrats where joueurs.id=contrats.idJoueur and contrats.idClub='.$idClubDomicile);
    foreach ($joueurs as $joueur){
        $noteGlobale=DB::select('select noteGlobale from joueurs where id='.$joueur->id);
        $noteGlobale=$noteGlobale[0]->noteGlobale;
        DB::table('joueurs')->where('id', $joueur->id)->update(['forme' => 100 , 'noteInstantanee' => $noteGlobale]);
    }
    $joueurs=DB::select('select joueurs.id from joueurs,contrats where joueurs.id=contrats.idJoueur and contrats.idClub='.$idClubExterieur);
    foreach ($joueurs as $joueur){
        $noteGlobale=DB::select('select noteGlobale from joueurs where id='.$joueur->id);
        $noteGlobale=$noteGlobale[0]->noteGlobale;
        DB::table('joueurs')->where('id', $joueur->id)->update(['forme' => 100, 'noteInstantanee' => $noteGlobale]);
    }
    $noteGlobaleClubExterieur=DB::select('select noteAbsolue from clubs where id='.$idClubExterieur);
    $noteGlobaleClubDomicile=DB::select('select noteAbsolue from clubs where id='.$idClubDomicile);
    $noteGlobaleClubExterieur=$noteGlobaleClubExterieur[0]->noteAbsolue;
    $noteGlobaleClubDomicile=$noteGlobaleClubDomicile[0]->noteAbsolue;
    DB::table('clubs')->where('id', $idClubExterieur)->update(['noteInstantannee' => $noteGlobaleClubExterieur]);
    DB::table('clubs')->where('id', $idClubDomicile)->update(['noteInstantannee' => $noteGlobaleClubDomicile]);
}
