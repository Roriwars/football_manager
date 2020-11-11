<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('nomClub');
            $table->unique('nomClub');
            $table->integer('reputation');
            $table->integer('budget');
            $table->string('formation');
            $table->integer('noteAbsolue');
            $table->integer('noteFormation');
            $table->integer('noteAttaque')->default(0);
            $table->integer('noteDefense')->default(0);
            $table->integer('noteInstantannee');//noteEnCours2Jeu
            $table->string('nomVille')->nullable($value=true);
            $table->string('nomStade')->nullable($value=true);
            $table->string('isMain')->nullable($value=true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clubs');
    }
}
