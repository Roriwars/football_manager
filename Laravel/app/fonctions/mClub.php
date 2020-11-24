<?php
require_once('mStade.php');
require_once('mVille.php');

class mClub
{
	static $nbr_club = 0; // nombre de joueur
	private $_id_club; // id du joueur
	public $_nomClub;
	public $_ville; // {'ville'=>'Ajaccio','attractivite'=>100}
	public $_stade; // {'nom'=>'SAC','capacite'=>10000} 
	/*public $_effectif; // liste des joueurs
	public $_equipe; // liste des 5 joueurs qui jouent {'gardien'=>nom du joueur,...}*/
    public $_reputation;
	public $_budget;
	public $_formation ; // {'gardien'=>1,'defenseur'=>1,'milieu'=>2,'attaquat'=>1}
	public $_noteAbs; # note de l effectif
	public $_noteFormation; # note des joueurs qui jouent
	public $_noteInstantannee; # note temporaire
	/*public $_classement; 
	public $_calendrier; 
	public $_nextMatch;
	public $_points = 0;
	public $_noteAttaque = 0;
	public $_noteDefense = 0;*/

	# constructeur
	function __construct($args)
	{
        #VILLE
        $this->_ville = new mVille($args['ville']);
		#STADE
        $this->_stade = new mStade($args['stade']); // {'nom'=>'SAC','capacite'=>10000} 
        #CLUB
		Club::$nbr_club += 1;
		$this->_id_club = Club::$nbr_club;
		$this->_nomClub = $args['nomClub'];
		$this->_reputation = 0;
		$this->_budget = 200;
		$this->_formation = '[1,2,1]';// {'gardien'=>1,'defenseur'=>1,'milieu'=>2,'attaquant'=>1}
		$this->noteEffectif();
		$this->noteEquipe();
        $this->bdd();
	}
    
    function bdd(){
        $club=new Club();
        $club->nomClub=$this->_nomClub;
        $club->reputation=$this->_reputation;
        $club->budget=$this->_budget;
        $club->formation=$this->_formation;
        $club->noteAbsolue=$this->_noteAbsolue;
        $club->noteFormation=$this->_noteFormation;
        $club->noteInstantannee=$this->_noteInstantannee;
        $club->nomStade=$this->stade->nomStade;
        $club->nomVille=$this->ville->nomVille;
        $club->save();
    }

	# getter id
	function getId()
	{
		return $this->_id_club;
	}

	# setter id
	function setId($value)
	{
		$this->_id_club = $value;
	}

	# definition de la formation de l equipe et MAJ des notes
	function setFormation($tab=[1,2,1])
	{
		# ToDO ICI MODIFIER LES POSTES DES JOUEURS
		$equipe = array();
		$this->_formation = $tab;
		$equipe['gardien'] = $this->searchJoueur('gardien',1);
		$equipe['defenseur'] = $this->searchJoueur('defense',$this->_formation[0]);
		$equipe['milieu'] = $this->searchJoueur('milieu',$this->_formation[1]);
		$equipe['attaquant'] = $this->searchJoueur('attaque',$this->_formation[2]);
		$this->_equipe = $equipe;
		#var_dump($this->_equipe['milieu']);
		$this->noteEquipe();
	}

	# recherche de n joueur(s) à un poste donnée
	function searchJoueur($poste='attaque',$nbr=1)
	{
		$tabTmpByPoste = array();
		foreach ($this->_effectif as $key => $value) {
			#echo "$value->_pposition vs $poste <br>";
			if ($value->_pposition == $poste){
				# tri du tableau par meilleure note
				if (count($tabTmpByPoste)===0){
					array_push($tabTmpByPoste,$value);
				}
				elseif($tabTmpByPoste[0]->_noteG > $value->_noteG){
					array_push($tabTmpByPoste,$value);
				}else{
					array_unshift($tabTmpByPoste,$value); 
				}			
			}
			# maj le poste du joueur
			# si $nbr == 1 centre sinon droit et gauche
			if ($nbr==1){
				if ($poste == 'gardien'){
					$name = $poste[0].'k';
				}
				else{
					$name = $poste[0].'c';
				}
				$name = strtoupper($name);
				$value->posteMatch = $name;
			}
			elseif ($nbr==2 and count($tabTmpByPoste)===1)
			{
				$name = $poste[0].'g';
				$name = strtoupper($name);
				$value->posteMatch = $name;
			}
			else {
				$name = $poste[0].'d';
				$name = strtoupper($name);
				$value->posteMatch = $name;
			}
		}
		//var_dump($tabTmpByPoste);
		return array_slice($tabTmpByPoste,0,$nbr);
	}

	# création de l effectif
	function setEffectif($value)
	{
		if (gettype($value)=="array"){
			$this->_effectif = $value;
		}
		else{
			array_push($this->_effectif,$value);
		}
		# maj de la note de l'effectif
		$this->noteEffectif();
		# attribution d'un contrat au joueur
		foreach ($this->_effectif as $joueur){
			$joueur->newContrat($this->_nom,4,10);
		}
	}

	# RAZ des notes
	function repos()
	{
		foreach ($this->_effectif as $joueur){
			$joueur->repos();
		}
		$this->noteEquipe();
	}

	# gestion des notes de l effectif
	function noteEffectif()
	{
		$somme = 0; 
		$size = count($this->_effectif);
		for ($i=0; $i < $size; $i++) { 
			$somme += $this->_effectif[$i]->_noteG;
		}
		if ($size == 0){ $size = 1;}
		$this->_noteAbs = floor($somme / $size);
	}

	# gestion des notes de l equipe
	function noteEquipe()
	{
		$somme = 0;
		$sommeAttaque = 0;
		$sommeDefense = 0;
		foreach ($this->_equipe as $joueurs) {
			foreach ($joueurs as $joueur) {
				$somme += $joueur->_noteG;
				$sommeAttaque += $joueur->attaque;
				$sommeDefense += $joueur->defense;
			}
		}
		$this->_noteFormation = floor($somme / 5);
		$this->_noteAttaque = floor($sommeAttaque / 5);
		$this->_noteDefense = floor($sommeDefense / 5);
	}

	# fonction utilisée en match pour diminuer la forme des joueurs et MAJ des notes
	function runMatch($time=1)
	{
		$somme = 0; 
		foreach ($this->_equipe as $joueurs) {
			foreach ($joueurs as $joueur)  {
				$joueur->runMatch($time,$joueur->poste);
				$somme += $joueur->_noteG;
			}
		}
		# à vérifier modifier note temporaire
		$this->_noteFormation = floor($somme / 5);
	}

	# gestion de la fin de saison
	function nextYear()
	{
		# augmente le budget à améliorer avec le classement en compétition
		$this->_budget += 100;
		$sumSalaire = 0;
		foreach ($this->_effectif as $joueur){
			# gestion de l'age et des contrats des joueurs
			$joueur->nextYear();
			$sumSalaire += $joueur->contrat['salaire'] * 12;
			if ($joueur->contrat['club'] != $this->_nom)
			{
				// suppression du joueur du club
				unset($this->_effectif[array_search($joueur, $this->_effectif)]);
			}
		}
		$this->_budget -= $sumSalaire;
	}

	# getter et setter
	function action($arg,$value)
	{
		if (isset($value))
		{
			$this->{$arg} = $value;
		}
		else
		{
			return $this->{$arg};
		}
	}

	# acheter joueur
	function achat($joueur)
	{
		$joueur->newContrat($this->_nom,4,10);
		array_push($this->_effectif,$joueur);
		# gérer le prix du joueur
		$this->_budget -= $joueur->_prix;
	}
	# vente de joueur
	function vendre($joueur)
	{
		$joueur->newContrat('libre',0,0);
		unset($this->_effectif[array_search($joueur, $this->_effectif)]);
		# prix de vente
		$this->_budget += $joueur->_prix;
	}

	function prinTeam()
	{
		$output = '<table>';
		$output .= '<tr><td>Club</td><td>'.ucfirst($this->_nom).'</td><td>Note : '.$this->_noteFormation.'</td></tr>';
		foreach ($this->_equipe as $joueurs) {
			foreach ($joueurs as $joueur) {
				$output .= '<tr><td>'.ucfirst($joueur->_pposition).'</td>
							<td>'.ucfirst($joueur->_nom).'</td>
							<td>'.ucfirst($joueur->_noteG).'</td>
							</tr>';
			}
		}
		$output .= '<table>';
		return $output;
	}

	function __toString2()
	{
		$output = '<table class="table">';
		$output .= '<tr><td>Club : </td><td>'.ucfirst($this->_nom).'</td></tr>';
		$output .= '<tr><td>Ville : '.ucfirst($this->_ville['ville']).'</td><td>'.$this->_ville['attractivite'].'</td></tr>';
		$output .= '<tr><td>Stade : '.ucfirst($this->_stade['nom']).'</td><td> Capacité : '.$this->_stade['capacite'].'</td></tr>';
		$output .= '<tr><td>Note effectif : </td><td>'.$this->_noteAbs.'</td></tr>';
		$output .= '<tr><td>Note formation : </td><td>'.$this->_noteFormation.'</td></tr>';		
		$output .= '</table>';
		return $output;
	}
    
    function __toString()
	{
		$output = '<tr><td>'.ucfirst($this->_nom).'</td>';
		$output .= '<td>'.ucfirst($this->_ville['ville']).'</td>';
		$output .= '<td>'.ucfirst($this->_stade['nom']).'</td></tr>';		
		return $output;
	}
}

/*
echo "<br>test<br>";
$parms = array('nom'=>'secb','ville'=>'bastia','stade'=>'neant');
$objeTest = new Joueur($parms);
echo $objeTest;
print_r($objeTest->_stat);
*/
