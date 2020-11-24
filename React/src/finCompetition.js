import React, {Component} from 'react';
import {mesJoueurs, monClub, saisonSuivante, vendreJoueur, getJoueurSansContrat, selectJoueur, getCompet, getClassement} from './listeFunctions'

class FinCompetition extends Component{
    constructor(){
        super()
        this.state={
            nomCompet:{},
            classement:[],
            mesJoueurs:[],
            monClub:{},
            joueursLibre:[]
        }
    }

    componentDidMount(){
        this.getCompet()
        this.getJoueur()
        this.getJoueurSansContrat()
        this.monClub()
    }

    monClub= () => {
        monClub().then(data => {
          let club=[...data]
          this.setState(
            {
              monClub:club[0],
            }
          )
          console.log(this.state.monClub);
        })
    }

    getCompet = () => {
        getCompet().then(data => {
            this.setState(
                {
                    nomCompet:data
                }
            )
            this.getClassement()
        })
    }

    getClassement = () => {
        getClassement(this.state.nomCompet.id,).then(data => {
            this.setState(
                {
                    classement:[...data]
                }
            )
        })
    }

    getJoueur = () => {
        mesJoueurs().then(data =>{
            this.setState(
                {
                    mesJoueurs:[...data]
                },
                () => {
                    console.log(this.state.mesJoueurs)
                }
            )
        })
    }

    selectJoueur = (e,a,p,d) => {
        if((this.state.monClub.budget-p)>0){
            selectJoueur(e,a,p,d).then(() => {
                this.getJoueurSansContrat();
                this.getJoueur()
                this.monClub()
            })
        }else{
            alert("Vous n'avez pas assez de sous")
        }
    }

    vendreJoueur(j,p,c){
        vendreJoueur(j,p,c).then(() => {
            this.getJoueurSansContrat();
            this.getJoueur()
            this.monClub()
        })
    }

    tabJoueur(){
        return(
            <table class="table is-fullwidth is-narrow">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Poste</th>
                        <th>Note</th>
                        <th>Âge</th>
                        <th>Prix</th>
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
                        <td>{joueur.prix}</td>
                        {joueur.dureeAnneesContrat<=1 ?
                        <td class="has-background-danger">{joueur.dureeAnneesContrat} ans <button class="button is-small" onClick={()=>this.vendreJoueur(joueur.id,joueur.prix,this.state.monClub.id)}>Vendre</button></td>
                        :<td>{joueur.dureeAnneesContrat} ans <button class="button is-small" onClick={()=>this.vendreJoueur(joueur.id,joueur.prix,this.state.monClub.id)}>Vendre</button></td>}
                    </tr>
                    ))}
                </tbody>
            </table>
        )
    }

    getJoueurSansContrat = () =>{
        getJoueurSansContrat().then(data => {
            this.setState(
                {
                    joueursLibre:[...data]
                },
                () => {
                    console.log(this.state.joueursLibre)
                }
            )
        })
    }

    choisirJoueur(){
        return (
            <div>
                <h1 class="subtitle is-3 has-text-centered has-text-white">Marché des Joueurs</h1>
                <h1 class="subtitle is-6 has-text-centered has-text-white">Achetez un joueur pour une durée de 3 ans</h1>
                <div class="columns is-variable is-destop is-multiline">
                <div class="column">{this.afficherJoueurs("Gardien")}</div>
                <div class="column">{this.afficherJoueurs("Defenseur")}</div>
                <div class="column">{this.afficherJoueurs("Milieu")}</div>
                <div class="column">{this.afficherJoueurs("Attaquant")}</div>
                </div>
            </div>
        );
    }

    afficherJoueurs(poste){
        return(
            <article class="panel panel-block-hover-background-color">
                <p class="panel-heading">
                    {poste}
                </p>
                <div class="panel-block table-container">
                    <table class="table is-fullwidth is-narrow">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Âge</th>
                                        <th>Note</th>
                                        <th>Prix</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {this.state.joueursLibre.map(joueur => {
                                        return( joueur.postePredilection === poste ?
                                        <tr key={joueur.id} onClick={()=>this.selectJoueur(joueur.id,this.state.monClub.id,joueur.prix,3)}>
                                            <td>{joueur.nom} {joueur.prenom}</td>
                                            <td>{joueur.age}</td>
                                            <td>{joueur.noteGlobale}</td>
                                            <td>{joueur.prix}€</td>
                                            {/*<td><input type="number" value={this.state.value} min="1" onChange={(event,joueur)=>{
                                                let joueurs=new Array(this.state.joueursLibre.length)
                                                joueurs=this.state.joueurLibre
                                                for( var k = 0; k < joueurs.length; ++k ) {
                                                    if( joueur.id === joueurs[k]["id"] ) {
                                                        joueurs[k]["dureeAnneesContrat"] = event.target.value ;
                                                    }
                                                }
                                                this.setState({
                                                    joueursLibre:this.state.joueursLibre
                                                })
                                                console.log(this.state.joueursLibre)
                                            }}/></td>*/}
                                        </tr>
                                        : null )}
                                    )}
                                </tbody>
                            </table>
                        </div>
            </article>
        )
    }

    saisonSuivante(){
        if(this.state.mesJoueurs.length>=5){
            saisonSuivante().then(
                window.location=""
            )
        }else{
            alert("Vous n'avez pas assez de joueurs pour passer à la saison suivante")
        }
    }

    render(){
        let i = 0
        return (
        <div>
            <div class="columns is-multiline">
                <div class="column">
                    <h1 class="title is-1 is-spaced has-text-left has-text-black">Fin de la saison</h1>
                </div>
                <div class="column">
                    <div class="buttons is-right">
                        <button class="button is-large is-danger" onClick={()=>this.saisonSuivante()}>Saison suivante</button>
                    </div>
                </div>
            </div>
            <div class="columns is-multiline">
                <div class="column">
                    <article class="panel panel-block-hover-background-color">
                    <h1 class="title panel-heading">Classement final de {this.state.nomCompet.nomCompetition}</h1>
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
            </div>
            <div class="columns is-multiline">
                <div class="column">
                    <article class="panel panel-block-hover-background-color">
                        <p class="panel-heading">
                            Votre Club
                        </p>
                        <div class="panel-block columns">
                                <div class="column is-half">
                                    <h1 class="title ">
                                        {this.state.monClub.nomClub}
                                    </h1>
                                </div>
                                <div class="column">
                                    <h1 class="subtitle">
                                        note: {this.state.monClub.noteAbsolue}
                                    </h1>
                                </div>
                                <div class="column">
                                    <h1 class="subtitle">
                                        Réputation: {this.state.monClub.reputation}
                                    </h1>
                                </div>
                                <div class="column">
                                    <h1 class="subtitle ">
                                        Budget: {this.state.monClub.budget}€
                                    </h1>
                                </div>
                        </div>
                    </article>
                </div>
            </div>
            <div class="columns is-multiline">
                <div class="column">
                    <article class="panel panel-block-hover-background-color">
                        <h1 class="title panel-heading">
                            Liste de vos joueurs
                        </h1>
                        <div class="panel-block table-container">
                        {this.tabJoueur()}
                        </div>
                    </article>
                </div>
            </div>
            <div class="columns is-multiline">
                {this.choisirJoueur()}
            </div>
        </div>
        )
    }
}

export default FinCompetition