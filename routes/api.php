<?php

use App\Http\Controllers\Accurate_Creds;
use App\Http\Controllers\Accurate_Sales_Orders;
use App\Http\Controllers\RedisController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/debug', function () {
    return "yes";
});

Route::get('/accurate-token', function() {
    $this->object = new Accurate_Creds();
    return $this->object->request_token_accurate();
});

// setInterval(function(){
//     $this->object = new Accurate_Sales_Orders();
//     $this->object->request_list_with_details("aade25d1-d3c3-4e44-b11a-6c611171fb25", "31890f5e-5f0e-4c02-a205-d4198cbedbea");
// }, 1000);

function setInterval($f, $milliseconds)
{
       $seconds=(int)$milliseconds/1000;
       while(true)
       {
           $f();
           sleep($seconds);
       }
}

Route::get('/get-sales-order-list', function(Request $request) {
    $this->object = new Accurate_Sales_Orders();
    return $this->object->request_list_with_details($request->access_token, $request->session);
});

Route::get('/redis-get', function(Request $request) {
    $this->object = new RedisController();
    return $this->object->show($request->id);
});

Route::get('/redis-get-all', function(Request $request) {
    $this->object = new RedisController();
    return $this->object->showAll();
});

Route::get('/redis-create', function(Request $request) {
    $this->object = new RedisController();
    return $this->object->create($request->key, $request->value);
});

$this->testerObject = new RedisController(123);
Route::get('/debugger2', function(Request $request) {
    return $this->testerObject->getConsignment($request->data);
});