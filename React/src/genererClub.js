import React, {Component} from 'react'
import {getClub, initClub, selectClub, selectJoueur, getJoueurSansContrat, initCompet} from './listeFunctions'
import Joueur from './Joueur'

class GenererClub extends Component{
    constructor(){
        super()
        this.state={
            monClub:"",
            clubs:[],
            joueursLibre:[],
            showClubs:false,
            showJoueursLibre:false,
            btn:"button is-danger is-rounded is-large",
            nbRemplaçant:0
        }
    }

    initClub = () => {
        this.setState(
            {
                showClubs:true,
                showJoueursLibre:false,
                btn:"button is-danger is-rounded is-large is-loading"
            });
        initClub().then(() =>{
            this.getAll();
        })
    }

    getAll = () => {
        getClub().then(data => {
            this.setState(
                {
                    clubs:[...data],
                    showClubs:true,
                    btn:"button is-danger is-rounded is-large"
                },
                () => {
                    console.log(this.state.clubs)
                }
            )
        })
    }

    getJoueurSansContrat = () =>{
        getJoueurSansContrat().then(data => {
            this.setState(
                {
                    joueursLibre:[...data],
                    btn:"button is-danger is-rounded is-large"
                },
                () => {
                    console.log(this.state.joueursLibre)
                }
            )
        })
    }

    selectClub = e => {
        console.log(e)
        this.setState(
            {
                monClub:e,
                clubs:[],
                showClubs:false,
                showJoueursLibre:true,
                btn:"button is-danger is-rounded is-large is-loading"
            }
        )
        this.getJoueurSansContrat();
        console.log('mon club:'+this.state.monClub);
    }

    selectJoueur = (e,a,p,d) => {
        selectJoueur(e,a,p,d).then(() => {
            this.setState(
                {
                    btn:"button is-danger is-rounded is-large is-loading",
                    nbRemplaçant:this.state.nbRemplaçant+1
                }
            )
            this.getJoueurSansContrat();
            console.log(this.state.nbRemplaçant);
            if(this.state.nbRemplaçant>=3){
                selectClub(a).then(() =>{
                    initCompet().then(()=>{
                        window.location="";
                    });
                });
            }
        })
    }

    choisirClubs(){
        if(!this.state.showClubs){
            return null;
        }

        return (
            <div>
                <h1 class="subtitle is-4 has-text-centered has-text-white">sélectionnez votre équipes</h1>
                {this.state.clubs.map(club => (
                <article class="panel is-warning panel-block-hover-background-color">
                    <p class="panel-heading">
                        {club.nomClub} ({club.noteAbsolue} points)
                    </p>
                    <div class="panel-block">
                        <Joueur idClub={club.id}/>
                    </div>
                    <div class="panel-block">
                        <button class="button is-fullwidth is-success is-outlined" onClick={()=>this.selectClub(club.id)}>
                            Selectionner ce club
                        </button>
                    </div>
                </article>
                ))}
            </div>
        );
    }

    choisirRemplacant(){
        if(!this.state.showJoueursLibre){
            return null;
        }

        return (
            <div>
                <h1 class="subtitle is-4 has-text-centered has-text-white">sélectionnez 3 remplaçants</h1>
                <div class="columns is-multiline is-variable is-destop">
                <div class="column">{this.afficherRemplaçant("Gardien")}</div>
                <div class="column">{this.afficherRemplaçant("Defenseur")}</div>
                <div class="column">{this.afficherRemplaçant("Milieu")}</div>
                <div class="column">{this.afficherRemplaçant("Attaquant")}</div>
                </div>
            </div>
        );
    }

    afficherRemplaçant(poste){
        return(
            <article class="panel is-warning panel-block-hover-background-color">
                <p class="panel-heading">
                    {poste}
                </p>
                <div class="panel-block">
                    <table class="table long-tab is-fullwidth is-narrow">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Âge</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {this.state.joueursLibre.map(joueur => {
                                        return( joueur.postePredilection === poste ?
                                        <tr key={joueur.id} onClick={()=>this.selectJoueur(joueur.id,this.state.monClub,20,3)}>
                                            <td>{joueur.nom}</td>
                                            <td>{joueur.prenom}</td>
                                            <td>{joueur.age}</td>
                                            <td>{joueur.noteGlobale}</td>
                                        </tr>
                                        : null )}
                                    )}
                                </tbody>
                            </table>
                        </div>
            </article>
        )
    }

    render() {
        return (
            <div>
            {this.choisirClubs()}
            {this.choisirRemplacant()}
                <div class="columns is-mobile is-centered has-text-centered">
                    <div class="column">
                        <button class={this.state.btn} onClick={()=>{this.initClub();}} >
                            Générer les nouveaux clubs !
                        </button>
                    </div>
                </div>
            </div>
        );
    }
}

export default GenererClub