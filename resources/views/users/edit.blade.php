@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('page_title', 'Edit Data Pengguna')
@section('page_subtitle', 'Perbarui informasi profil pengguna dan hak akses')

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-2xl bg-white p-4">
                <div class="d-flex align-items-center mb-4">
                    <img src="{{ $targetUser->avatar_url }}" class="w-16 h-16 rounded-full object-cover mr-4 border-2 border-slate-200">
                    <div>
                        <h5 class="font-bold text-slate-800 mb-0">{{ $targetUser->name }}</h5>
                        <span class="text-xs text-slate-400 font-mono">{{ $targetUser->email }} &bull; Role: {{ strtoupper($targetUser->role) }}</span>
                    </div>
                </div>

                <form action="{{ route('users.update', $targetUser->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $targetUser->name) }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" value="{{ old('username', $targetUser->username) }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $targetUser->email) }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">No. Telepon</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $targetUser->phone) }}">
                        </div>

                        @if(Auth::user()->isAdmin())
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Role / Hak Akses</label>
                            <select name="role" class="form-control custom-select">
                                <option value="cashier" {{ $targetUser->role === 'cashier' ? 'selected' : '' }}>Kasir (Hak Akses Terbatas)</option>
                                <option value="admin" {{ $targetUser->role === 'admin' ? 'selected' : '' }}>Administrator (Full Access)</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Status Akun</label>
                            <select name="status" class="form-control custom-select">
                                <option value="active" {{ $targetUser->status === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ $targetUser->status === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        @endif

                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Ganti Password (Kosongkan bila tidak diubah)</label>
                            <input type="password" name="password" class="form-control" placeholder="••••••••">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-700 uppercase">Ganti Foto Profil</label>
                            <input type="file" name="avatar" class="form-control-file border p-1 rounded">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-slate-100">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary font-semibold">Kembali</a>
                        <button type="submit" class="btn btn-primary font-bold px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
