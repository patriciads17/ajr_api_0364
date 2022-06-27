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
// Route::post('employee', 'Api\EmployeeController@store');
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register','Api\AuthController@register');
Route::post('regisDriver','Api\DriverController@store');
Route::post('login','Api\AuthController@login');



Route::group(['middleware' => 'auth:user-api'], function(){
    Route::get('user/{id}', 'Api\UserController@show');
    Route::post('user', 'Api\UserController@update'); 
    Route::put('removeAccount/{id}', 'Api\UserController@updateByCs');

    Route::get('listpromo', 'Api\PromoController@indexActivePromo');
    Route::get('listKodePromo', 'Api\PromoController@indexActiveKodePromo');
    Route::get('selectedPromo/{id}', 'Api\PromoController@showSelectedPromo');
    Route::get('listcar', 'Api\CarController@showCarByType');
    Route::get('listcarr', 'Api\CarController@showAvailableCar');
    Route::get('listdriver', 'Api\DriverController@showActiveDriver');

    Route::get('readDriver/{id}', 'Api\DriverController@show');
    Route::get('readEmployee/{id}', 'Api\EmployeeController@show');
    Route::get('readCar/{id}', 'Api\CarController@show');

    Route::get('car', 'Api\CarController@index');
    Route::put('selectedCar/{id}', 'Api\CarController@updateAvailabilityCar');
    Route::put('selectedDriver/{id}', 'Api\DriverController@updateAvailabilityDriver');

    Route::post('logout','Api\AuthController@logout');

    Route::get('showPayment/{id}', 'Api\TransactionController@showPayment'); 
    Route::get('showRent/{id}', 'Api\TransactionController@showRent'); 
    Route::get('showTransaction/{id}', 'Api\TransactionController@showTransaction');
    Route::get('transaction/{id}', 'Api\TransactionController@show'); 
    Route::post('rent', 'Api\TransactionController@storeRenting');
    Route::post('payment', 'Api\TransactionController@storePayment');
    Route::put('rating/{id}', 'Api\TransactionController@storeRating');
    Route::put('statusTransaction/{id}', 'Api\TransactionController@updateStatusRent');
    Route::put('transaction/{id}', 'Api\TransactionController@updateRent');
    Route::delete('transaction/{id}', 'Api\TransactionController@delete'); 
    Route::post('updateAverage', 'Api\TransactionController@averageDriverRate');
});

Route::group(['middleware' => 'auth:driver-api'], function(){    
    Route::get('driverprofile/{id}', 'Api\DriverController@show');
    Route::post('driver', 'Api\DriverController@update');
    Route::post('updateAvailability', 'Api\DriverController@updateAvailabilityDriverMobile');
    Route::post('updateAverage', 'Api\TransactionController@averageDriverRate');
    Route::get('upcomingOrder/{id}', 'Api\TransactionController@findUpcomingOrder');
    Route::get('newestRate/{id}', 'Api\TransactionController@getNewestRate');
    Route::get('driverTransaction/{id}', 'Api\TransactionController@showTransactionDriver');
    Route::get('dataCar/{id}', 'Api\CarController@show');
});

Route::group(['middleware' => 'auth:employee-api'], function(){
    Route::group(['middleware' => 'scope:admin'], function(){
        Route::get('employee', 'Api\EmployeeController@indexEmployee');
        Route::get('owner', 'Api\EmployeeController@indexOwner');
        Route::post('employee', 'Api\EmployeeController@store');
        Route::post('update_employees/{id}', 'Api\EmployeeController@updateByAdmin');
        Route::delete('employee/{id}', 'Api\EmployeeController@destroy');

        Route::get('driver', 'Api\DriverController@index');
        Route::get('driver/{id}', 'Api\DriverController@show');
        Route::put('driver/{id}', 'Api\DriverController@updateAccountDriver');
        Route::post('dataDriver', 'Api\DriverController@updatebyAdmin');

        Route::get('car', 'Api\CarController@index');
        Route::get('updateStatusContract', 'Api\CarController@updateStatusContract');
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
        Route::get('transaction', 'Api\TransactionController@index');  
        Route::put('custTransaction/{id}', 'Api\TransactionController@updateRentByCs'); 
        Route::get('showCar/{id}', 'Api\CarController@show');
        Route::get('showDriver/{id}', 'Api\DriverController@show');

        Route::put('selectedCarCs/{id}', 'Api\CarController@updateAvailabilityCar');
        Route::put('selectedDriverCs/{id}', 'Api\DriverController@updateAvailabilityDriver');
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

        Route::get('carReport/{id}', 'Api\ReportController@carReport');
        Route::get('incomeReport/{id}', 'Api\ReportController@incomeReport');
        Route::get('driverReport/{id}', 'Api\ReportController@driverReport');
        Route::get('topCust/{id}', 'Api\ReportController@topCustomer');
        Route::get('topDriver/{id}', 'Api\ReportController@topDriver');
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

