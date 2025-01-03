<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
class fileupload extends Controller
{
    function uploadimg(Request $req){
        return $req->file('doc')->store('img');
        // return $req->file('doc')->storeAs('img',getClientOriginalName('aaa'));
    }
 

    public function showPdf(Request $req){
        // $filename = 'ejlXYRCDQWZfYuoR4HcImEJyY1mb6sdFKPKBC30i.pdf';
        // $url = asset('storage/app/img/' . $filename);
        // return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank">View PDF</a>';

        return view('/products/ejlXYRCDQWZfYuoR4HcImEJyY1mb6sdFKPKBC30i.pdf');
        // return 'storage/app/img/ejlXYRCDQWZfYuoR4HcImEJyY1mb6sdFKPKBC30i.pdf';
    }


}
//  storage/app/img/ejlXYRCDQWZfYuoR4HcImEJyY1mb6sdFKPKBC30i.pdf