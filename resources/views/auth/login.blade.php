{{-- login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — LIBAS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-libas.png') }}">
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&family=Barlow+Condensed:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --red:      #D70608;
            --red-dark: #A80406;
            --orange:   #DB8138;
            --cream:    #FDFCB8;
            --white:    #FDFDFE;
            --gray-50:  #F9F4F4;
            --gray-100: #F0E8E8;
            --gray-200: #DDD0D0;
            --gray-400: #B89090;
            --gray-600: #7A5050;
            --gray-900: #2A0808;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Barlow', sans-serif;
        }

        body {
            min-height: 100vh;
            background: var(--gray-50);
            display: flex;
            flex-direction: row;
        }

        /* ══════════════════════════════
           LEFT PANEL — desktop branding
        ══════════════════════════════ */
        .left-panel {
            width: 50%;
            min-height: 100vh;
            background: var(--red);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }

        /* decorative bg circles */
        .left-panel::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            bottom: -160px; left: -120px;
            pointer-events: none;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            top: -80px; right: -80px;
            pointer-events: none;
        }

        .left-logo-wrap {
            position: relative; z-index: 1;
            width: 150px; height: 150px;
            background: var(--white);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 40px rgba(0,0,0,0.25);
            border: 5px solid rgba(255,255,255,0.35);
            margin-bottom: 28px;
            flex-shrink: 0;
        }
        .left-logo-wrap img {
            width: 120px; height: 120px;
            object-fit: contain; border-radius: 50%;
            display: block;
        }
        .left-logo-fallback {
            font-size: 64px; line-height: 1;
            display: none;
        }

        .left-title {
            position: relative; z-index: 1;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 30px; font-weight: 800;
            color: var(--white);
            text-align: center;
            letter-spacing: 0.5px;
            line-height: 1.15;
            margin-bottom: 14px;
        }
        .left-subtitle {
            position: relative; z-index: 1;
            font-size: 13px;
            color: rgba(255,255,255,0.75);
            text-align: center;
            line-height: 1.65;
            max-width: 260px;
        }
        .left-badge {
            position: relative; z-index: 1;
            margin-top: 32px;
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(0,0,0,0.18);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 7px 18px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
            color: var(--cream); letter-spacing: 1.2px;
        }
        .left-badge-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--cream);
            animation: blink 2s ease-in-out infinite;
            flex-shrink: 0;
        }
        @keyframes blink {
            0%,100% { opacity:1; } 50% { opacity:0.25; }
        }

        /* ══════════════════════════════
           RIGHT PANEL — login form
        ══════════════════════════════ */
        .right-panel {
            width: 50%;
            min-height: 100vh;
            background: var(--white);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            flex-shrink: 0;
        }

        .form-wrap {
            width: 100%;
            max-width: 380px;
        }

        .form-heading {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 30px; font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 6px;
        }
        .form-subheading {
            font-size: 13px; color: var(--gray-600);
            margin-bottom: 32px; line-height: 1.5;
        }

        /* Alert */
        .alert-error {
            display: flex; align-items: flex-start; gap: 9px;
            background: #fff1f1; border: 1.5px solid #fecaca;
            color: var(--red-dark);
            padding: 11px 14px; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            margin-bottom: 24px;
        }

        /* Fields */
        .field { margin-bottom: 20px; }
        .field label {
            display: block;
            font-size: 11px; font-weight: 700;
            color: var(--gray-600);
            text-transform: uppercase; letter-spacing: 0.5px;
            margin-bottom: 7px;
        }
        .field input[type="email"],
        .field input[type="text"],
        .field input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            background: var(--gray-50);
            border: 1.5px solid var(--gray-200);
            border-radius: 9px;
            color: var(--gray-900);
            font-family: 'Barlow', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .field input:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(215,6,8,0.10);
        }
        .field input::placeholder { color: var(--gray-400); }

        /* Password wrapper */
        .password-wrapper {
            position: relative;
            display: flex; align-items: center;
        }
        .password-wrapper input { padding-right: 46px !important; }
        .toggle-pw {
            position: absolute; right: 1px; top: 1px; bottom: 1px;
            width: 42px;
            display: flex; align-items: center; justify-content: center;
            background: transparent; border: none;
            border-radius: 0 8px 8px 0;
            cursor: pointer; color: var(--gray-400);
            transition: color .2s, background .2s;
        }
        .toggle-pw:hover { color: var(--red); background: var(--gray-100); }
        .toggle-pw svg { width: 18px; height: 18px; pointer-events: none; }

        /* Submit button */
        .btn-login {
            width: 100%; margin-top: 8px;
            padding: 13px 0;
            background: var(--red); color: var(--white);
            border: none; border-radius: 9px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 17px; font-weight: 700; letter-spacing: 0.5px;
            cursor: pointer;
            box-shadow: 0 3px 12px rgba(215,6,8,0.30);
            transition: background .2s, transform .2s, box-shadow .2s;
        }
        .btn-login:hover {
            background: var(--red-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(215,6,8,0.38);
        }
        .btn-login:active { transform: translateY(0); }

        .form-footer {
            margin-top: 28px; text-align: center;
            font-size: 12px; color: var(--gray-400);
        }

        /* ══════════════════════════════
           MOBILE HEADER — logo di atas form
        ══════════════════════════════ */
        .mobile-header { display: none; }

        /* ══════════════════════════════
           RESPONSIVE — ≤ 768px
        ══════════════════════════════ */
        @media (max-width: 768px) {
            body { flex-direction: column; }

            .left-panel { display: none; }

            .mobile-header {
                display: flex;
                flex-direction: column;
                align-items: center;
                background: var(--red);
                padding: 36px 20px 0;
                position: relative;
                overflow: hidden;
            }
            .mobile-header::before {
                content: '';
                position: absolute;
                width: 220px; height: 220px;
                border-radius: 50%;
                background: rgba(255,255,255,0.06);
                top: -70px; right: -50px;
                pointer-events: none;
            }
            .mobile-logo-wrap {
                position: relative; z-index: 1;
                width: 88px; height: 88px;
                background: var(--white);
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 4px 20px rgba(0,0,0,0.2);
                border: 3px solid rgba(255,255,255,0.4);
                margin-bottom: 12px;
            }
            .mobile-logo-wrap img {
                width: 70px; height: 70px;
                object-fit: contain; border-radius: 50%;
            }
            .mobile-logo-fallback { font-size: 38px; display: none; }
            .mobile-title {
                position: relative; z-index: 1;
                font-family: 'Barlow Condensed', sans-serif;
                font-size: 20px; font-weight: 800;
                color: var(--white); text-align: center;
                margin-bottom: 4px;
            }
            .mobile-subtitle {
                position: relative; z-index: 1;
                font-size: 12px; color: rgba(255,255,255,0.75);
                text-align: center; margin-bottom: 28px;
            }
            .mobile-wave {
                width: 100%; overflow: hidden; line-height: 0;
                background: var(--red);
            }
            .mobile-wave svg { display: block; }

            .right-panel {
                width: 100%;
                min-height: unset;
                flex: 1;
                padding: 32px 24px 48px;
                justify-content: flex-start;
            }
            .form-wrap { max-width: 100%; }
        }
    </style>
</head>
<body>

    {{-- ── LEFT PANEL (desktop) ── --}}
    <div class="left-panel">
        <div class="left-logo-wrap">
            <img src="{{ asset('images/logo-libas.png') }}"
                 alt="Logo Polrestabes Semarang"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <span class="left-logo-fallback">🚔</span>
        </div>
        <div class="left-title">LIBAS</div>
        <div class="left-subtitle">
            Sistem Live Streaming TKP — Monitoring lapangan secara real-time oleh petugas terotorisasi.
        </div>
        <div class="left-badge">
            <div class="left-badge-dot"></div>
            SISTEM AKTIF
        </div>
    </div>

    {{-- ── MOBILE HEADER (mobile only) ── --}}
    <div class="mobile-header">
        <div class="mobile-logo-wrap">
            <img src="{{ asset('images/logo-libas.png') }}"
                 alt="Logo Polrestabes Semarang"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <span class="mobile-logo-fallback">🚔</span>
        </div>
        <div class="mobile-title">Polrestabes Semarang</div>
        <div class="mobile-subtitle">Sistem Live Streaming TKP</div>
    </div>
    <div class="mobile-wave" style="display:none;" id="mobileWave">
        <svg viewBox="0 0 1440 32" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" height="32">
            <path d="M0,0 C360,32 1080,32 1440,0 L1440,0 L0,0 Z" fill="#F9F4F4"/>
        </svg>
    </div>

    {{-- ── RIGHT PANEL (form) ── --}}
    <div class="right-panel">
        <div class="form-wrap">

            <div class="form-heading">Selamat Datang</div>
            <div class="form-subheading">Masuk ke akun Anda untuk melanjutkan.</div>

            @if($errors->any())
                <div class="alert-error">
                    ⚠️ {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email"
                           value="{{ old('email') }}"
                           required autofocus
                           placeholder="Masukkan email Anda">
                </div>

                <div class="field">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="login_password"
                               name="password" required
                               placeholder="Masukkan password Anda">
                        <button type="button" class="toggle-pw"
                                onclick="togglePassword('login_password', this)"
                                aria-label="Tampilkan/sembunyikan password">
                            <svg id="login_password-icon-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="login_password-icon-hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">Masuk</button>
            </form>

            <div class="form-footer">
                © {{ date('Y') }} Polrestabes Semarang. Hak akses terbatas.
            </div>

        </div>
    </div>

    <script>
        // Tampilkan wave divider di mobile
        if (window.innerWidth <= 768) {
            document.getElementById('mobileWave').style.display = 'block';
        }
        window.addEventListener('resize', function() {
            document.getElementById('mobileWave').style.display =
                window.innerWidth <= 768 ? 'block' : 'none';
        });

        function togglePassword(inputId, btn) {
            const input    = document.getElementById(inputId);
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