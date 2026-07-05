@extends('layouts.app')

@section('titlepage', 'Minio Storage')

@section('navigasi')
    <span class="text-muted fw-light">Storage /</span> Minio Manager
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Bucket MinIO</h5>
                <button class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Buat Bucket Baru
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Bucket</th>
                                <th>Ukuran</th>
                                <th>Region</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <tr>
                                <td>
                                    <i class="ti ti-bucket fa-lg text-warning me-3"></i> <strong>ocr-scans</strong>
                                </td>
                                <td>2.5 GB</td>
                                <td>ap-southeast-1</td>
                                <td>01 Juli 2026</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-folder-open me-1"></i> Buka</a>
                                            <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-trash me-1"></i> Hapus</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="ti ti-bucket fa-lg text-warning me-3"></i> <strong>ocr-models</strong>
                                </td>
                                <td>850 MB</td>
                                <td>ap-southeast-1</td>
                                <td>15 Juni 2026</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-folder-open me-1"></i> Buka</a>
                                            <a class="dropdown-item" href="javascript:void(0);"><i class="ti ti-trash me-1"></i> Hapus</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
