<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'message' => 'Webhook Service is running',
]));

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::view('/login', 'auth.login');
Route::prefix('minio')->group( function(){
    Route::get('/', [\App\Http\Controllers\MinioController::class, 'index'])->name('minio.dashboard');
});


Route::prefix('ocr')->group( function(){
    Route::get('/storage', [\App\Http\Controllers\OcrController::class, 'storage_index'])->name('ocr.storage.index');
    Route::get('/storage/{business_unit}', [\App\Http\Controllers\OcrController::class, 'storage_businessUnit'])->name('ocr.storage.business_unit');
    Route::get('/storage/{business_unit}/{dokumen}', [\App\Http\Controllers\OcrController::class, 'storage_dokumen'])->name('ocr.storage.dokumen');
});

Route::get('/ocr/simulate-webhook', [\App\Http\Controllers\OcrController::class, 'simulateWebhookForm'])->name('ocr.simulate_webhook.form');
Route::post('/ocr/simulate-webhook', [\App\Http\Controllers\OcrController::class, 'simulateWebhook'])->name('ocr.simulate_webhook');
