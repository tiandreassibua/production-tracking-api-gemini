<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DeliveryController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\DesignController;
use App\Http\Controllers\API\DesignRevisionController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\ProjectItemController;

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

    // Route untuk upload desain awal
    Route::post('projects/{project}/designs', [DesignController::class, 'uploadInitialDesign']);

    // Route untuk Studio melihat tugasnya
    Route::get('designs/my-tasks', [DesignController::class, 'myTasks']);

    // Route untuk Studio upload hasil desain
    Route::post('designs/{design}/upload-final', [DesignController::class, 'uploadFinalDesign']);

    // Route untuk Marketing menyetujui desain
    Route::post('designs/{design}/approve', [DesignController::class, 'approveDesign']);

    // Route untuk Marketing meminta revisi
    Route::post('designs/{design}/request-revision', [DesignController::class, 'requestRevision']);

    // Route untuk Studio upload file revisi
    Route::post('design-revisions/{revision}/upload', [DesignRevisionController::class, 'uploadRevisedFile']);

    // Route untuk Gudang konfirmasi bahan
    Route::post('projects/{project}/confirm-materials', [ProjectController::class, 'confirmMaterials']);

    // Route untuk CRUD Project Items (kita pakai 'update' saja)
    Route::apiResource('project-items', ProjectItemController::class)->only(['update']);

    // Route untuk CRUD Invoices
    Route::apiResource('invoices', InvoiceController::class)->only(['store', 'show']);

    // Routes untuk aksi pembayaran
    Route::post('invoices/{invoice}/upload-proof', [InvoiceController::class, 'uploadPaymentProof']);
    Route::post('invoices/{invoice}/verify', [InvoiceController::class, 'verifyPayment']);

    // Route untuk QC
    Route::post('projects/{project}/pass-qc', [ProjectController::class, 'passQualityControl']);

    // Route untuk CRUD Deliveries
    Route::apiResource('deliveries', DeliveryController::class)->only(['store']);

    // Route untuk upload dokumen serah terima
    Route::post('deliveries/{delivery}/upload-handover', [DeliveryController::class, 'uploadHandoverDocument']);

    // Route untuk Dashboard
    Route::get('dashboard/projects-overview', [DashboardController::class, 'projectsOverview']);
});
