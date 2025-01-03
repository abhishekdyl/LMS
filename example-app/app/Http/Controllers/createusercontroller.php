<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\createuser;

class createusercontroller extends Controller
{ 

    public function __construct(){
        $this->middleware('auth');
    }

    // function require_login(){
    //     //$userid = Auth::user()->id;
    //     if(!empty(Auth::user()->id)){
    //         $this->createuser();
    //     }else{
    //         // return view('login'); 
    //         return redirect('/');
    //     }
    // }

    function createuser(Request $req){

        // When data flow break between the code Data will not we inserted by using transaction (TRANSACTION)
        DB::transaction(function() use ($req) {

            //validate data
            $req->validate([
                "name" => "required",
                "email" => "required",
                "profile_img" => "required|mimes:jpg,jpeg,png,gif|max:10000"
            ]);


            //image data
            $imgname = "/products/" .time(). "_img." .$req->profile_img->extension();
            // $imgname = time(). "_img." .$req->profile_img->extension();

            //form data
            $createuser = new createuser();
            $createuser->name = $req->name;
            $createuser->email = $req->email;
            $createuser->password = Hash::make($req->password);
            $createuser->address = $req->address;
            $createuser->contact = $req->number;
            $createuser->profile_photo_path = $imgname;
            // $createuser->user_img = $imgname;
            $createuser->save();

            // -----To fail the code-----
            // throw new Exception('Someting went wrong');

            $req->profile_img->move(public_path('products'),$imgname);
        });    

        return redirect('/dashboard/createuser');

        // return $this->userlist();
        //----------default image store---------
        // $req->profile_img('file')->store('image');
        // echo '<pre>---';
        //----------form request data---------
        // print_r($req->all());
        // echo '</pre>';
        // die;
    } 

    function userlist(){
        $users = createuser::all();
        // $users = createuser::get();
        return view('createuser',['datausers'=>$users]);
    }    
    
    function delete($id){
        $data = createuser::find($id);
        $data->delete();
        return redirect('/dashboard/createuser');
    }

    function update($id){
        $data = createuser::find($id);
        return view('edit',['upuser'=>$data]);
    }
    
    function edit(Request $req){
        // echo '<pre>---';
        // print_r($req->all());
        // echo '</pre>';
        // die;
        $data = createuser::find($req->id);

        //validate data
        $req->validate([
            "name" => "required",
            "email" => "required",
            "profile_img" => "nullable|mimes:jpeg,jpg,png,gif|max:10000",
        ]);

        if(isset($req->profile_img)){
            //image data
            // $imgname = time(). "_img." .$req->profile_img->extension();
            $imgname = "/products/" .time(). "_img." .$req->profile_img->extension();
            $req->profile_img->move(public_path('products'),$imgname);
            $data->profile_photo_path = $imgname;
            // $data->user_img = $imgname;

        }

        $data->name = $req->name;
        $data->roleid = $req->roles;
        $data->email = $req->email;
        $data->address = $req->address;
        $data->contact = $req->number;
        $data->save();

        return redirect('/dashboard/createuser');
    }
}
