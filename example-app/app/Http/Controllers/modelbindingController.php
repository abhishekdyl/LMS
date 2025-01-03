<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\device;
use Illuminate\Support\Facades\DB;

class modelbindingController extends Controller
{
    function mbinding(device $keyname){
        // return $key->all();
        return $keyname;
    }


    function tablejoin(){

        return DB::table('devices')
        ->join('employee','employee.id','=','devices.employee_id')
        ->get();        
    }
}
 