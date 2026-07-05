<?php

use App\Http\Controllers\AdminKeyController;
use App\Http\Controllers\UsageController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\MinioControllerAPI;
use App\Http\Middleware\AuthenticateAdminKey;
use App\Http\Middleware\AuthenticateWebhookApiKey;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('admin')
        ->middleware(AuthenticateAdminKey::class)
        ->group(function () {
            Route::post('/keys', [AdminKeyController::class, 'store']);
            Route::get('/keys', [AdminKeyController::class, 'index']);
            Route::delete('/keys/{apiKey}', [AdminKeyController::class, 'destroy']);
            Route::get('/usages', [UsageController::class, 'show']);
        });

    Route::post('/webhook', [WebhookController::class, 'store'])
        ->middleware(AuthenticateWebhookApiKey::class);
});

Route::prefix('minio')->group(function(){
    // Menampilkan list bucket
    Route::get('/', [MinioControllerAPI::class, 'listBuckets']);
    // Menampilkan list business unit pada sebuah bucket
    Route::get('/{bucket}', [MinioControllerAPI::class, 'listBusinessUnits']);
    // Menampilkan list dokumen pada sebuah bucket dan business unit
    Route::get('/{bucket}/{directoryBusinessUnit}', [MinioControllerAPI::class, 'listDocuments']);
    // Menampilkan list file pada sebuah bucket, business unit, dan dokumen
    Route::get('/{bucket}/{directoryBusinessUnit}/{directoryDocument}', [MinioControllerAPI::class, 'listFiles']);
    // Download file atau document berdasarkan parameter query
    Route::get('/{bucket}/download', [MinioControllerAPI::class, 'download']);
    // Upload file pada sebuah bucket melalui antarmuka tertentu
    Route::post('/{bucket}/uploadInterface', [MinioControllerAPI::class, 'uploadFileInterface']);


});

