<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [ApiController::class, 'login']);
Route::post('/list-perizinan', [ApiController::class, 'listPerizinan']);
Route::post('/approve-perizinan', [ApiController::class, 'approvePerizinan']);
Route::post('/rekap-perizinan', [ApiController::class, 'rekapPerizinan']);
Route::post('/users-list', [ApiController::class, 'usersList']);
Route::post('/user-detail', [ApiController::class, 'userDetail']);
Route::post('/user-create', [ApiController::class, 'userCreate']);
Route::post('/user-update', [ApiController::class, 'userUpdate']);
Route::post('/user-delete', [ApiController::class, 'userDelete']);
