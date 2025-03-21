<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class createuser extends Model
{
    public $table = 'users';
    public $timestamps = false;
    // public $table = 'userlist';
    // public $timestamps = false;
 
    use HasFactory;
} 
 