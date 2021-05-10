<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', ['as' => 'login', 'uses' => 'App\Http\Controllers\AuthController@login']);
    Route::post('/check', 'App\Http\Controllers\AuthController@check');
    Route::post('/logout', ['as' => 'logout', 'uses' => 'App\Http\Controllers\AuthController@logout']);
});
