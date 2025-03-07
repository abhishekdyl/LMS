<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\fileupload;
use App\Http\Controllers\createusercontroller;
use App\Http\Controllers\modelbindingController;
use App\Http\Controllers\productcontroller;
use App\Http\Controllers\relationContoller;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

 

Route::view('/', 'welcome'); 
Route::get('/user', function () {
    return view('user');
});


//----------API upload file-----------------
Route::post('uploadfile',[fileupload::class,'uploadimg']);
Route::get('img',[fileupload::class,'showPdf']);

 //---------------------CRUD--------------------
// Route::view('createuser','createuser'); 
// Route::middleware(['loggedin'])->group(function(){
    Route::middleware(['isadmin'])->group(function(){
        Route::get('/dashboard/createuser',[createusercontroller::class,'userlist']);
        Route::post('/dashboard/createusercontroller',[createusercontroller::class,'createuser']);
        Route::get('/dashboard/delete/{id}',[createusercontroller::class,'delete']);
        Route::get('/dashboard/update/{id}',[createusercontroller::class,'update']);
        Route::post('edit',[createusercontroller::class,'edit']);
    });
// });
    
//----------------products-----------------
// Route::view('/dashboard/products', 'products');
Route::get('/dashboard/products/{id?}',[productcontroller::class,'getproduct']);
// Route::get('/dashboard/addproduct/{id}',[productcontroller::class,'addproduct']);


//------------modelbinding-----------------
Route::get('join',[modelbindingController::class,'tablejoin']);
//------------modelbinding-----------------
Route::get('device/{keyname:name}',[modelbindingController::class,'mbinding']);
//------------relation-----------------
Route::get('relation',[relationContoller::class,'otmanyrelation']);

//fluent strings
$string = 'hi, welcome to the laravel';
// $string = Str:: ucfirst($string);
// $string = Str:: replaceFirst('Hi','Hello',$string);
// $string = Str:: camel($string);
//-----------------OR USE LIKE FLUENT STRING (METHOD CHAINING) ---------------------
$string = Str:: of($string)->ucfirst($string)
->replaceFirst('Hi','Hello',$string) 
->camel($string);
echo $string;
//------------------------------------------------------------------------


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
