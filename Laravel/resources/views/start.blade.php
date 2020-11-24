@extends('layout')

@section('content')
<section class="section">
    <div class="container">
        <div class="columns is-mobile is-centered has-text-centered">
            <div class="column is-half">
                <div class="columns ">
                    <div class="column ">
                        <h1 class="title is-1">Football Manager</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="columns is-desktop has-text-centered">
            <div class="column">
                <div class="box">
                    <h1 class="title">Le club</h1>   
                    <div class="columns is-desktop">
                        <div class="column">
                            <h1 class="title is-4">Choisissez votre club</h1>
                            <form action="/start" method="POST" class="box">
                                @csrf
                                <table class="table is-fullwidth is-narrow">
                                    <thead>
                                        <tr>
                                            <th>Choix</th>
                                            <th>Club</th>
                                            <th>Ville</th>
                                            <th>Stade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($clubs as $club)
                                        <tr>
                                            <td>
                                                <label class="radio">
                                                  <input type="radio" name="nomClub"  value="{{ $club->nomClub}}">
                                                </label>
                                            </td>
                                            <td>{{ $club->nomClub}}</td>
                                            <td>{{ $club->nomVille}}</td>
                                            <td>{{ $club->nomStade}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="field is-grouped is-grouped-centered">
                                    <div class="control">
                                        <input type="submit" name="validerClub" class="button is-warning" value="Selectionner ce club">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="column">
                            <h1 class="title is-4">Ou créez-le</h1>
                            <form action="/start" method="POST" class="box">
                                @csrf
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Club</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <input class="input" type="text" placeholder="Nom du club" name="nomClub">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Ville</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <input class="input" type="text" placeholder="Nom de la ville" name="nomVille">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="field is-horizontal">
                                    <div class="field-label is-normal">
                                        <label class="label">Stade</label>
                                    </div>
                                    <div class="field-body">
                                        <div class="field">
                                            <p class="control">
                                                <input class="input" type="text" placeholder="Nom du stade" name="nomStade">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="field is-grouped is-grouped-centered">
                                    <div class="control">
                                        <input type="submit" name="validerNewClub" class="button is-warning" value="Créer et sélectionner ce nouveau club">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="columns is-desktop has-text-centered">
            <div class="column">
                <div class="box">
                    <h1 class="title">Les joueurs</h1>    
                    <div class="columns is-desktop">
                        <div class="column">
                            <h1 class="title is-4">Selectionnez des joueurs</h1>
                            <form>
                                <table class="table is-fullwidth is-narrow">
                                    <thead>
                                        <tr>
                                            <th>Choix</th>
                                            <th>Joueur</th>
                                            <th>Age</th>
                                            <th>Poste de prédilection</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($joueurs as $joueur)
                                        <tr>
                                            <td>
                                                <label class="radio">
                                                  <input type="radio" name="idJoueur"  value="{{ $joueur->id}}">
                                                </label>
                                            </td>
                                            <td>{{ $joueur->nom}} {{ $joueur->prenom}}</td>
                                            <td>{{ $joueur->age}}</td>
                                            <td>{{ $joueur->postePredilection}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>                            
                            </form>
                        </div>
                        <div class="column">
                            <h1 class="title is-4">ou créez-en</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection