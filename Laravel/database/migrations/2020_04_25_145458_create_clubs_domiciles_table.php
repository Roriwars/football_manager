<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubsDomicilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_domiciles', function (Blueprint $table) {
            $table->integer('idClub');
            $table->integer('idMatch');
            $table->integer('nbBut')->default(0);
            $table->foreign('idClub')->references('id')->on('clubs');
            $table->foreign('idMatch')->references('id')->on('matches');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('club_domiciles');
    }
}
