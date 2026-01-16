<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PetaniController;
use App\Http\Controllers\Api\PenggilinganController;

// Auth Routes (Public)
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Petani Routes - All users can view
    Route::get('/petani', [PetaniController::class, 'index']);
    Route::get('/petani/{id}', [PetaniController::class, 'show']);
    Route::get('/petani/export/excel', [PetaniController::class, 'export']);
    
    // Petani Routes - Only superadmin can create/update/delete
    Route::middleware('role:superadmin')->group(function () {
        Route::post('/petani', [PetaniController::class, 'store']);
        Route::put('/petani/{id}', [PetaniController::class, 'update']);
        Route::delete('/petani/{id}', [PetaniController::class, 'destroy']);
        Route::post('/petani/check-nik', [PetaniController::class, 'checkNik']);
    });
    
    // Penggilingan Routes - All users can view
    Route::get('/penggilingan', [PenggilinganController::class, 'index']);
    Route::get('/penggilingan/{id}', [PenggilinganController::class, 'show']);
    Route::get('/penggilingan/summary', [PenggilinganController::class, 'summary']);
    Route::get('/penggilingan/export/excel', [PenggilinganController::class, 'export']);
    
    // Penggilingan Routes - Only superadmin can create/update/delete
    Route::middleware('role:superadmin')->group(function () {
        Route::post('/penggilingan', [PenggilinganController::class, 'store']);
        Route::put('/penggilingan/{id}', [PenggilinganController::class, 'update']);
        Route::post('/penggilingan/{id}', [PenggilinganController::class, 'update']); // For FormData
        Route::delete('/penggilingan/{id}', [PenggilinganController::class, 'destroy']);
    });
});
