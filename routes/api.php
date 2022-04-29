<?php

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

Route::post('register','Api\AuthController@register');
Route::post('login','Api\AuthController@login');

// Route::post('employee', 'Api\EmployeeController@store');

Route::group(['middleware' => 'auth:user-api'], function(){
    Route::get('user/{id}', 'Api\UserController@show');
    Route::post('user', 'Api\UserController@update'); 

    Route::get('listpromo', 'Api\PromoController@indexActivePromo');
    Route::get('listcar', 'Api\CarController@indexActiveCar');

    Route::get('car', 'Api\CarController@index');
    Route::get('car/{id}', 'Api\CarController@show');

    Route::post('logout','Api\AuthController@logout');

});

Route::group(['middleware' => 'auth:employee-api'], function(){

   
    
    Route::group(['middleware' => 'scope:admin'], function(){
        Route::get('employee', 'Api\EmployeeController@index');
        Route::post('employee', 'Api\EmployeeController@store');
        Route::post('update_employees/{id}', 'Api\EmployeeController@updateByAdmin');
        Route::delete('employee/{id}', 'Api\EmployeeController@destroy');


        Route::get('car', 'Api\CarController@index');
        Route::get('car/{id}', 'Api\CarController@show');
        Route::post('car', 'Api\CarController@store');
        Route::post('update_car', 'Api\CarController@update');
        Route::delete('car/{id}', 'Api\CarController@destroy');
        Route::get('deadlineCar/{id}', 'Api\CarController@contractDeadline');
        Route::put('deadline/{id}', 'Api\CarController@updateByDeadline');

        Route::get('partner', 'Api\PartnerController@index');
        Route::get('idpartner/{id}', 'Api\PartnerController@getId');
        Route::get('namepartner/{id}', 'Api\PartnerController@getName');
        Route::get('partner/{id}', 'Api\PartnerController@show');
        Route::post('partner', 'Api\PartnerController@store');
        Route::put('partner/{id}', 'Api\PartnerController@update');
        Route::delete('partner/{id}', 'Api\PartnerController@destroy');
    });    

    Route::group(['middleware' => 'scope:cs'], function(){    
        Route::get('users', 'Api\UserController@index');
        Route::put('update/{id}', 'Api\UserController@updateByCs');
    });
    

    Route::group(['middleware' => 'scope:manager'], function(){
        Route::get('detailshift', 'Api\DetailShiftController@index');
        Route::post('detailshift', 'Api\DetailShiftController@store');
        Route::put('detailshift/{id}', 'Api\DetailShiftController@update');
        Route::delete('detailshift/{id}', 'Api\DetailShiftController@destroy');

        Route::get('id_shift/{id}/{id2}', 'Api\ShiftController@getIdShift');
        Route::get('get_shift/{id}', 'Api\ShiftController@getShift');

        Route::get('name_employee/{id}', 'Api\EmployeeController@getNameEmployee');
        Route::get('id_employee/{id}', 'Api\EmployeeController@getIdEmployee');
        Route::get('get_employee/{id}', 'Api\EmployeeController@getEmployee');

        Route::get('promo', 'Api\PromoController@index');
        Route::get('promo/{id}', 'Api\PromoController@show');
        Route::post('promo', 'Api\PromoController@store');
        Route::put('promo/{id}', 'Api\PromoController@update');
        Route::delete('promo/{id}', 'Api\PromoController@destroy');
    });

    Route::group(['middleware' => 'scope:manager,admin,cs'], function(){
        Route::post('update_employee', 'Api\EmployeeController@update');
        Route::get('employee/{id}', 'Api\EmployeeController@show');
        Route::post('logout','Api\AuthController@logout');
        Route::get('myShift/{id}', 'Api\DetailShiftController@getShiftEmployee');
        Route::get('get_myshift/{id}', 'Api\ShiftController@getShiftArray');
        Route::get('validationShift/{id}', 'Api\DetailShiftController@countShiftEmployee');
    });
    
});

