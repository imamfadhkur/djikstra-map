<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return view('index');
});

Route::get('/map-1-point', function () {
    return view('map_1_point');
});

Route::get('/map-2-point', function () {
    return view('map_2_point');
});

Route::get('/map-3-point', function () {
    return view('map_3_point');
});

Route::get('/multi-track', function () {
    return view('multi_track');
});