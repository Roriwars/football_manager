import React from 'react';
import {getJour, getCompet, isMainClub} from './listeFunctions'
import GenererClub from './genererClub'
import Competition from './competition'

class App extends React.Component{
  constructor(){
    super()
    this.state={
      mainClub:false,
      monClub:{},
      compet:false,
      nomCompet:{}
    }
  }
  
  componentDidMount(){
    this.isMainClub();
    this.getCompet();
  }

  isMainClub = () => {
    isMainClub().then(data => {
        if(data===1){
            this.setState(
            {
                mainClub:true
            },
            ()=>{
                console.log('isMain : '+this.state.mainClub);
            }
            )
        }else{
            this.setState(
                {
                    mainClub:false,
                    monClub:null
                },
                ()=>{
                    console.log('isMain : '+this.state.mainClub);
                }
            )
        }
    })
  }

  getJour = () => {
    getJour(this.state.nomCompet.id).then(data => {
        if(data==='vide'){
          this.setState(
            {
                compet:false
            }
        )
        }else{
            this.setState(
                {
                    compet:true
                }
            )
        }
    })
  }

  getCompet = () => {
    getCompet().then(data => {
        this.setState(
            {
                nomCompet:data
            }
        )
        this.getJour()
    })
}

  resetJeu(){
    this.setState(
      {
        mainClub:false
      }
    )
  }

  rendu(){
    if(!this.state.mainClub){
      return(
        <div>
          <h1 class="title is-1 is-spaced has-text-centered has-text-black">Football Manager</h1>                  
          <GenererClub/>
        </div>
      )
    }else{
      return(
        <div>

            <Competition/>
          
            <div class="buttons is-right">
              <button class="button is-warning is-rounded" onClick={()=>this.resetJeu()}>Reset jeu</button>
            </div>
        </div>
      )
    }
  }

  render(){
      return(
        this.rendu()
      )
    
  }
}

export default App