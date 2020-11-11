<?php
use App\Ville;

class mVille
{
	public $_nomStade;
	public $_capacite;

	# constructeur
	function __construct($args)
	{
		$this->_nomVille = $args['nomVille'];
		$this->_attractivite = $args['attractivite'];
        $this->bdd();
	}
    
    function bdd(){
        $nomExiste=DB::table('villes')->where('nomVille',$nomVille)->value('nomVille');
        if($nomExiste==null){
            $ville=new Ville();
            $ville->nomVille=$this->_nomVille;
            $ville->attractivite=$this->_attractivite;
            $ville->save();
        }
    }
}