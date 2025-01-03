<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\employee;

class getApiContoller extends Controller
{
    function firstgetapi($id = NULL){
        if(empty($id)){
            return employee::all();
        }else{
            return employee::find($id);
        }
    }
}
 