@extends('layouts.app')

@section('titlepage', 'Dokumen OCR')

@section('navigasi')
    <span class="text-muted fw-light">Dashboard /</span> Business Unit
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Business Unit</h5>
                <button class="btn btn-primary btn-sm">
                    <i class="ti ti-plus me-1"></i> Tambah Business Unit
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Business Unit</th>
                                <th>Total Dokumen</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @for( $i=0; $i<5; $i++) 
                            @php
                                $nama_dirBusinessUnit = $i . "-" . "business_unit";
                            @endphp
                            <tr>
                                    <td>
                                        <i style="color:blue!important" class="ti ti-building fa-lg text-primary me-3"></i> <strong> 
                                            {{ $nama_dirBusinessUnit }}
                                        </strong>
                                    </td>
                                    <td>150 Dokumen</td>
                                    <td><span class="badge bg-label-success">Aktif</span></td>
                                    <td>03 Juli 2026</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a target="_blank" class="dropdown-item" href="{{ route('ocr.storage.business_unit', ['business_unit' => $nama_dirBusinessUnit]) }}"><i class="ti ti-folder-open me-1"></i> Buka</a>
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
