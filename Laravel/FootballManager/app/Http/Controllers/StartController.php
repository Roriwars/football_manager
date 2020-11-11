<?php

namespace App\Http\Controllers;

use App\Club;
use App\Ville;
use App\Stade;
use App\Joueur;
use DB;
use Illuminate\Http\Request;

class StartController extends Controller
{
    public function liste(){
        $clubs = DB::table('clubs')->whereNull('isMain')->get();
        //dd($clubs);
        $joueurs = DB::select('select * from joueurs order by postePredilection, nom, prenom, age');
        if($clubs!=null && $joueurs!=null){
            
        }else{
            return view('/','HomePageController@main');
        }
        return view('start',[
            'clubs'=>$clubs,
            'joueurs'=>$joueurs
        ]);
    }
    
    public function select(){
        $mainExiste=DB::table('clubs')->where('isMain','1')->get();
        //dd($mainExiste);
        if($mainExiste!=null){
            //il existe déjà un club selectionner
            //changer le club ??
        }else{
            if(request('validerNewClub')){
                request()->validate([
                    'nomClub'=>'required|max:255',
                    'nomVille'=>'required|max:255',
                    'nomStade'=>'required|max:255'
                ]);
                $nomClub=request('nomClub');
                $nomVille=request('nomVille');
                $nomStade=request('nomStade');

                $nomExiste=DB::table('villes')->where('nomVille',$nomVille)->value('nomVille');
                if($nomExiste==null){
                    $ville = new Ville();
                    $ville->nomVille=$nomVille;
                    $ville->attractivite = rand(60,100);
                    $ville->save();
                }
                //Stade
                $nomExiste=DB::table('stades')->where('nomStade',$nomStade)->value('nomStade');
                if($nomExiste==null){
                    $stade = new Stade();
                    $stade->nomStade = $nomStade;
                    $stade->capacite = rand(8000,80000);
                    $stade->save();
                }
                //Club            
                $club=new Club();
                $club->nomClub=$nomClub;
                $club->reputation=0;
                $club->budget=200;
                $club->formation='[1,2,1]';
                $club->noteAbsolue=0;
                $club->noteFormation=0;
                $club->noteInstantannee=0;
                $club->nomStade=$nomStade;
                $club->nomVille=$nomVille;
                $club->isMain='1';
                $club->save();
            }
            if(request('validerClub')){
                $nomClub=request('nomClub');
                DB::table('clubs')->where('nomClub',$nomClub)->update(['isMain'=>'1']);
            }
        }
        return back();
    }
}
