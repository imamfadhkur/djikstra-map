<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return view('index');
});

Route::get('/map-1-point', function () {
    return view('example.map_1_point');
});

Route::get('/map-2-point', function () {
    return view('example.map_2_point');
});

Route::get('/map-3-point', function () {
    return view('example.map_3_point');
});

Route::get('/multi-track', function () {
    return view('example.multi_track');
});

Route::get('/test', function () {
    return view('test');
});

Route::get('/lat_n_long', [Controller::class, 'lat_n_long']);
Route::get('/one_address', [Controller::class, 'one_address']);
Route::get('/two_address', [Controller::class, 'two_address']);
Route::get('/multi_address', [Controller::class, 'multi_address']);