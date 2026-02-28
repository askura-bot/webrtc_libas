{{-- credentials.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kredensial — LIBAS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-libas.png') }}">
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&family=Barlow+Condensed:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --red:       #D70608;
            --red-dark:  #A80406;
            --orange:    #DB8138;
            --cream:     #FDFCB8;
            --white:     #FDFDFE;
            --gray-50:   #F9F4F4;
            --gray-100:  #F0E8E8;
            --gray-200:  #DDD0D0;
            --gray-400:  #B89090;
            --gray-600:  #7A5050;
            --gray-900:  #2A0808;
            --shadow-sm: 0 1px 4px rgba(167,2,6,0.10);
            --shadow-md: 0 4px 16px rgba(167,2,6,0.14);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: var(--gray-50);
            color: var(--gray-900);
            font-family: 'Barlow', sans-serif;
            min-height: 100vh;
        }

        /* ── NAVBAR ── */
        .navbar {
            background: var(--red);
            padding: 0 28px;
            display: flex; align-items: center; justify-content: space-between;
            height: 64px;
            box-shadow: 0 2px 12px rgba(100,0,0,0.25);
            position: sticky; top: 0; z-index: 100;
        }
        .navbar-brand { display: flex; align-items: center; gap: 14px; text-decoration: none; }
        .navbar-logo {
            width: 44px; height: 44px; border-radius: 50%;
            object-fit: contain; background: var(--white);
            padding: 3px; border: 2px solid rgba(255,255,255,0.4); flex-shrink: 0;
        }
        .navbar-logo-placeholder {
            width: 44px; height: 44px; border-radius: 50%;
            background: var(--white); border: 2px solid rgba(255,255,255,0.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        .navbar-title { display: flex; flex-direction: column; gap: 1px; }
        .navbar-title-main {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 800; font-size: 17px;
            color: var(--white); letter-spacing: 0.5px; line-height: 1;
        }
        .navbar-title-sub {
            font-size: 11px; color: rgba(255,255,255,0.72); font-weight: 500; letter-spacing: 0.3px;
        }
        .navbar-right { display: flex; align-items: center; gap: 12px; }
        .btn-nav-link {
            display: flex; align-items: center; gap: 6px;
            padding: 8px 14px;
            background: rgba(255,255,255,0.12); color: var(--white);
            border: 1px solid rgba(255,255,255,0.25); border-radius: 8px;
            text-decoration: none; font-size: 13px; font-weight: 600; transition: background .2s;
        }
        .btn-nav-link:hover { background: rgba(255,255,255,0.22); }
        .btn-logout {
            padding: 8px 18px; background: rgba(0,0,0,0.2); color: var(--white);
            border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; cursor: pointer;
            font-size: 13px; font-family: 'Barlow', sans-serif; font-weight: 600; transition: background .2s;
        }
        .btn-logout:hover { background: rgba(0,0,0,0.35); }

        /* ── PAGE HEADER ── */
        .page-header { padding: 28px 28px 0; }
        .page-header h1 {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 26px; font-weight: 800; color: var(--gray-900); letter-spacing: 0.3px;
        }
        .page-header p { font-size: 13px; color: var(--gray-600); margin-top: 4px; }

        /* ── MAIN ── */
        .main-content {
            max-width: 680px; margin: 28px auto 60px; padding: 0 20px;
            display: flex; flex-direction: column; gap: 24px;
        }

        /* ── FORM CARD ── */
        .form-card {
            background: var(--white); border: 1.5px solid var(--gray-200);
            border-radius: 14px; overflow: hidden; box-shadow: var(--shadow-md);
        }
        .form-card-header {
            background: var(--red); padding: 16px 22px;
            display: flex; align-items: center; gap: 12px;
        }
        .form-card-header-icon {
            width: 36px; height: 36px; border-radius: 9px;
            background: rgba(255,255,255,0.18);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }
        .form-card-header-text {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 17px; font-weight: 800; color: var(--white); letter-spacing: 0.3px;
        }
        .form-card-header-sub { font-size: 11px; color: rgba(255,255,255,0.7); font-weight: 500; margin-top: 1px; }
        .form-card-body { padding: 24px 22px; }

        /* ── ALERTS ── */
        .alert {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 12px 16px; border-radius: 8px;
            font-size: 13px; font-weight: 500; margin-bottom: 20px;
        }
        .alert-success { background: #f0fdf4; border: 1.5px solid #bbf7d0; color: #166534; }
        .alert-error   { background: #fff1f1; border: 1.5px solid #fecaca; color: var(--red-dark); }

        /* ── FORM FIELDS ── */
        .field { margin-bottom: 18px; }
        .field:last-of-type { margin-bottom: 0; }
        .field label {
            display: block; font-size: 11px; font-weight: 700; color: var(--gray-600);
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 7px;
        }
        .field-optional {
            font-size: 10px; color: var(--gray-400); font-weight: 500;
            text-transform: none; letter-spacing: 0; margin-left: 4px;
        }
        .field input[type="email"],
        .field input[type="text"],
        .field input[type="password"] {
            width: 100%; padding: 10px 13px;
            background: var(--gray-50); border: 1.5px solid var(--gray-200);
            border-radius: 8px; color: var(--gray-900);
            font-family: 'Barlow', sans-serif; font-size: 14px;
            outline: none; transition: border-color .2s, box-shadow .2s;
        }
        .field input:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(215,6,8,0.10);
        }
        .field input::placeholder { color: var(--gray-400); }
        .field-error { font-size: 12px; color: var(--red-dark); margin-top: 5px; display: block; }
        .field-divider { height: 1px; background: var(--gray-100); margin: 20px 0; }

        /* ── PASSWORD WRAPPER ── */
        .password-wrapper {
            position: relative; display: flex; align-items: center;
        }
        .password-wrapper input {
            width: 100%;
            padding-right: 44px !important; /* ruang untuk tombol mata */
        }
        .toggle-pw {
            position: absolute; right: 1px; top: 1px; bottom: 1px;
            width: 40px;
            display: flex; align-items: center; justify-content: center;
            background: transparent; border: none; border-radius: 0 7px 7px 0;
            cursor: pointer; color: var(--gray-400);
            transition: color .2s, background .2s;
            flex-shrink: 0;
        }
        .toggle-pw:hover { color: var(--red); background: var(--gray-100); }
        .toggle-pw svg { width: 18px; height: 18px; pointer-events: none; }

        /* ── SUBMIT BUTTON ── */
        .btn-submit {
            width: 100%; margin-top: 22px; padding: 13px 0;
            background: var(--red); color: var(--white); border: none; border-radius: 9px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 16px; font-weight: 700; letter-spacing: 0.5px; cursor: pointer;
            box-shadow: 0 3px 10px rgba(215,6,8,0.28);
            transition: background .2s, transform .2s, box-shadow .2s;
        }
        .btn-submit:hover {
            background: var(--red-dark); transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(215,6,8,0.35);
        }
        .btn-submit:active { transform: translateY(0); }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .navbar { padding: 0 16px; height: 58px; }
            .navbar-title-sub { display: none; }
            .page-header { padding: 20px 16px 0; }
            .main-content { padding: 0 16px; }
        }
    </style>
</head>
<body>

    {{-- ── NAVBAR ── --}}
    <nav class="navbar">
        <a href="#" class="navbar-brand">
            <img src="{{ asset('images/logo-libas.png') }}"
                 alt="Logo Polrestabes Semarang"
                 class="navbar-logo"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="navbar-logo-placeholder" style="display:none;">🚔</div>
            <div class="navbar-title">
                <span class="navbar-title-main">LIBAS</span>
                <span class="navbar-title-sub">Presisi Command Center — Admin</span>
            </div>
        </a>
        <div class="navbar-right">
            <a href="{{ route('admin.dashboard') }}" class="btn-nav-link">← Dashboard</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    {{-- ── PAGE HEADER ── --}}
    <div class="page-header">
        <h1>🔑 Kelola Kredensial</h1>
        <p>Kosongkan field password jika tidak ingin mengubah password.</p>
    </div>

    {{-- ── MAIN ── --}}
    <div class="main-content">

        {{-- FORM OFFICER --}}
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-header-icon">👮</div>
                <div>
                    <div class="form-card-header-text">Kredensial Petugas</div>
                    <div class="form-card-header-sub">Akun login untuk petugas lapangan</div>
                </div>
            </div>
            <div class="form-card-body">

                @if(session('success_officer'))
                    <div class="alert alert-success">✅ {{ session('success_officer') }}</div>
                @endif
                @if($errors->hasBag('default'))
                    <div class="alert alert-error">⚠️ {{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('admin.credentials.officer') }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label>Email Petugas</label>
                        <input type="email" name="officer_email"
                               value="{{ old('officer_email', $officer->email) }}"
                               required placeholder="email@polrestabes.go.id">
                        @error('officer_email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field-divider"></div>

                    <div class="field">
                        <label>Password Baru <span class="field-optional">(opsional)</span></label>
                        <div class="password-wrapper">
                            <input type="password" id="officer_password" name="officer_password"
                                   placeholder="Kosongkan jika tidak ingin mengubah">
                            <button type="button" class="toggle-pw"
                                    onclick="togglePassword('officer_password', this)"
                                    aria-label="Tampilkan/sembunyikan password">
                                <svg id="officer_password-icon-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="officer_password-icon-hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('officer_password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Konfirmasi Password Baru</label>
                        <div class="password-wrapper">
                            <input type="password" id="officer_password_confirmation"
                                   name="officer_password_confirmation"
                                   placeholder="Ulangi password baru">
                            <button type="button" class="toggle-pw"
                                    onclick="togglePassword('officer_password_confirmation', this)"
                                    aria-label="Tampilkan/sembunyikan konfirmasi password">
                                <svg id="officer_password_confirmation-icon-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="officer_password_confirmation-icon-hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Simpan Kredensial Petugas</button>
                </form>
            </div>
        </div>

        {{-- FORM ADMIN --}}
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-header-icon">🛡️</div>
                <div>
                    <div class="form-card-header-text">Kredensial Admin</div>
                    <div class="form-card-header-sub">Akun login untuk administrator sistem</div>
                </div>
            </div>
            <div class="form-card-body">

                @if(session('success_admin'))
                    <div class="alert alert-success">✅ {{ session('success_admin') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.credentials.admin') }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label>Email Admin</label>
                        <input type="email" name="admin_email"
                               value="{{ old('admin_email', $admin->email) }}"
                               required placeholder="admin@polrestabes.go.id">
                        @error('admin_email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field-divider"></div>

                    <div class="field">
                        <label>Password Baru <span class="field-optional">(opsional)</span></label>
                        <div class="password-wrapper">
                            <input type="password" id="admin_password" name="admin_password"
                                   placeholder="Kosongkan jika tidak ingin mengubah">
                            <button type="button" class="toggle-pw"
                                    onclick="togglePassword('admin_password', this)"
                                    aria-label="Tampilkan/sembunyikan password">
                                <svg id="admin_password-icon-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="admin_password-icon-hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('admin_password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label>Konfirmasi Password Baru</label>
                        <div class="password-wrapper">
                            <input type="password" id="admin_password_confirmation"
                                   name="admin_password_confirmation"
                                   placeholder="Ulangi password baru">
                            <button type="button" class="toggle-pw"
                                    onclick="togglePassword('admin_password_confirmation', this)"
                                    aria-label="Tampilkan/sembunyikan konfirmasi password">
                                <svg id="admin_password_confirmation-icon-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="admin_password_confirmation-icon-hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Simpan Kredensial Admin</button>
                </form>
            </div>
        </div>

    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input   = document.getElementById(inputId);
            const iconShow = document.getElementById(inputId + '-icon-show');
            const iconHide = document.getElementById(inputId + '-icon-hide');

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';

            iconShow.style.display = isHidden ? 'none'  : 'block';
            iconHide.style.display = isHidden ? 'block' : 'none';

            btn.style.color = isHidden ? 'var(--red)' : 'var(--gray-400)';
        }
    </script>

</body>
</html>