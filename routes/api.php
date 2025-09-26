<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProjectController;

// Endpoint publik untuk login dan register
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Endpoint yang dilindungi oleh autentikasi Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Endpoint untuk logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Route untuk mengecek user yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ROUTE UNTUK CRUD PROJECT
    Route::apiResource('projects', ProjectController::class);

    // NANTINYA, SEMUA ROUTE API LAINNYA AKAN DITARUH DI SINI
});
