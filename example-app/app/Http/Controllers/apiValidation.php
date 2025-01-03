<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\device;
use Validator;

class apiValidation extends Controller
{
    function validation(Request $req){
        $rules = array(
            'name'=>'required',
            'store'=>'required',
            'price'=>'required | max:5',
            'employee_id'=>'required | max:1'

        );
        $validator = Validator::make($req->all(),$rules);
        if($validator->fails()){
            return response()->json($validator->errors(),404) ;
        }else{
            $obj = new device;
            $obj->name = $req->name;
            $obj->store = $req->store;
            $obj->price = $req->price;
            $obj->employee_id = $req->employee_id;
            $result = $obj->save();
            if($result){
                return ['a'=>'Success'];
            }else{
                return ['b'=>'Failed'];
            }

        }
    }
}
 