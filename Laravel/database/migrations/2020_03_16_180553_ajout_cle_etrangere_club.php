<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AjoutCleEtrangereClub extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->foreign('nomVille')->references('nomVille')->on('villes');
            $table->foreign('nomStade')->references('nomStade')->on('stades');
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->foreign('idJoueur')->references('id')->on('joueurs');
            $table->foreign('idClub')->references('id')->on('clubs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clubs', function (Blueprint $table) {
            //
        });
    }
}
