<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class device extends Model
{
    use HasFactory;

    public function getmanytone(){
        return $this->belongTo('App\Models\employee');

    }
    public $timestamps = false;
}
   