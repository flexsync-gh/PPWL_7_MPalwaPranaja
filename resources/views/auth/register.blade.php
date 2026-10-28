@extends('layouts.app')

@section('title', 'Register - FinTrack')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="display-6 mb-2">📝</div>
                    <h3 class="fw-bold">Daftar Akun Baru</h3>
                    <p class="text-muted small">Lengkapi data di bawah ini untuk membuat akun baru</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input 
                                type="text" 
                                class="form-control @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}" 
                                placeholder="Contoh: Budi Santoso" 
                                required 
                                autofocus
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                placeholder="nama@email.com" 
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="phonenumber" class="form-label fw-semibold">Nomor Telepon (Opsional)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input 
                                type="text" 
                                class="form-control @error('phonenumber') is-invalid @enderror" 
                                id="phonenumber" 
                                name="phonenumber" 
                                value="{{ old('phonenumber') }}" 
                                placeholder="08xxxxxxxxxx"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input 
                                    type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Min. 6 karakter" 
                                    required
                                >
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    placeholder="Ulangi password" 
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 small d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <div>
                            Akun baru yang didaftarkan akan otomatis mendapatkan <strong>Role: User</strong> sesuai ketentuan sistem RBAC.
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg fs-6 fw-semibold">
                            <i class="bi bi-person-check-fill me-1"></i> Daftar Sekarang
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="small text-muted mb-0">
                            Sudah memiliki akun? 
                            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Masuk di sini</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
