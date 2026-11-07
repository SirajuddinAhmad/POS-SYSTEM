@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Pribadi')
@section('page_subtitle', 'Kelola informasi identitas, foto profil, dan kata sandi akun Anda')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-2xl bg-white p-4">
                
                <div class="d-flex align-items-center p-3 bg-slate-50 border border-slate-200 rounded-2xl mb-4">
                    <img src="{{ $user->avatar_url }}" class="w-16 h-16 rounded-full object-cover mr-4 border-2 border-slate-300">
                    <div>
                        <h5 class="font-bold text-slate-800 mb-0">{{ $user->name }}</h5>
                        <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $user->email }}</div>
                        <span class="badge {{ $user->isAdmin() ? 'badge-danger' : 'badge-info' }} text-xs font-bold mt-1">
                            {{ $user->isAdmin() ? 'ADMINISTRATOR (FULL ACCESS)' : 'KASIR (AKSES KHUSUS POS & PROFIL PRIBADI)' }}
                        </span>
                    </div>
                </div>

                @if($user->isCashier())
                <div class="alert alert-info text-xs py-2 px-3 rounded-xl mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-base"></i>
                    <span>Sebagai <strong>Kasir</strong>, Anda dapat memperbarui profil pribadi Anda di halaman ini. Sesuai aturan sistem, akun Administrator dan akun kasir lain tidak dapat diubah oleh Anda.</span>
                </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">No. Telepon / WhatsApp</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="0812xxxxxxxx">
                        </div>

                        <div class="col-12 form-group mb-4">
                            <label class="text-xs font-bold text-slate-700 uppercase">Ganti Foto Profil (Avatar)</label>
                            <input type="file" name="avatar" class="form-control-file border p-2 rounded-xl">
                            <small class="text-slate-400">Format JPG, PNG, WEBP (Maksimal 2MB)</small>
                        </div>
                    </div>

                    <hr class="my-3 border-slate-200">
                    <h6 class="font-bold text-slate-800 text-sm mb-3">Ganti Kata Sandi (Opsional)</h6>

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control" placeholder="••••••••">
                            @error('current_password')
                                <small class="text-danger font-semibold">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Password Baru</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Minimal 6 karakter">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" class="form-control" placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <div class="text-right mt-4 pt-3 border-top border-slate-100">
                        <button type="submit" class="btn btn-primary font-bold px-4 py-2 rounded-xl shadow-md">
                            <i class="fa-solid fa-check mr-1"></i> Simpan Profil Pribadi
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
