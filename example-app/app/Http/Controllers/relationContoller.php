<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\employee;

class relationContoller extends Controller
{
    function otmanyrelation(){
        return employee::find(2)->getttrelation;
    }
}
 