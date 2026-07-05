@extends('layouts.auth')

@section('title', 'Login - OCR')

@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
            <!-- Login -->
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-4 mt-2">
                        <span class="app-brand-text demo text-body fw-bold">OCR APP</span>
                    </div>
                    <!-- /Logo -->
                    <h4 class="mb-1 pt-2">Selamat Datang di OCR! 👋</h4>
                    <p class="mb-4">Silahkan Login untuk mengakses dashboard.</p>
                    
                    <form id="formAuthentication" class="mb-3" action="#" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="id_user" class="form-label">Email atau Username</label>
                            <input type="text" class="form-control" id="id_user" name="id_user" placeholder="Masukkan email atau username" autofocus />
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">Password</label>
                                <a href="#">
                                    <small>Lupa Password?</small>
                                </a>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-me" />
                                <label class="form-check-label" for="remember-me"> Ingat Saya </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Login -->
        </div>
    </div>
</div>
@endsection
