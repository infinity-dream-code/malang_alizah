<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/list-perizinan', function () {
    return view('list-perizinan');
});

Route::get('/dashboard/list-perizinan', function () {
    return view('list-perizinan');
});

Route::post('/api/login', [\App\Http\Controllers\ApiController::class, 'login']);
Route::post('/api/list-perizinan', [\App\Http\Controllers\ApiController::class, 'listPerizinan']);
