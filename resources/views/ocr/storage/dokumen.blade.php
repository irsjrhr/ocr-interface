@extends('layouts.app')

@section('titlepage', 'Dokumen OCR')

@section('navigasi')
    <span class="text-muted fw-light">
        <a href="{{ route('ocr.storage.index') }}" class="text-muted">Storage</a> /
        <a href="{{ route('ocr.storage.business_unit', $business_unit) }}" class="text-muted">{{ $business_unit }}</a> /
    </span>
    {{ $dokumen }}
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar File (Dokumen: {{ $dokumen }})</h5>
                <button class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Upload File
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Dokumen</th>
                                <th>Ukuran</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @for($i=0; $i<10; $i++)
                                @php
                                    $fileName = "Dokumen_0" . $i . ".json";
                                @endphp
                                <tr>
                                    <td>
                                        <i class="ti ti-file-text fa-lg text-primary me-3"></i> <strong>{{ $fileName }}</strong>
                                    </td>
                                    <td>2.5 MB</td>
                                    <td><span class="badge bg-label-success">Selesai</span></td>
                                    <td>0{{ $i }} Juli 2026</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-download me-1"></i> Download</a>
                                                <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-trash me-1"></i> Hapus</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
