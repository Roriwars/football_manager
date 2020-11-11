<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Joueur;
use DB;

class JoueurController extends Controller
{
    public function index(){
        return DB::select('select * from joueurs');
    }

    public function create(){
        creerJoueur(200);
    }

    public function reset(){
        Joueur::destroy();
    }

    public function mesJoueurs(){
        $mesJoueurs=DB::table('joueurs')
            ->leftJoin('contrats','joueurs.id','=','contrats.idJoueur')
            ->join('clubs', 'contrats.idClub', '=', 'clubs.id')
            ->where('clubs.isMain','1')
            ->select('joueurs.*','contrats.dureeAnneesContrat')
            ->get();
        return $mesJoueurs;
    }
}
