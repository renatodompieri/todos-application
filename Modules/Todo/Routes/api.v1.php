<?php

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/todo', 'TodoV1Controller@index');
    Route::get('/todo/prepare-elements', 'TodoV1Controller@prepareSelectElements');
    Route::get('/todo/{id}', 'TodoV1Controller@show');
    Route::post('/todo', 'TodoV1Controller@store');
    Route::patch('/todo/{id}', 'TodoV1Controller@update');
    Route::post('/todo/{id}/status', 'TodoV1Controller@toggleStatus');
    /** Laravel is bugging with DELETE method, that's why it's post here */
    Route::post('/todo/{id}', 'TodoV1Controller@destroy');
});
