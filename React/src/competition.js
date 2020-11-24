import React, {Component} from 'react';
import {getCompet, getInfoClub, newTactique, finCompet, getMatches, getJour, jouerQuartTemps, jouerMatch, finMatch, jouerSaison} from './listeFunctions'
import Modal from 'react-awesome-modal';
import FinCompetition from './finCompetition'

class Competition extends Component{
    constructor(){
        super();
        this.state={
            nomCompet:{},
            classement:[],
            matches:[],
            journee:{},
            mesJoueurs:[],
            monClub:{},
            joueurTerrain:[],
            showPopup:false,
            changerTactique:false,
            infoClub:{},
            finCompet:false,
            tactique:'',
            joueursTactique:new Array(5),
            changementOk:false,
            btn:"button is-primary is-outlined is-medium is-fullwidth"
        }
    }

    componentDidMount(){
        this.getCompet()
        console.log(this.state.finCompet)
    }

    togglePopup = (idClub) => {
        getInfoClub(idClub).then(data =>{
            this.setState({
                infoClub:data,
                showPopup: true  
           });  
        })
    }

    closeTogglePopup = () => {  
        this.setState({  
             showPopup: false  
        });
    }

    changerTactiquePopup = () => {
        this.setState({
            changerTactique: true  
        });  
    }

    closeChangerTactiquePopup = () => {  
        this.setState({  
             changerTactique: false  
        });
    }

    changerTactique = () =>{
        return(
            <div class="control">
                <label class="label">Formation</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        this.setState(
                            {
                                tactique:event.target.value
                            }
                        )
                        }}>
                        <option value="[1,2,1]">1-2-1</option>
                        <option value="[1,1,2]">1-1-2</option>
                        <option value="[2,1,1]">2-1-1</option>
                    </select>
                </div>
                {this.tactiqueEquipe()}
                <div class="panel-bloc">
                    <button class="button" onClick={()=>this.envoyerNouvelleTactique()} disabled={!this.state.changementOk}>Valider Changement</button>
                </div>
            </div>
        )
    }

    validerChangement = () =>{
        this.setState({
            changementOk:true
        })
        for(let i=0;i<this.state.joueursTactique.length-1;i++){
            for(let j=i+1;j<this.state.joueursTactique.length;j++){
                if(this.state.joueursTactique[i]===this.state.joueursTactique[j]){
                    this.setState({
                        changementOk:false
                    })
                    break;
                }
            }
        }
    }

    envoyerNouvelleTactique = () =>{
        console.log(this.state.joueursTactique)
        console.log(this.state.tactique)
        newTactique(this.state.joueursTactique,this.state.tactique).then(() =>{
                this.setState(
                    {
                        changerTactique: false
                    }
                )
                this.getMatches()
            }
        )
    }

    tactiqueEquipe = () => {
        let joueurs=new Array(5)
        if(this.state.tactique==='[1,2,1]'){
            return(
            <div>
                <label class="label">Attaquant</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[0]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }
                        }>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
                <label class="label">Milieu</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[1]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
                <label class="label">Milieu</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[2]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
                <label class="label">Défenseur</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[3]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
                <label class="label">Gardien</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[4]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
            </div>)
        }
        if(this.state.tactique==='[1,1,2]'){
            return(
            <div>
                <label class="label">Attaquant</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[0]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
                <label class="label">Attaquant</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[1]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
                <label class="label">Milieu</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[2]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
                <label class="label">Défenseur</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[3]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
                <label class="label">Gardien</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[4]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
            </div>)
        }
        if(this.state.tactique==='[2,1,1]'){
            return(
            <div>
                <label class="label">Attaquant</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[0]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
                <label class="label">Milieu</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[1]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
                <label class="label">Défenseur</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[2]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
                <label class="label">Défenseur</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[3]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
                <label class="label">Gardien</label>
                <div class="select is-rounded">
                    <select value={this.state.value} onChange={(event) => {
                        joueurs=this.state.joueursTactique
                        joueurs[4]=event.target.value
                        this.setState(
                            {
                                joueursTactique:joueurs
                            }
                        )
                        this.validerChangement()
                        }}>
                        {this.state.mesJoueurs.map(joueur => (
                            <option value={joueur.id}>{joueur.nom} {joueur.prenom} -> {joueur.postePredilection}</option>
                        ))}
                    </select>
                </div>
            </div>)
        }
    }

    getCompet = () => {
        getCompet().then(data => {
            this.setState(
                {
                    nomCompet:data
                }
            )
            if(this.state.nomCompet.statut==="fini"){
                this.setState({
                    finCompet:true
                })
            }
            this.getJour()
        })
    }
    
    getMatches = () => {
        console.log(this.state.nomCompet.id)
        console.log(this.state.journee.jour)
        getMatches(this.state.nomCompet.id,this.state.journee.jour).then(data => {
            let donnees=[...data]
            console.log(donnees)
            let joueursTactiqueDefaut=[""+donnees[2][0].id,""+donnees[2][0].id,""+donnees[2][0].id,""+donnees[2][0].id,""+donnees[2][0].id]
            this.setState(
                {
                    matches:donnees[0],
                    mesJoueurs:donnees[2],
                    joueursTactique:joueursTactiqueDefaut,
                    monClub:donnees[1][0],
                    tactique:donnees[1][0].formation,
                    classement:donnees[3],
                    btn:"button is-primary is-outlined is-medium is-fullwidth"
                }
            )
        })
    }

    getJour = () => {
        getJour(this.state.nomCompet.id).then(data => {
            if(data==='vide'){
                finCompet().then(() => {
                    this.setState(
                        {
                            finCompet:true
                        }
                    )
                })
            }else{
                this.setState(
                    {
                        journee:data,
                        btn:"button is-primary is-outlined is-medium is-fullwidth"
                    }
                )
                console.log(this.state.journee)
                this.getMatches()
            }
        })
    }

    jouerQuartTemps = () => {
        this.setState(
            {
                btn:"button is-primary is-outlined is-medium is-fullwidth is-loading",
            }
        )
        let joueurs=[]
        for(var i=0;i<this.state.mesJoueurs.length;i++){
            if(this.state.mesJoueurs[i].joue!=='Banc'){
                joueurs.push(this.state.mesJoueurs[i])
            }
        }
        if(joueurs.length>=5){
            jouerQuartTemps(this.state.nomCompet.id,this.state.journee.jour).then(() => {
                if(this.state.journee.quartTemps<3){
                    this.getJour()
                }else{
                    this.getMatches()
                    this.setState(
                        {
                            journee:{
                                jour:this.state.journee.jour,
                                jourMax:this.state.journee.jourMax,
                                quartTemps:'Fin des match'
                            }
                        }
                    )
                }
            })
        }else{
            alert("Il n'y a pas assez de joueurs assigné à votre formation")
            this.setState(
                {
                    btn:"button is-primary is-outlined is-medium is-fullwidth"
                }
            )
        }
    }

    jouerMatch = () => {
        this.setState(
            {
                btn:"button is-primary is-outlined is-medium is-fullwidth is-loading",
            }
        )
        let joueurs=[]
        for(var i=0;i<this.state.mesJoueurs.length;i++){
            if(this.state.mesJoueurs[i].joue!=='Banc'){
                joueurs.push(this.state.mesJoueurs[i])
            }
        }
        if(joueurs.length>=5){
            jouerMatch(this.state.nomCompet.id,this.state.journee.jour).then(() => {
                this.getMatches()
                this.setState(
                    {
                        journee:{
                            jour:this.state.journee.jour,
                            jourMax:this.state.journee.jourMax,
                            quartTemps:'Fin des match'
                        }
                    }
                )
            })
        }else{
            alert("Il n'y a pas assez de joueurs assigné à votre formation")
            this.setState(
                {
                    btn:"button is-primary is-outlined is-medium is-fullwidth"
                }
            )
        }
    }

    jouerSaison = () => {
        this.setState(
            {
                btn:"button is-primary is-outlined is-medium is-fullwidth is-loading",
            }
        )
        let joueurs=[]
        for(var i=0;i<this.state.mesJoueurs.length;i++){
            if(this.state.mesJoueurs[i].joue!=='Banc'){
                joueurs.push(this.state.mesJoueurs[i])
            }
        }
        if(joueurs.length>=5){
            jouerSaison(this.state.nomCompet.id).then(() => {
                this.getJour()
            })
        }else{
            alert("Il n'y a pas assez de joueurs assigné à votre formation")
            this.setState(
                {
                    btn:"button is-primary is-outlined is-medium is-fullwidth"
                }
            )
        }
    }

    finMatch = () => {
        finMatch(this.state.nomCompet.id,this.state.journee.jour).then(() => {
            this.getJour()
        })
    }

    tabJoueurTerrain(){
        let joueurs=[]
        for(var i=0;i<this.state.mesJoueurs.length;i++){
            if(this.state.mesJoueurs[i].joue!=='Banc'){
                joueurs.push(this.state.mesJoueurs[i])
            }
        }
        if(this.state.monClub.formation==='[1,2,1]'){
            let milieu=[]
            let attaquant=""
            let gardien=""
            let defenseur=""
            for(i=0;i<joueurs.length;i++){
                if(joueurs[i].joue==='Attaquant'){
                    attaquant=joueurs[i].nom
                }
                if(joueurs[i].joue==='Defenseur'){
                    defenseur=joueurs[i].nom
                }
                if(joueurs[i].joue==='Milieu'){
                    milieu.push(joueurs[i].nom)
                }
                if(joueurs[i].joue==='Gardien'){
                    gardien=joueurs[i].nom
                }
            }
            return(
                <table class="table is-bordered is-fullwidth has-background-success is-narrow">
                    <tbody>
                        <tr>
                            <td class="has-text-weight-bold has-background-white">Attaquants</td>
                            <td></td>
                            <td><p class="has-text-white has-text-centered has-text-weight-bold">{attaquant}</p></td>
                            <td></td>
                        </tr>                        
                        <tr>
                            <td class="has-text-weight-bold  has-background-white">Milieux</td>
                            <td><p class="has-text-white has-text-centered has-text-weight-bold">{milieu[0]}</p></td>
                            <td></td>
                            <td><p class="has-text-white has-text-centered has-text-weight-bold">{milieu[1]}</p></td>
                        </tr>
                        <tr>
                            <td class="has-text-weight-bold has-background-white">Défenseurs</td>
                            <td></td>
                            <td><p class="has-text-white has-text-centered has-text-weight-bold">{defenseur}</p></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="has-text-weight-bold has-background-white">Gardien</td>
                            <td></td>
                            <td><p class="has-background-warning has-text-white has-text-centered has-text-weight-bold">{gardien}</p></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            )
        }
        if(this.state.monClub.formation==='[1,1,2]'){
            let milieu=""
            let attaquant=[]
            let gardien=""
            let defenseur=""
            for(i=0;i<joueurs.length;i++){
                if(joueurs[i].joue==='Attaquant'){
                    attaquant.push(joueurs[i].nom)
                }
                if(joueurs[i].joue==='Defenseur'){
                    defenseur=joueurs[i].nom
                }
                if(joueurs[i].joue==='Milieu'){
                    milieu=joueurs[i].nom
                }
                if(joueurs[i].joue==='Gardien'){
                    gardien=joueurs[i].nom
                }
            }
            return(
                <table class="table is-bordered is-fullwidth has-background-success is-narrow">
                    <tbody>
                        <tr>
                            <td class="has-text-weight-bold has-background-white">Attaquants</td>
                            <td><p class="has-text-white has-text-centered has-text-weight-bold">{attaquant[0]}</p></td>
                            <td></td>
                            <td><p class="has-text-white has-text-centered has-text-weight-bold">{attaquant[1]}</p></td>
                        </tr>                        
                        <tr>
                            <td class="has-text-weight-bold has-background-white">Milieux</td>
                            <td></td>
                            <td><p class="has-text-white has-text-centered has-text-weight-bold">{milieu}</p></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="has-text-weight-bold has-background-white">Défenseurs</td>
                            <td></td>
                            <td><p class="has-text-white has-text-centered has-text-weight-bold">{defenseur}</p></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="has-text-weight-bold has-background-white">Gardien</td>
                            <td></td>
                            <td><p class="has-background-warning has-text-white has-text-centered has-text-weight-bold">{gardien}</p></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            )
        }
        if(this.state.monClub.formation==='[2,1,1]'){
            let milieu=""
            let attaquant=""
            let gardien=""
            let defenseur=[]
            for(i=0;i<joueurs.length;i++){
                if(joueurs[i].joue==='Attaquant'){
                    attaquant=joueurs[i].nom
                }
                if(joueurs[i].joue==='Defenseur'){
                    defenseur.push(joueurs[i].nom)
                }
                if(joueurs[i].joue==='Milieu'){
                    milieu=joueurs[i].nom
                }
                if(joueurs[i].joue==='Gardien'){
                    gardien=joueurs[i].nom
                }
            }
            return(
                <table class="table is-bordered is-fullwidth has-background-success is-narrow">
                    <tbody>
                        <tr>
                            <td class="has-text-weight-bold has-background-white">Attaquants</td>
                            <td></td>
                            <td><p class="has-text-white has-text-centered has-text-weight-bold">{attaquant}</p></td>
                            <td></td>
                        </tr>                        
                        <tr>
                            <td class="has-text-weight-bold has-background-white">Milieux</td>
                            <td></td>
                            <td><p class="has-text-white has-text-centered has-text-weight-bold">{milieu}</p></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="has-text-weight-bold has-background-white">Défenseurs</td>
                            <td><p class="has-text-white has-text-centered has-text-weight-bold">{defenseur[0]}</p></td>
                            <td></td>
                            <td><p class="has-text-white has-text-centered has-text-weight-bold">{defenseur[1]}</p></td>
                        </tr>
                        <tr>
                            <td class="has-text-weight-bold has-background-white">Gardien</td>
                            <td></td>
                            <td><p class="has-background-warning has-text-white has-text-centered has-text-weight-bold">{gardien}</p></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            )
        }
    }

    tabListJoueur(){
        return(
            <table class="table is-fullwidth is-narrow">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Poste de prédilection</th>
                        <th>Note</th>
                        <th>Âge</th>
                        <th>Durée contrat</th>
                    </tr>
                </thead>
                <tbody>
                    {this.state.mesJoueurs.map(joueur => (
                    <tr key={joueur.id}>
                        <td>{joueur.nom} {joueur.prenom}</td>
                        <td>{joueur.postePredilection}</td>
                        <td>{joueur.noteInstantanee}</td>
                        <td>{joueur.age}</td>
                        {joueur.dureeAnneesContrat<=1 ?
                        <td class="has-background-danger">{joueur.dureeAnneesContrat} ans</td>
                        :<td>{joueur.dureeAnneesContrat} ans</td>}
                    </tr>
                    ))}
                </tbody>
            </table>
        )
    }

    renduLancerMatch = () => {
        if(this.state.journee.quartTemps==='Fin des match'){
                return(
                    <div class="panel-block">
                        <button class={this.state.btn} onClick={()=>this.finMatch()}>Journée suivante</button>
                    </div>
                )
            }
            return(  
                <div class="panel-block">
                    <button class={this.state.btn} onClick={()=>this.jouerQuartTemps()}>Jouer quart-temps n°{this.state.journee.quartTemps+1}</button>
                    <button class={this.state.btn} onClick={()=>this.jouerMatch()}>Jouer match</button>
                    <button class={this.state.btn} onClick={()=>this.jouerSaison()}>Jouer saison</button>
                </div>
            )
    }

    renduQuartTemps(){
        if(this.state.journee.quartTemps<4){
            return(
                <p class="panel-block has-text-weight-bold">
                    Quart-temps n°{this.state.journee.quartTemps+1}
                </p>
            )
        }
        return(
            <p class="panel-block has-text-weight-bold">
                {this.state.journee.quartTemps}
            </p>
        )
    }

    rendu(){
        if(this.state.finCompet){
            return(
                <FinCompetition/>
            )
        }else{
        let i = 0
        return(
        <div>       
            <nav class="level">
                <div class="title level-item is-1 has-text-black has-text-weight-bold has-text-centered">
                    {this.state.monClub.nomClub}
                </div>
                <div class="level-item has-text-centered has-text-black has-text-weight-bold">
                    <p>Note : {this.state.monClub.noteInstantannee}</p>
                </div>
                <div class="level-item has-text-centered has-text-black has-text-weight-bold">
                    <p>Budget : {this.state.monClub.budget}€</p>
                </div>
                <div class="level-item has-text-centered has-text-black has-text-weight-bold">
                    <p>Réputation : {this.state.monClub.reputation}</p>
                </div>
            </nav>
            <div class="columns is-multiline">
                <div class="column">
                    <article class="panel panel-block-hover-background-color">
                    <h1 class="title panel-heading">{this.state.nomCompet.nomCompetition}</h1>
                    <div class="panel-block table-container">
                    <table class="table is-fullwidth is-narrow">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>Pts</th>
                                <th>G.</th>
                                <th>N.</th>
                                <th>P.</th>
                            </tr>
                        </thead>
                        <tbody>
                        {this.state.classement.map(club => {
                            i++
                            if(club.nomClub===this.state.monClub.nomClub){
                                return(
                                    <tr class="is-selected" key={club.nomClub}>
                                        <td>{i}</td>
                                        <td>{club.nomClub}</td>
                                        <td>{club.points}</td>
                                        <td>{club.nbVictoires}</td>
                                        <td>{club.nbNuls}</td>
                                        <td>{club.nbDefaites}</td>
                                    </tr>
                                )
                            }else{
                                return(
                                    <tr key={club.nomClub}>
                                        <th>{i}</th>
                                        <td>{club.nomClub}</td>
                                        <td>{club.points}</td>
                                        <td>{club.nbVictoires}</td>
                                        <td>{club.nbNuls}</td>
                                        <td>{club.nbDefaites}</td>
                                    </tr>
                                )
                            }
                        })}
                        </tbody>
                    </table>
                    </div>
                    </article>
                </div>
                <div class="column">
                <article class="panel panel-block-hover-background-color">
                    <h1 class="title panel-heading">Matches</h1>
                    <p class="panel-block has-text-centered has-text-weight-bold">
                        Jour {this.state.journee.jour} sur {this.state.journee.jourMax}
                    </p>
                    {this.renduQuartTemps()}
                    <div class="panel-block table-container">
                    <table class="table is-fullwidth is-narrow">
                        <thead>
                            <tr>
                                <th class="has-text-right">Club Domicile</th>
                                <th class="has-text-centered">Score</th>
                                <th class="has-text-left">Club Exterieur</th>
                                <th class="has-text-centered">Lieu</th>
                                <th class="has-text-centered">Recette</th>
                            </tr>
                        </thead>
                        <tbody>
                        {this.state.matches.map(match => {
                            i++
                            if(match.date===this.state.journee.jour){
                                if(match.nomClubDomicile===this.state.monClub.nomClub || match.nomClubExterieur===this.state.monClub.nomClub){
                                    return(
                                        <tr class="is-selected" key={match.id}>
                                            <td class="has-text-right" onClick={()=>this.togglePopup(match.idClubDomicile)}>{match.nomClubDomicile}</td>
                                            <td class="has-text-centered">{match.nbButDomicile} - {match.nbButExterieur}</td>
                                            <td class="has-text-left" onClick={()=>this.togglePopup(match.idClubExterieur)}>{match.nomClubExterieur}</td>
                                            <td class="has-text-centered">{match.stadeDomicile}</td>
                                            <td class="has-text-centered">{match.recette}€</td>
                                        </tr>
                                    )
                                }else{
                                    return(
                                        <tr key={match.id}>
                                            <td class="has-text-right" onClick={()=>this.togglePopup(match.idClubDomicile)}>{match.nomClubDomicile}</td>
                                            <td class="has-text-centered">{match.nbButDomicile} - {match.nbButExterieur}</td>
                                            <td class="has-text-left" onClick={()=>this.togglePopup(match.idClubExterieur)}>{match.nomClubExterieur}</td>
                                            <td class="has-text-centered">{match.stadeDomicile}</td>
                                            <td class="has-text-centered">{match.recette}€</td>
                                        </tr>
                                    )
                                }
                            }else{
                                return null
                            }
                        })}
                        </tbody>
                    </table>
                    </div>
                    {this.renduLancerMatch()}
                    </article>
                </div>
            </div>
            <div class="columns is-multiline">
                <div class="column">
                    <article class="panel panel-block-hover-background-color">
                        <h1 class="title panel-heading">
                            Mes joueurs
                        </h1>
                        <div class="panel-block table-container">
                        {this.tabListJoueur()}
                        </div>
                    </article>
                </div>
                <div class="column">
                    <article class="panel panel-block-hover-background-color">
                        <h1 class="title panel-heading">
                            Tactique sur le terrain
                        </h1>
                        <article class="panel is-info panel-block-hover-background-color">
                        <div class="columns panel-block ">
                            <p class="column has-text-centered has-text-weight-bold">
                                Formation en {this.state.monClub.formation}
                            </p>
                            <p class="column has-text-centered has-text-weight-bold">
                                Note de la formation : {this.state.monClub.noteFormation}
                            </p>
                        </div>
                        <div class="panel-block table-container">
                        {this.tabJoueurTerrain()}
                        </div>
                        <div class="panel-block">
                            <button class="button is-primary is-outlined is-fullwidth" onClick={()=>this.changerTactiquePopup()}>Changer de Stratégie</button>
                        </div>
                        </article>
                    </article>
                </div>
            </div>
            {this.state.showPopup ?  
            <Modal visible={this.state.showPopup} onClickAway={() => this.closeTogglePopup()}>
                <div class="box">
                    <article class="media">
                        <div class="media-content">
                            <h1 class="title">{this.state.infoClub.nomClub}</h1>
                            <h1 class="subtitle">Note : {this.state.infoClub.noteClub}</h1>
                            <div class="table-container">
                                <table class="table is-fullwidth is-narrow">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Poste</th>
                                            <th>Note</th>
                                            <th>Âge</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {this.state.infoClub.joueurs.map(joueur => (
                                        <tr key={joueur.id}>
                                            <td>{joueur.nom} {joueur.prenom}</td>
                                            <td>{joueur.postePredilection}</td>
                                            <td>{joueur.noteInstantanee}</td>
                                            <td>{joueur.age}</td>
                                        </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </article>
                </div>                    
            </Modal>
            : null  
            } 
            {this.state.changerTactique ?  
            <Modal visible={this.state.changerTactique} onClickAway={() => this.closeChangerTactiquePopup()}>
                <div class="box">
                    <article class="media">
                        <div class="media-content">
                            <h1 class="title">Changement de Stratégie</h1>
                            <div class="field">
                                {this.changerTactique()}
                            </div>
                        </div>
                    </article>
                </div>                    
            </Modal>
            : null  
            }  
            </div>
        )
        }
    }

    render(){
        return this.rendu()
    }
}

export default Competition