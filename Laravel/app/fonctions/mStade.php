<?php
use App\Stade;

class mStade
{
	public $_nomStade;
	public $_capacite;

	# constructeur
	function __construct($args)
	{
		$this->_nomStade = $args['nomStade'];
		$this->_capacite = $args['capacite'];
        $this->bdd();
	}
    
    function bdd(){
        $nomExiste=DB::table('stades')->where('nomStade',$nomStade)->value('nomStade');
        if($nomExiste==null){
            $stade=new Stade();
            $stade->nomStade=$this->nomStade;
            $stade->capacite=$this->capacite;
            $stade->save();
        }
    }
}