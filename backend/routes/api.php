<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PetaniController;
use App\Http\Controllers\Api\PenggilinganController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SubAdminController;
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\WilayahController as WilayahExportImportController;

// Auth Routes (Public)
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Petani Routes - Admin and Lapangan role can access
    Route::middleware('role:admin,lapangan,superadmin')->group(function () {
        Route::get('/petani', [PetaniController::class, 'index']);
        Route::get('/petani/{id}', [PetaniController::class, 'show']);
        Route::get('/petani/export/excel', [PetaniController::class, 'export']);
        Route::post('/petani', [PetaniController::class, 'store']);
        Route::put('/petani/{id}', [PetaniController::class, 'update']);
        Route::delete('/petani/{id}', [PetaniController::class, 'destroy']);
        Route::post('/petani/check-nik', [PetaniController::class, 'checkNik']);
    });
    
    // Penggilingan Routes - Admin and Penggilingan role can access
    Route::middleware('role:admin,penggilingan,superadmin')->group(function () {
        Route::get('/penggilingan', [PenggilinganController::class, 'index']);
        Route::get('/penggilingan/{id}', [PenggilinganController::class, 'show']);
        Route::get('/penggilingan/summary', [PenggilinganController::class, 'summary']);
        Route::get('/penggilingan/export/excel', [PenggilinganController::class, 'export']);
        Route::get('/penggilingan/{id}/export-makloon-gkp', [PenggilinganController::class, 'exportMakloonGKP']);
        Route::post('/penggilingan', [PenggilinganController::class, 'store']);
        Route::put('/penggilingan/{id}', [PenggilinganController::class, 'update']);
        Route::post('/penggilingan/{id}', [PenggilinganController::class, 'update']); // For FormData
        Route::delete('/penggilingan/{id}', [PenggilinganController::class, 'destroy']);
    });
    
    // Verifikasi Routes - Admin & SuperAdmin only
    Route::middleware('role:admin,superadmin')->group(function () {
        Route::post('/petani/{id}/verifikasi', [PetaniController::class, 'verifikasi']);
        Route::post('/penggilingan/{id}/verifikasi', [PenggilinganController::class, 'verifikasi']);
    });

    // Wilayah Read-Only Routes - All authenticated users (for dropdowns)
    Route::get('/provinsi', [WilayahController::class, 'getProvinsi']);
    Route::get('/kabupaten', [WilayahController::class, 'getKabupaten']);
    Route::get('/kecamatan', [WilayahController::class, 'getKecamatan']);
    Route::get('/kalurahan', [WilayahController::class, 'getKalurahan']);

    // Sub-Admin Management - Only parent penggilingan admin (no parent_id)
    Route::middleware('role:penggilingan')->group(function () {
        Route::get('/my-sub-admins', [SubAdminController::class, 'index']);
        Route::post('/my-sub-admins', [SubAdminController::class, 'store']);
        Route::put('/my-sub-admins/{id}', [SubAdminController::class, 'update']);
        Route::delete('/my-sub-admins/{id}', [SubAdminController::class, 'destroy']);
    });

    // User Management Routes - Only SuperAdmin can access
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        
        // Wilayah Write Routes
        Route::post('/provinsi', [WilayahController::class, 'storeProvinsi']);
        Route::put('/provinsi/{id}', [WilayahController::class, 'updateProvinsi']);
        Route::delete('/provinsi/{id}', [WilayahController::class, 'deleteProvinsi']);
        
        Route::post('/kabupaten', [WilayahController::class, 'storeKabupaten']);
        Route::put('/kabupaten/{id}', [WilayahController::class, 'updateKabupaten']);
        Route::delete('/kabupaten/{id}', [WilayahController::class, 'deleteKabupaten']);
        
        Route::post('/kecamatan', [WilayahController::class, 'storeKecamatan']);
        Route::put('/kecamatan/{id}', [WilayahController::class, 'updateKecamatan']);
        Route::delete('/kecamatan/{id}', [WilayahController::class, 'deleteKecamatan']);
        
        Route::post('/kalurahan', [WilayahController::class, 'storeKalurahan']);
        Route::put('/kalurahan/{id}', [WilayahController::class, 'updateKalurahan']);
        Route::delete('/kalurahan/{id}', [WilayahController::class, 'deleteKalurahan']);
        
        // Wilayah Export/Import Routes (Combined - SuperAdmin only)
        Route::get('/wilayah/export', [WilayahExportImportController::class, 'exportWilayah']);
        Route::post('/wilayah/import', [WilayahExportImportController::class, 'importWilayah']);
        Route::get('/wilayah/template', [WilayahExportImportController::class, 'templateWilayah']);
    });
});
