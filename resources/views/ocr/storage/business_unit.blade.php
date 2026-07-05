@extends('layouts.app')

@section('titlepage', 'Dokumen OCR')

@section('navigasi')
    <span class="text-muted fw-light">
        <a href="{{ route('ocr.storage.index') }}" class="text-muted">Storage</a> /
    </span>
    {{ $business_unit }}
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Dokumen (Business Unit: {{ $business_unit }})</h5>
                <button class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Tambah Dokumen
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Dokumen</th>
                                <th>Jumlah File</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @for( $i=0;$i<10;$i++ )
                            @php
                                $nama_dirDokumen = "0" . $i . "07-2026-" . "scan";
                            @endphp
                            <tr>
                                <td>
                                    <i class="ti ti-folder-filled fa-lg text-warning me-3"></i> <strong>{{ $nama_dirDokumen }}</strong>
                                </td>
                                <td>5 File</td>
                                <td><span class="badge bg-label-secondary">-</span></td>
                                <td>0{{$i}} Juli 2026</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a target="_blank" class="dropdown-item" href="{{ route('ocr.storage.dokumen', ['business_unit' => $business_unit, 'dokumen' => '2026-07-03-Scans']) }}"><i class="ti ti-folder-open me-1"></i> Buka</a>
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
