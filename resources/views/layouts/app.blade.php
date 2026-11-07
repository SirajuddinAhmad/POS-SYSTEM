<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ \App\Models\Setting::get('store_name', 'POS PRO') }}</title>

    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Play CDN for utility styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            transition: all 0.3s ease;
        }
        .sidebar .nav-link {
            color: #94a3b8;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }
        .sidebar .nav-link.active {
            color: #ffffff;
            background: #2563eb;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }
        .sidebar .nav-link i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }
        .content-area {
            flex: 1;
            min-width: 0;
            overflow-x: hidden;
        }
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        .badge-role-admin {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: 600;
        }
        .badge-role-cashier {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
        }
        @media print {
            .sidebar, nav.navbar, .btn, .no-print {
                display: none !important;
            }
            .content-area {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            body {
                background: #fff !important;
            }
            .card {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

<div class="d-flex">
    <!-- Sidebar -->
    <aside class="sidebar d-none d-lg-flex flex-column p-3 text-white flex-shrink-0">
        <!-- Brand Header -->
        <div class="d-flex align-items-center gap-2 mb-4 px-2 pt-2">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-md font-bold text-xl">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div class="ml-2 overflow-hidden">
                <h5 class="font-bold text-white mb-0 text-truncate">{{ \App\Models\Setting::get('store_name', 'POS PRO') }}</h5>
                <span class="text-xs text-blue-400 font-semibold tracking-wider uppercase">Online System</span>
            </div>
        </div>

        <hr class="border-slate-700 my-2">

        <!-- Navigation Links -->
        <ul class="nav nav-pills flex-column mb-auto">
            @if(Auth::user()->isAdmin())
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Dashboard Admin</span>
                </a>
            </li>
            @endif

            <li class="nav-item">
                <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-desktop"></i>
                    <span>Mesin Kasir (POS)</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('pos.history') }}" class="nav-link {{ request()->routeIs('pos.history') ? 'active' : '' }}">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Riwayat Transaksi</span>
                </a>
            </li>

            @if(Auth::user()->isAdmin())
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider px-3 mt-4 mb-2">Master Data</div>

            <li class="nav-item">
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Katalog Produk</span>
                </a>
            </li>

            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider px-3 mt-4 mb-2">Keuangan & Laba</div>

            <li class="nav-item">
                <a href="{{ route('reports.profit-loss') }}" class="nav-link {{ request()->routeIs('reports.profit-loss*') ? 'active' : '' }}">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span>Laba Bersih & Rugi</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                    <span>Beban Operasional</span>
                </a>
            </li>

            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider px-3 mt-4 mb-2">Pengaturan & User</div>

            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Manajemen User</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sliders"></i>
                    <span>Pengaturan Toko </span>
                </a>
            </li>
            @endif

            <li class="nav-item mt-3">
                <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                    <i class="fa-solid fa-circle-user"></i>
                    <span>Profil Saya</span>
                </a>
            </li>
        </ul>

        <hr class="border-slate-700 my-3">

        <!-- User Info Bottom -->
        <div class="d-flex align-items-center justify-content-between px-2">
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <img src="{{ Auth::user()->avatar_url }}" alt="avatar" class="rounded-full w-9 h-9 object-cover border border-slate-600">
                <div class="ml-2 overflow-hidden">
                    <p class="font-semibold text-white text-sm mb-0 text-truncate">{{ Auth::user()->name }}</p>
                    <span class="badge {{ Auth::user()->isAdmin() ? 'badge-danger' : 'badge-info' }} text-xs">
                        {{ strtoupper(Auth::user()->role) }}
                    </span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="button" class="btn btn-sm btn-outline-danger border-0 text-slate-400 hover:text-white" onclick="confirmLogout()" title="Keluar">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="content-area d-flex flex-column min-vh-100">
        <!-- Top Navbar -->
        <header class="bg-white border-bottom border-slate-200 sticky-top px-4 py-3 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <button class="btn btn-light d-lg-none mr-3" type="button" data-toggle="collapse" data-target="#mobileSidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div>
                        <h4 class="font-bold text-slate-800 mb-0">@yield('page_title', 'Dashboard')</h4>
                        <span class="text-xs text-slate-500">@yield('page_subtitle', 'Sistem Kasir & Manajemen Penjualan Online')</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                
                   

                    <!-- Quick POS Button for Cashiers/Admins -->
                    <a href="{{ route('pos.index') }}" class="btn btn-primary btn-sm rounded-lg font-semibold px-3 py-2 shadow-sm d-none d-sm-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-cash-register"></i> Buka Kasir
                    </a>

                    <!-- User Menu Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm dropdown-toggle rounded-full p-1 pl-2 pr-3 d-flex align-items-center gap-2" type="button" data-toggle="dropdown">
                            <img src="{{ Auth::user()->avatar_url }}" class="rounded-full w-7 h-7 object-cover">
                            <span class="font-medium text-xs text-slate-700 ml-1 d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 rounded-xl mt-2">
                            <h6 class="dropdown-header text-slate-400 text-xs">Role: {{ strtoupper(Auth::user()->role) }}</h6>
                            <a class="dropdown-item py-2 text-sm" href="{{ route('profile') }}">
                                <i class="fa-solid fa-user-pen mr-2 text-blue-500"></i> Edit Profil Pribadi
                            </a>
                            @if(Auth::user()->isAdmin())
                            <a class="dropdown-item py-2 text-sm" href="{{ route('settings.index') }}">
                                <i class="fa-solid fa-gear mr-2 text-slate-500"></i> Pengaturan Toko
                            </a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item py-2 text-sm text-danger" href="javascript:void(0)" onclick="confirmLogout()">
                                <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Sidebar Collapse -->
            <div class="collapse d-lg-none mt-3" id="mobileSidebar">
                <div class="bg-slate-900 rounded-xl p-3 text-white">
                    <ul class="nav flex-column">
                        @if(Auth::user()->isAdmin())
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-chart-pie mr-2"></i> Dashboard Admin</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('products.index') }}"><i class="fa-solid fa-boxes-stacked mr-2"></i> Produk</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('reports.profit-loss') }}"><i class="fa-solid fa-scale-balanced mr-2"></i> Laba Bersih & Rugi</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('expenses.index') }}"><i class="fa-solid fa-money-bill mr-2"></i> Beban Operasional</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('users.index') }}"><i class="fa-solid fa-users mr-2"></i> User Management</a></li>
                        @endif
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('pos.index') }}"><i class="fa-solid fa-desktop mr-2"></i> Mesin Kasir</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('pos.history') }}"><i class="fa-solid fa-receipt mr-2"></i> Riwayat Transaksi</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="{{ route('profile') }}"><i class="fa-solid fa-circle-user mr-2"></i> Profil Pribadi</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="p-4 flex-grow-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top border-slate-200 py-3 px-4 text-center text-xs text-slate-500">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span>&copy; {{ date('Y') }} <strong>{{ \App\Models\Setting::get('store_name', 'POS PRO') }}</strong> - Sistem Point of Sale </span>
                
            </div>
        </footer>
    </div>
</div>

<!-- Scripts -->
<!-- jQuery 3.6 -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<!-- Bootstrap 4.6 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // SweetAlert Flash Message Handlers
    @if(session('swal_success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            html: {!! json_encode(session('swal_success')) !!},
            timer: 3500,
            showConfirmButton: true,
            confirmButtonColor: '#2563eb'
        });
    @endif

    @if(session('swal_error'))
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan!',
            html: {!! json_encode(session('swal_error')) !!},
            showConfirmButton: true,
            confirmButtonColor: '#ef4444'
        });
    @endif

    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: {!! json_encode(session('success')) !!},
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Akses Ditolak!',
            text: {!! json_encode(session('error')) !!},
            confirmButtonColor: '#ef4444'
        });
    @endif

    function confirmLogout() {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: 'Apakah Anda yakin ingin keluar dari sistem?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fa-solid fa-right-from-bracket mr-1"></i> Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }

    // CSRF Ajax Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

@stack('scripts')
</body>
</html>
