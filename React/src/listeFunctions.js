import axios from 'axios'

export const getClub = () => {
    return axios
        .get(
            'http://footballmanager.test/api/clubs', {
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => {
            return res.data
        })
}

export const initClub = () => {
    return axios
        .get(
            'http://footballmanager.test/api/initClubs', {
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => {
            return res.data
        })
}

export const selectClub = (idClub) =>{
    return axios
        .put(
            'http://footballmanager.test/api/chooseClub/'+idClub,
            {
                isMain: true
            },
            {
                headers: { 'Content-Type': 'application/json' }
            }
        )
        .then(function(response){
            console.log(response)
    })
}

export const selectJoueur = (idJoueur,idClub,prixJoueur,dureeContrat) =>{
    return axios
        .post(
            'http://footballmanager.test/api/creerContrat',
            {
                idJoueur: idJoueur,
                idClub: idClub,
                prixJoueur: prixJoueur,
                dureeContrat: dureeContrat
            },
            {
                headers: { 'Content-Type': 'application/json' }
            }
        )
        .then(function(response){
            console.log(response)
    })
}

export const vendreJoueur = (idJoueur,prixJoueur,idClub) =>{
    return axios
        .post(
            'http://footballmanager.test/api/vendreJoueur',
            {
                idJoueur: idJoueur,
                idClub: idClub,
                prixJoueur: prixJoueur
            },
            {
                headers: { 'Content-Type': 'application/json' }
            }
        )
        .then(function(response){
            console.log(response)
    })
}

export const getJoueurContrat = (idClub) =>{
    return axios
        .get(
            "http://footballmanager.test/api/joueurContrat/"+idClub, {
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => {
                return res.data
            })
}

export const getJoueurSansContrat = () => {
    return axios   
        .get(
            "http://footballmanager.test/api/joueurSansContrat", {
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => {
                return res.data
            })
}

export const remplirClubJoueur = () => {
    return axios   
        .get(
            "http://footballmanager.test/api/remplirClubJoueur", {
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => {
                return res.data
            })
}

export const isMainClub = () => {
    return axios   
        .get(
            "http://footballmanager.test/api/isMainClub", {
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => {
                return res.data
            })
}

export const monClub = () =>{
    return axios
        .get("http://footballmanager.test/api/monClub", {
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => {
            return res.data
        })
}

export const mesJoueurs = () =>{
    return axios
        .get("http://footballmanager.test/api/mesJoueurs", {
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => {
            return res.data
        })
}

export const initCompet = () =>{
    return axios
        .get("http://footballmanager.test/api/initCompet", {
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => {
            return res.data
        })
}

export const getCompet = () =>{
    return axios
        .get("http://footballmanager.test/api/getCompet", {
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => {
            return res.data
        })
}

export const getClassement = (idCompet) =>{
    return axios
        .get("http://footballmanager.test/api/getClassement/"+idCompet, {
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => {
            return res.data
        })
}

export const getMatches = (idCompet,jour) =>{
    return axios
        .post("http://footballmanager.test/api/getMatches", 
        {
            idCompetition: idCompet,
            jour: jour
        },{
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => {
            return res.data
        })
}

export const getJour = (idCompet) =>{
    return axios
        .get("http://footballmanager.test/api/getJour/"+idCompet, 
        {
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => {
            return res.data
        })
}

export const jouerQuartTemps = (idCompet,jour) =>{
    return axios
        .post(
            'http://footballmanager.test/api/jouerQuartTemps',
            {
                idCompetition: idCompet,
                jour: jour
            },
            {
                headers: { 'Content-Type': 'application/json' }
            }
        )
        .then(function(response){
            console.log(response)
    })
}

export const jouerMatch = (idCompet,jour) =>{
    return axios
        .post(
            'http://footballmanager.test/api/jouerMatch',
            {
                idCompetition: idCompet,
                jour: jour
            },
            {
                headers: { 'Content-Type': 'application/json' }
            }
        )
        .then(function(response){
            console.log(response)
    })
}

export const jouerSaison = (idCompet) =>{
    return axios
        .post(
            'http://footballmanager.test/api/jouerSaison',
            {
                idCompetition: idCompet
            },
            {
                headers: { 'Content-Type': 'application/json' }
            }
        )
        .then(function(response){
            console.log(response)
    })
}

export const finMatch = (idCompet,jour) =>{
    return axios
        .post(
            'http://footballmanager.test/api/finMatch',
            {
                idCompetition: idCompet,
                jour: jour
            },
            {
                headers: { 'Content-Type': 'application/json' }
            }
        )
        .then(function(response){
            console.log(response)
    })
}

export const finCompet = () =>{
    return axios
        .get(
            'http://footballmanager.test/api/finCompet',
            {
                headers: { 'Content-Type': 'application/json' }
            }
        )
        .then(function(response){
            console.log(response)
    })
}

export const getInfoClub = (idClub) =>{
    return axios
        .post(
            'http://footballmanager.test/api/getInfoClub',
            {
                idClub:idClub
            },
            {
                headers: { 'Content-Type': 'application/json' }
            }
        )
        .then(res => {
            return res.data
        })
}

export const newTactique = (joueurs,formation) =>{
    return axios
        .post(
            'http://footballmanager.test/api/newTactique',
            {
                joueurs:joueurs,
                formation:formation
            },
            {
                headers: { 'Content-Type': 'application/json' }
            }
        )
}

export const saisonSuivante = () =>{
    return axios
        .post(
            'http://footballmanager.test/api/saisonSuivante',
            {
                headers: { 'Content-Type': 'application/json' }
            }
        )
}