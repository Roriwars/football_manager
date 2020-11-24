<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    public $timestamps = false;
    protected $fillable = ['nomClub','reputation','budget','formation','noteAbsolue','noteFormation','noteInstantannee','isMain'];
}
