@extends('layouts.app')

@section('title', 'Profil Pengguna - FinTrack')

@section('content')
<div class="row g-4 justify-content-center">
    <!-- Ringkasan Profil & Foto Saat Ini -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-person-badge me-2 text-{{ $user->avatar_color }}"></i>Profil Pengguna
                </h5>
                <span class="badge bg-secondary-subtle text-secondary small">
                    <code>/profile</code>
                </span>
            </div>
            <div class="card-body p-4 text-center">
                <div class="mb-3 position-relative d-inline-block">
                    <div id="avatarWrapper">
                        <x-user-avatar :user="$user" :size="112" id="avatarCurrent" />
                    </div>
                    <img
                        id="avatarPreview"
                        src="#"
                        alt="Pratinjau foto profil"
                        class="rounded-circle object-fit-cover border border-3 border-{{ $user->avatar_color }} shadow-sm d-none"
                        style="width: 112px; height: 112px;"
                    >
                </div>

                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted small mb-2">{{ $user->email }}</p>

                <div class="d-flex justify-content-center gap-2 flex-wrap mb-3">
                    <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }} text-uppercase px-3 py-1">
                        Role: {{ $user->role }}
                    </span>
                    <span class="badge bg-{{ $user->avatar_color }} {{ $user->avatar_color === 'warning' ? 'text-dark' : 'text-white' }} px-3 py-1">
                        Tema: {{ ucfirst($user->avatar_color) }}
                    </span>
                </div>

                @if($user->bio)
                    <div class="p-3 bg-light rounded text-start small mb-3">
                        <div class="text-muted fw-semibold mb-1"><i class="bi bi-chat-left-quote me-1"></i> Bio</div>
                        <p class="mb-0 text-dark">{{ $user->bio }}</p>
                    </div>
                @endif

                <div class="text-start small border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">ID Pengguna</span>
                        <span class="fw-semibold">#{{ $user->id }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Nomor Telepon</span>
                        <span class="fw-semibold">{{ $user->phonenumber ?? 'Belum ditambahkan' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Terdaftar Sejak</span>
                        <span class="fw-semibold">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                    </div>
                </div>

                @if($user->profile_photo)
                    <form action="{{ route('profile.photo.destroy') }}" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="btn btn-outline-danger btn-sm w-100"
                            onclick="return confirm('Hapus foto profil Anda saat ini?')"
                        >
                            <i class="bi bi-trash me-1"></i> Hapus Foto Profil
                        </button>
                    </form>
                @endif
            </div>
            <div class="card-footer bg-white d-flex justify-content-between py-3">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
                <a href="{{ route('transactions.index') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-wallet2 me-1"></i> Transaksi
                </a>
            </div>
        </div>
    </div>

    <!-- Form Kustomisasi Profil & Keamanan -->
    <div class="col-lg-8">
        <!-- Form Edit Profil & Upload Foto -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-sliders me-2 text-primary"></i>Kustomisasi Profil & Foto Profil
                </h5>
            </div>
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Upload Foto Profil -->
                    <div class="mb-4 p-3 bg-light rounded border">
                        <label for="profile_photo" class="form-label fw-semibold mb-2">
                            <i class="bi bi-camera me-1 text-primary"></i> Foto Profil (File Upload)
                        </label>
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2">
                            <input
                                type="file"
                                class="form-control @error('profile_photo') is-invalid @enderror"
                                id="profile_photo"
                                name="profile_photo"
                                accept="image/jpeg,image/png,image/webp"
                            >
                            <button type="button" id="resetPhotoSelection" class="btn btn-outline-secondary btn-sm d-none text-nowrap">
                                Batal Pilih
                            </button>
                        </div>
                        @error('profile_photo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text mt-2">
                            Unggah gambar berformat <strong>JPG, JPEG, PNG, atau WEBP</strong> (maks. 2 MB). Foto lama akan otomatis diganti saat Anda menyimpan foto baru.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                            <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                maxlength="70"
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Alamat Email</label>
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                maxlength="80"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phonenumber" class="form-label fw-semibold">Nomor Telepon</label>
                            <input
                                type="text"
                                class="form-control @error('phonenumber') is-invalid @enderror"
                                id="phonenumber"
                                name="phonenumber"
                                value="{{ old('phonenumber', $user->phonenumber) }}"
                                placeholder="08xxxxxxxxxx"
                                maxlength="20"
                            >
                            @error('phonenumber')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="theme_color" class="form-label fw-semibold">Warna Tema Profil</label>
                            <select
                                class="form-select @error('theme_color') is-invalid @enderror"
                                id="theme_color"
                                name="theme_color"
                            >
                                @foreach($themeColors as $value => $label)
                                    <option value="{{ $value }}" @selected(old('theme_color', $user->avatar_color) === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('theme_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="bio" class="form-label fw-semibold">Bio / Tentang Saya</label>
                            <textarea
                                class="form-control @error('bio') is-invalid @enderror"
                                id="bio"
                                name="bio"
                                rows="3"
                                maxlength="500"
                                placeholder="Ceritakan sedikit tentang diri Anda..."
                            >{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maksimal 500 karakter.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Form Ubah Password -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-shield-lock me-2 text-secondary"></i>Ubah Password
                </h5>
            </div>
            <div class="card-body p-4">
                @if ($errors->passwordUpdate->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->passwordUpdate->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="current_password" class="form-label fw-semibold">Password Saat Ini</label>
                            <input
                                type="password"
                                class="form-control @if($errors->passwordUpdate->has('current_password')) is-invalid @endif"
                                id="current_password"
                                name="current_password"
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">Password Baru</label>
                            <input
                                type="password"
                                class="form-control @if($errors->passwordUpdate->has('password')) is-invalid @endif"
                                id="password"
                                name="password"
                                placeholder="Min. 6 karakter"
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                            <input
                                type="password"
                                class="form-control"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Ulangi password baru"
                                required
                            >
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-outline-dark px-4 fw-semibold">
                            <i class="bi bi-key me-1"></i> Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const photoInput = document.getElementById('profile_photo');
        const avatarWrapper = document.getElementById('avatarWrapper');
        const avatarPreview = document.getElementById('avatarPreview');
        const resetButton = document.getElementById('resetPhotoSelection');

        if (!photoInput || !avatarWrapper || !avatarPreview || !resetButton) {
            return;
        }

        photoInput.addEventListener('change', function (event) {
            const file = event.target.files && event.target.files[0];
            if (!file) {
                avatarPreview.classList.add('d-none');
                avatarWrapper.classList.remove('d-none');
                resetButton.classList.add('d-none');
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                avatarPreview.src = e.target.result;
                avatarPreview.classList.remove('d-none');
                avatarWrapper.classList.add('d-none');
                resetButton.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        });

        resetButton.addEventListener('click', function () {
            photoInput.value = '';
            avatarPreview.src = '#';
            avatarPreview.classList.add('d-none');
            avatarWrapper.classList.remove('d-none');
            resetButton.classList.add('d-none');
        });
    });
</script>
@endsection
