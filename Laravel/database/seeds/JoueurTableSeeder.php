<?php

use Illuminate\Database\Seeder;

class JoueurTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(LinksTableSeeder::class);
    }
}
