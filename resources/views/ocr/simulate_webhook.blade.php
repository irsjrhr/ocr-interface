@extends('layouts.app')

@section('titlepage', 'Rekayasa Webhook')

@section('navigasi')
    <span class="text-muted fw-light">OCR Interface /</span> Rekayasa Webhook
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Form Simulasi Webhook Nanonets</h5>
                <small class="text-muted">Kirim request simulasi OCR secara langsung ke endpoint tujuan.</small>
            </div>
            <div class="card-body">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('ocr.simulate_webhook') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label" for="webhook_url">URL Tujuan Webhook</label>
                        <input type="url" class="form-control" id="webhook_url" name="webhook_url" value="http://127.0.0.1:8001/api/v1/webhook" required>
                        <div class="form-text">Pastikan port-nya mengarah ke aplikasi OCR (misal: 8001).</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="api_key">API Key (x-api-key)</label>
                        <input type="text" class="form-control" id="api_key" name="api_key" value="UI-TEST-KEY" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label" for="payload">JSON Payload Nanonets</label>
                        <textarea class="form-control text-monospace" id="payload" name="payload" rows="15" required style="font-family: monospace;">{{ $defaultPayload }}</textarea>
                        <div class="form-text">Anda bisa mengubah nilai hasil prediksi di sini untuk mensimulasikan dokumen yang berbeda.</div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="ti ti-rocket me-1"></i> Kirim Simulasi
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
