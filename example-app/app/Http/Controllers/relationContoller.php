<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\employee;
use App\Models\device;

class relationContoller extends Controller
{
    function otmanyrelation(){
        // return employee::find(1);

        return employee::find(1)->getttrelation; // this function is initialise in employee model
    }


    // many to one relation 
    function manytonerelation(){
        return device::all();
        // return device::with('getmanytone')->get(); // this function works with laravel11
    }


}
 