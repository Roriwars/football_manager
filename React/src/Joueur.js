import React, {Component} from 'react'
import {getJoueurContrat} from './listeFunctions'

class Joueur extends Component{
    constructor(props){
      super(props)
      this.state={
        joueurs:[]
      }
    }

    componentDidMount(){
        this.getAllJoueur();
    }
    
    componentDidUpdate(prevProps){
        if(this.props.idClub!==prevProps.idClub){
            this.getAllJoueur();        
        }
    }

    getAllJoueur = () => {
        var idClub = this.props.idClub;
        getJoueurContrat(idClub).then(data =>{
            this.setState(
                {
                    joueurs:[...data],
                },
                () => {
                    console.log(this.state.joueurs)
                }
            )
        })
    }
    
    render(){
        return(
            <table class="table is-fullwidth is-narrow">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Âge</th>
                        <th>Note</th>
                        <th>Poste</th>
                    </tr>
                </thead>
                <tbody>
                    {this.state.joueurs.map(joueur => (
                        <tr key={joueur.id}>
                            <td>{joueur.nom}</td>
                            <td>{joueur.prenom}</td>
                            <td>{joueur.age}</td>
                            <td>{joueur.noteGlobale}</td>
                            <td>{joueur.postePredilection}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
       )
     }
  }

  export default Joueur