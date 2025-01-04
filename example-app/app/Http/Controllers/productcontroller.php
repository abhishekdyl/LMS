<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\device;


class productcontroller extends Controller
{
    
    function getproduct($id){
        $product = device::all();
        return view('products',['prodata'=>$product]);
    }

    function addproduct($id){
        return $id;

    }

}
