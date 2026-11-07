@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page_title', 'Manajemen Pengguna & Hak Akses')
@section('page_subtitle', 'Administrator Full Access & Hak Akses Kasir Terbatas')

@section('content')
<div class="container-fluid px-0">

    <!-- Information Alert regarding Kasir and Admin Roles -->
    <div class="card border-0 shadow-sm rounded-2xl bg-blue-50 border-l-4 border-blue-600 p-4 mb-4">
        <div class="d-flex align-items-center">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg mr-3 flex-shrink-0">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div>
                <h6 class="font-bold text-blue-900 mb-0">Kebijakan Hak Akses Sistem (Role Management)</h6>
                <p class="text-xs text-blue-800 mb-0 mt-0.5">
                    1. <strong>Administrator:</strong> Memiliki Full Access ke seluruh modul dan menu (Dashboard, Produk, Laba Rugi, Pengguna, Pengaturan).<br>
                    2. <strong>Kasir:</strong> Dilarang menghapus & mengedit Administrator. Kasir hanya dapat mengedit profil pribadinya dan dilarang mengubah profil kasir lain.
                </p>
            </div>
        </div>
    </div>

    <!-- Top Action Card -->
    <div class="card border-0 shadow-sm rounded-2xl bg-white mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <form action="{{ route('users.index') }}" method="GET" class="form-row align-items-center">
                    <div class="col-auto mb-2 mb-md-0">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control text-sm" placeholder="Cari nama, email, username..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto mb-2 mb-md-0">
                        <select name="role" class="form-control custom-select text-sm" onchange="this.form.submit()">
                            <option value="">Semua Role</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Kasir</option>
                        </select>
                    </div>
                </form>

                <div>
                    <button type="button" class="btn btn-primary rounded-xl font-bold px-4 py-2 shadow-sm" data-toggle="modal" data-target="#addUserModal">
                        <i class="fa-solid fa-user-plus mr-1"></i> Tambah Pengguna Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm rounded-2xl bg-white overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3">Username & Email</th>
                        <th class="py-3">No. Telepon</th>
                        <th class="py-3 text-center">Role / Hak Akses</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr>
                        <td class="py-3 px-4">
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full object-cover mr-3 border border-slate-200">
                                <div>
                                    <div class="font-bold text-slate-800">{{ $user->name }}</div>
                                    @if(Auth::id() === $user->id)
                                        <span class="badge badge-light border text-2xs text-blue-600 font-bold">AKUN ANDA</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="font-mono text-xs text-slate-800">{{ $user->username }}</div>
                            <div class="text-xs text-slate-400">{{ $user->email }}</div>
                        </td>
                        <td class="py-3 text-xs text-slate-600">
                            {{ $user->phone ?: '-' }}
                        </td>
                        <td class="py-3 text-center">
                            @if($user->isAdmin())
                                <span class="badge badge-danger font-bold text-xs px-2.5 py-1">
                                    <i class="fa-solid fa-shield-halved mr-1"></i> ADMINISTRATOR
                                </span>
                            @else
                                <span class="badge badge-info font-bold text-xs px-2.5 py-1">
                                    <i class="fa-solid fa-cash-register mr-1"></i> KASIR
                                </span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            @if($user->status === 'active')
                                <span class="badge badge-pill badge-success text-xs">Aktif</span>
                            @else
                                <span class="badge badge-pill badge-secondary text-xs">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-primary rounded-lg mr-1" title="Edit Pengguna">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @if(Auth::id() !== $user->id)
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger rounded-lg" title="Hapus Pengguna">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-slate-400">Tidak ada pengguna ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top border-slate-100">
            {{ $users->links('pagination::bootstrap-4') }}
        </div>
    </div>

</div>

<!-- Modal Add User -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-2xl shadow-xl overflow-hidden">
            <div class="modal-header bg-blue-600 text-white py-3 px-4">
                <h5 class="modal-title font-bold text-white"><i class="fa-solid fa-user-plus mr-2"></i> Tambah Pengguna Baru</h5>
                <button type="button" class="close text-white opacity-90" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-700 uppercase">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Nama pegawai / kasir" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-700 uppercase">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" placeholder="username untuk login" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-700 uppercase">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="email@contoh.com" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-700 uppercase">Password Login <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-700 uppercase">Role / Hak Akses <span class="text-danger">*</span></label>
                        <select name="role" class="form-control custom-select" required>
                            <option value="cashier" selected>Kasir (Hanya Akses POS Kasir & Transaksi)</option>
                            <option value="admin">Administrator (Full Access Seluruh Modul)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-700 uppercase">Nomor HP / WhatsApp</label>
                        <input type="text" name="phone" class="form-control" placeholder="0812xxxxxxxx">
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-bold text-slate-700 uppercase">Status Akun</label>
                        <select name="status" class="form-control custom-select" required>
                            <option value="active" selected>Aktif (Bisa Login)</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-slate-50 py-3 px-4">
                    <button type="button" class="btn btn-secondary font-semibold" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-bold px-4">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
