<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login POS System Online</title>

    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            min-height: 100vh;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <!-- App Logo & Title -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-600 text-white text-3xl shadow-lg mb-3">
            <i class="fa-solid fa-cash-register"></i>
        </div>
        <h2 class="text-2xl font-black text-white tracking-tight">{{ \App\Models\Setting::get('store_name', 'POS PRO SYSTEM') }}</h2>
        <p class="text-slate-300 text-sm mt-1">Sistem Point of Sale & Gateway QRIS, Binance, BCA</p>
    </div>

    <div class="login-card p-6 p-sm-8">
        <h4 class="font-bold text-slate-800 text-center mb-2">Masuk ke Akun</h4>
        <p class="text-center text-xs text-slate-500 mb-6">Silakan pilih atau masukkan kredensial login Anda</p>

        @if(session('error'))
            <div class="alert alert-danger text-sm py-2 px-3 rounded-lg flex items-center gap-2 mb-4">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success text-sm py-2 px-3 rounded-lg flex items-center gap-2 mb-4">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            
            <div class="form-group mb-4">
                <label for="login" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Username atau Email</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-slate-100 border-slate-300 text-slate-500"><i class="fa-solid fa-user"></i></span>
                    </div>
                    <input type="text" name="login" id="login" class="form-control form-control-lg text-sm border-slate-300 rounded-right" placeholder="admin atau kasir" value="{{ old('login') }}" required autofocus>
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="password" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Password</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-slate-100 border-slate-300 text-slate-500"><i class="fa-solid fa-lock"></i></span>
                    </div>
                    <input type="password" name="password" id="password" class="form-control form-control-lg text-sm border-slate-300 rounded-right" placeholder="••••••••" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                    <label class="custom-control-label text-xs text-slate-600" for="remember">Ingat Saya</label>
                </div>
                <span class="text-xs text-blue-600 font-semibold cursor-pointer">Bantuan Login?</span>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg rounded-xl font-bold shadow-md hover:shadow-lg transition-all py-3">
                <i class="fa-solid fa-arrow-right-to-bracket mr-2"></i> Masuk Sekarang
            </button>
        </form>

        <!-- Quick 1-Click Demo Buttons -->
        <div class="mt-6 pt-4 border-t border-slate-200">
            <p class="text-xs text-slate-500 text-center font-semibold mb-2">1-Click Demo Login:</p>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="fillCredentials('admin', 'password')" class="btn btn-sm btn-outline-primary rounded-lg text-xs py-2 flex items-center justify-center gap-1 font-semibold">
                    <i class="fa-solid fa-shield-halved"></i> Administrator
                </button>
                <button type="button" onclick="fillCredentials('kasir', 'password')" class="btn btn-sm btn-outline-success rounded-lg text-xs py-2 flex items-center justify-center gap-1 font-semibold">
                    <i class="fa-solid fa-cash-register"></i> Kasir 1 (Budi)
                </button>
            </div>
        </div>
    </div>


</div>

<script>
    function fillCredentials(username, password) {
        document.getElementById('login').value = username;
        document.getElementById('password').value = password;
    }
</script>
</body>
</html>
