<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classements', function (Blueprint $table) {
            $table->integer('idClub');
            $table->integer('idCompetition');
            $table->integer('points')->default(0);
            $table->integer('nbVictoires')->default(0);
            $table->integer('nbDefaites')->default(0);
            $table->integer('nbNuls')->default(0);
            $table->foreign('idCompetition')->references('id')->on('competitions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classements');
    }
}
