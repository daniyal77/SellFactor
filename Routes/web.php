<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::prefix('sell')->name('sell.')->group(function () {

    Route::get('/remove-factor', 'SellFactorViewController@removeFactor')->name('remove.factor');
    Route::get('/', 'SellFactorViewController@index')->name('index');
    Route::get('/get-factor', 'SellFactorViewController@getFactor')->name('get.factor');
    Route::get('/get-factor-remove', 'SellFactorViewController@getFactorRemove')->name('get.factor.remove');
    Route::get('/show-factor/{factor_id}', 'SellFactorViewController@showFactor')->name('show.factor');
    Route::get('/search', 'SellFactorViewController@search')->name('search');
    Route::get('/show/{buy_factor}', 'SellFactorViewController@show')->name('show');


    Route::get('/create', 'SellFactorController@create')->name('create');
    Route::post('/store', 'SellFactorController@store')->middleware('init.factor.request')->name('store');
    Route::post('/draft', 'SellFactorController@draft')->name('draft');
    Route::get('/edit/{factor_id}', 'SellFactorController@edit')->name('edit');
    Route::post('/update/{factor_id}', 'SellFactorController@update')->middleware('init.factor.request')->name('update');


});

