<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employee extends Model
{
    public $table = 'employee';
    use HasFactory;

    function getttrelation(){
        return $this->hasMany('App\Models\device');
    }

}
