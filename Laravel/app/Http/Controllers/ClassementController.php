<?php

namespace App\Http\Controllers;

use App\Classement;
use Illuminate\Http\Request;
use DB;

class ClassementController extends Controller
{
    public function getClassement($idCompet){
        return DB::select('select clubs.nomClub,classements.* from clubs,classements where clubs.id=classements.idClub and classements.idCompetition='.$idCompet.' order by classements.points DESC');
    }
}
