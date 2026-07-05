<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Jobs\ProcessWebhookPayload;

class OcrController extends Controller
{

    /**
     * Menampilkan daftar Business Unit.
     * Route:  GET /ocr/storage
     * Name:   ocr.storage.index
     */
    public function storage_index(Request $request)
    {
        return view('ocr.storage.index');
    }

    /**
     * Menampilkan daftar folder/dokumen di dalam suatu Business Unit.
     * Route:  GET /ocr/storage/{business_unit}
     * Name:   ocr.storage.business_unit
     */
    public function storage_businessUnit(Request $request, $business_unit)
    {
        return view('ocr.storage.business_unit', compact('business_unit'));
    }

    /**
     * Menampilkan isi file di dalam suatu direktori dokumen.
     * Route:  GET /ocr/storage/{business_unit}/{dokumen}
     * Name:   ocr.storage.dokumen
     */
    public function storage_dokumen(Request $request, $business_unit, $dokumen)
    {
        return view('ocr.storage.dokumen', compact('business_unit', 'dokumen'));
    }

    /**
     * Menampilkan antarmuka form untuk rekayasa (simulasi) webhook.
     * Route:  GET /ocr/simulate-webhook
     * Name:   ocr.simulate_webhook.form
     */
    public function simulateWebhookForm()
    {
        $jsonPath = storage_path('contoh_response.json');
        $defaultPayload = File::exists($jsonPath) ? File::get($jsonPath) : "{\n\n}";
        
        return view('ocr.simulate_webhook', compact('defaultPayload'));
    }

    /**
     * Memproses form rekayasa webhook dan mengirimkan HTTP POST ke endpoint tujuan.
     * Route:  POST /ocr/simulate-webhook
     * Name:   ocr.simulate_webhook
     */
    public function simulateWebhook(Request $request)
    {
        $apiKey = $request->input('api_key');
        $url = $request->input('webhook_url', 'http://127.0.0.1:8001/api/v1/webhook');
        $bodyString = $request->input('payload');
        
        $body = json_decode($bodyString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->with('error', 'Format JSON tidak valid: ' . json_last_error_msg());
        }

        // Rekayasa memanggil endpoint webhook via HTTP POST
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->withHeaders([
                'x-api-key' => $apiKey,
                'source-type' => 'Simulasi-UI',
                'include-pdf' => 'true',
            ])->post($url, $body);

            if ($response->successful()) {
                return back()->with('success', 'Rekayasa Webhook berhasil dikirim! Response: ' . $response->body());
            } else {
                return back()->with('error', 'Gagal mengirim webhook (Status ' . $response->status() . '): ' . $response->body());
            }
        } catch (\Exception $e) {
            return back()->with('error', "Gagal memanggil URL Webhook: " . $e->getMessage() . ". Pastikan server tujuan menyala dan tidak terjadi deadlock (jika menggunakan php artisan serve).");
        }
    }
}
