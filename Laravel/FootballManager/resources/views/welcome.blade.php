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
                    <div class="columns ">
                        <div class="column">
                            <form action="/" method="POST">
                                @csrf
                                <div class="field is-grouped is-grouped-centered">
                                    <div class="control">
                                        <input type="submit" name="validerClub" class="button is-large is-warning" value="Jouer">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
            </div>
        </div>
        <div class="columns is-desktop has-text-centered ">
            <div class="column">
                <div class="box">
                    <h1 class="title">Liste des clubs générés</h1>
                        <table class="table is-fullwidth">
                            <thead>
                                <tr>
                                    <th>Club</th>
                                    <th>Ville</th>
                                    <th>Stade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clubs as $club)
                                <tr>
                                    <td>{{ $club->nomClub}}</td>
                                    <td>{{ $club->nomVille}}</td>
                                    <td>{{ $club->nomStade}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
            <div class="column">
                <div class="box">
                    <h1 class="title">Liste des joueurs générés</h1>
                        <table class="table is-fullwidth is-narrow">
                            <thead>
                                <tr>
                                    <th>Joueur</th>
                                    <th>Age</th>
                                    <th>Poste de prédilection</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($joueurs as $joueur)
                                <tr>
                                    <td>{{ $joueur->nom}} {{ $joueur->prenom}}</td>
                                    <td>{{ $joueur->age}}</td>
                                    <td>{{ $joueur->postePredilection}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection