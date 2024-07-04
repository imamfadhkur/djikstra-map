<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DijkstraController;
use App\Http\Controllers\DijkstraV2Controller;
use App\Http\Controllers\DijkstraV3Controller;
use App\Http\Controllers\NonDijkstraController;

Route::get('/', function () {
    // return view('welcome');
    return view('index');
})->name('index');

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

Route::get('/get-lat-long', [Controller::class, 'get_lat_long_view']);
Route::post('/get-lat-long', [Controller::class, 'get_lat_long_data']);

Route::get('/non-dijkstra', [NonDijkstraController::class, 'nonDijkstra']);
Route::get('/dijkstra', [DijkstraV3Controller::class, 'ShortestPath']);

Route::get('/normal', [Controller::class, 'normalize']);
Route::get('/get-test', [Controller::class, 'get_test']);
Route::get('/insert-data', [Controller::class, 'insert_data']);

Route::get('/not-found', function () {
    abort(404);
})->name('not-found');

Route::get('/home', function () {
    if (auth()->check()) {
        // User is logged in, redirect to the dashboard
        return redirect()->route('dashboard');
    } else {
        // User is not logged in, redirect to the index
        return redirect()->route('index');
    }
});

Route::get('dashboard', [Controller::class, 'dashboard'])->name('dashboard')->middleware('auth');

Route::get('/login', function () {
    return view('auth.login', [
        'title' => 'login',
    ]);
})->middleware('guest')->name('login');
Route::post('/login', [Controller::class, 'login_action'])->middleware('guest');
Route::get('/logout', [Controller::class, 'logout_action'])->middleware('auth');
