@extends('layouts.app')

@section('title', 'Login - FinTrack')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="display-6 mb-2">🔐</div>
                    <h3 class="fw-bold">Login Pengguna</h3>
                    <p class="text-muted small">Silakan masuk menggunakan akun Anda</p>
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

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

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
                                autofocus
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                id="password" 
                                name="password" 
                                placeholder="Masukkan password Anda" 
                                required
                            >
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small text-muted" for="remember">
                                Ingat Saya
                            </label>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg fs-6 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="small text-muted mb-0">
                            Belum memiliki akun? 
                            <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">Daftar di sini</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kartu Bantuan Akun Demo untuk Kemudahan Pengujian Praktikum -->
        <div class="card mt-3 bg-light border-0">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-2 small text-secondary">
                    <i class="bi bi-info-circle-fill text-primary me-1"></i> Akun Pengujian (Testing):
                </h6>
                <div class="row g-2 small">
                    <div class="col-6">
                        <div class="p-2 border rounded bg-white">
                            <div class="badge bg-danger mb-1">Role: Admin</div>
                            <div class="text-truncate"><strong>admin@example.com</strong></div>
                            <div class="text-muted">Pass: <code>password123</code></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-white">
                            <div class="badge bg-primary mb-1">Role: User</div>
                            <div class="text-truncate"><strong>user@example.com</strong></div>
                            <div class="text-muted">Pass: <code>password123</code></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
