<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', 'App\Http\Controllers\AuthController@login');
    Route::post('/check', 'App\Http\Controllers\AuthController@check');
    Route::post('/logout', 'App\Http\Controllers\AuthController@logout');
});
