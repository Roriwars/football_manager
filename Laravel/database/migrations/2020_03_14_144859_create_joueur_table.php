<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJoueurTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joueurs', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nom');
            $table->string('prenom');
            $table->integer('age');
            $table->integer('noteGardien');
            $table->integer('noteDefense');
            $table->integer('noteMilieu');
            $table->integer('noteAttaque');
            $table->integer('tir');
            $table->integer('passe');
            $table->integer('technique');
            $table->integer('placement');
            $table->integer('vitesse');
            $table->integer('tacle');
            $table->integer('arret');
            $table->integer('forme');
            $table->integer('endurance');
            $table->string('postePredilection');
            $table->integer('prix');
            $table->integer('noteGlobale');
            $table->integer('noteInstantanee');
            $table->string('joue')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('joueur');
    }
}
