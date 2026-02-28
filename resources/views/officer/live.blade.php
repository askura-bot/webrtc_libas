{{-- live.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Stream — Petugas | LIBAS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-libas.png') }}">
    @vite(['resources/js/stream.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&family=Barlow+Condensed:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --red:        #D70608;
            --red-dark:   #A80406;
            --red-deep:   #7A0203;
            --orange:     #DB8138;
            --cream:      #FDFCB8;
            --cream-dim:  #F5F0A0;
            --white:      #FDFDFE;
            --gray-50:    #F9F4F4;
            --gray-100:   #F0E8E8;
            --gray-200:   #DDD0D0;
            --gray-400:   #B89090;
            --gray-600:   #7A5050;
            --gray-900:   #2A0808;
            --shadow-sm:  0 1px 4px rgba(167,2,6,0.10);
            --shadow-md:  0 4px 16px rgba(167,2,6,0.14);
            --shadow-lg:  0 8px 32px rgba(167,2,6,0.18);
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
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            box-shadow: 0 2px 12px rgba(100,0,0,0.25);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }
        .navbar-logo {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: contain;
            background: var(--white);
            padding: 3px;
            border: 2px solid rgba(255,255,255,0.4);
            flex-shrink: 0;
        }
        .navbar-logo-placeholder {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--white);
            border: 2px solid rgba(255,255,255,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .navbar-title {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }
        .navbar-title-main {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 800;
            font-size: 17px;
            color: var(--white);
            letter-spacing: 0.5px;
            line-height: 1;
        }
        .navbar-title-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.72);
            font-weight: 500;
            letter-spacing: 0.3px;
        }
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .live-indicator {
            display: none;
            align-items: center;
            gap: 6px;
            background: rgba(0,0,0,0.25);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            color: var(--cream);
            letter-spacing: 1px;
        }
        .live-indicator.active { display: flex; }
        .live-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--cream);
            animation: pulse 1.2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100% { opacity:1; transform:scale(1); }
            50%      { opacity:0.4; transform:scale(0.8); }
        }
        .btn-logout {
            padding: 8px 18px;
            background: rgba(0,0,0,0.2);
            color: var(--white);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-family: 'Barlow', sans-serif;
            font-weight: 600;
            transition: background .2s, border-color .2s;
        }
        .btn-logout:hover {
            background: rgba(0,0,0,0.35);
            border-color: rgba(255,255,255,0.5);
        }

        /* ── LAYOUT ── */
        .main-container {
            max-width: 1100px;
            margin: 32px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 24px;
            align-items: start;
        }

        /* ── SECTION HEADER ── */
        .section-header {
            margin-bottom: 14px;
        }
        .section-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: var(--gray-900);
            letter-spacing: 0.3px;
        }
        .section-sub {
            font-size: 13px;
            color: var(--gray-600);
            margin-top: 3px;
        }

        /* ── VIDEO CARD ── */
        .video-card {
            background: var(--white);
            border-radius: 14px;
            border: 1.5px solid var(--gray-200);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }
        .video-wrapper {
            position: relative;
            background: #1a0303;
            min-height: 340px;
        }
        #localVideo {
            width: 100%;
            display: block;
            min-height: 340px;
            object-fit: cover;
        }
        .video-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            background: var(--red);
            color: var(--white);
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            display: none;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 8px rgba(215,6,8,0.4);
        }
        .video-badge.active { display: flex; }
        .video-badge-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--cream);
            animation: pulse 1s infinite;
        }
        .video-stream-id {
            position: absolute;
            top: 14px;
            right: 14px;
            background: rgba(0,0,0,0.6);
            color: rgba(255,255,255,0.75);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-family: monospace;
            display: none;
        }

        /* ── STATUS BAR ── */
        .status-bar {
            padding: 13px 18px;
            background: var(--gray-50);
            border-top: 1.5px solid var(--gray-100);
            font-size: 13px;
            font-weight: 500;
            color: var(--gray-600);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color .3s;
        }
        .status-bar::before {
            content: '';
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--gray-400);
            flex-shrink: 0;
            transition: background .3s;
        }
        .status-bar.success { color: #166534; }
        .status-bar.success::before { background: #22c55e; }
        .status-bar.error   { color: var(--red-dark); }
        .status-bar.error::before   { background: var(--red); }
        .status-bar.warning { color: #854d0e; }
        .status-bar.warning::before { background: var(--orange); }

        /* ── ACTION BUTTONS ── */
        .action-row {
            display: flex;
            gap: 12px;
            padding: 14px 16px;
            border-top: 1.5px solid var(--gray-100);
        }
        .btn-primary, .btn-secondary {
            flex: 1;
            padding: 13px 0;
            border: none;
            border-radius: 9px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all .2s;
        }
        .btn-primary {
            background: var(--red);
            color: var(--white);
            box-shadow: 0 3px 10px rgba(215,6,8,0.30);
        }
        .btn-primary:hover:not(:disabled) {
            background: var(--red-dark);
            transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(215,6,8,0.38);
        }
        .btn-primary:disabled {
            background: var(--gray-200);
            color: var(--gray-400);
            cursor: not-allowed;
            box-shadow: none;
        }
        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-600);
            border: 1.5px solid var(--gray-200);
        }
        .btn-secondary:hover:not(:disabled) {
            background: var(--gray-200);
        }
        .btn-secondary:disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }
        .btn-secondary.active {
            background: var(--gray-900);
            color: var(--white);
            border-color: var(--gray-900);
        }
        .btn-secondary.active:hover {
            background: #3d0a0a;
        }

        /* ── STATS GRID ── */
        .stats-grid {
            display: none;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 18px;
        }
        .stat-card {
            background: var(--white);
            border: 1.5px solid var(--gray-200);
            border-radius: 10px;
            padding: 13px 14px;
            box-shadow: var(--shadow-sm);
        }
        .stat-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--gray-400);
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 6px;
        }
        .stat-value {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--gray-900);
            line-height: 1;
        }
        .stat-card.green  .stat-value { color: #166534; }
        .stat-card.blue   .stat-value { color: #1e40af; }
        .stat-card.red    .stat-value { color: var(--red-dark); }
        .stat-card.orange .stat-value { color: #92400e; }
        .stat-card.purple .stat-value { color: #6b21a8; }
        .audio-bar-bg {
            background: var(--gray-100);
            border-radius: 4px;
            height: 5px;
            overflow: hidden;
            margin-top: 8px;
        }
        #audioLevelBar {
            height: 100%;
            width: 0%;
            background: #22c55e;
            border-radius: 4px;
            transition: width .3s, background .3s;
        }

        /* ── SETTINGS PANEL ── */
        .settings-card {
            background: var(--white);
            border: 1.5px solid var(--gray-200);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }
        .settings-header {
            background: var(--red);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .settings-header-icon {
            width: 34px; height: 34px;
            border-radius: 8px;
            background: rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        .settings-header-text {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 0.3px;
        }
        .settings-body {
            padding: 20px;
        }

        /* ── TAB BAR ── */
        .tab-bar {
            display: flex;
            background: var(--gray-100);
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 22px;
            gap: 4px;
        }
        .tab-btn {
            flex: 1;
            padding: 9px 0;
            border: none;
            border-radius: 7px;
            background: transparent;
            color: var(--gray-600);
            font-family: 'Barlow', sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
        }
        .tab-btn.active {
            background: var(--red);
            color: var(--white);
            box-shadow: 0 2px 8px rgba(215,6,8,0.25);
        }

        /* ── FORM FIELDS ── */
        .field { margin-bottom: 18px; }
        .field:last-child { margin-bottom: 0; }
        .field label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 7px;
        }
        .field select, .field input[type="number"] {
            width: 100%;
            padding: 9px 12px;
            background: var(--gray-50);
            border: 1.5px solid var(--gray-200);
            border-radius: 8px;
            color: var(--gray-900);
            font-family: 'Barlow', sans-serif;
            font-size: 14px;
            appearance: none;
            -webkit-appearance: none;
            outline: none;
            transition: border-color .2s;
        }
        .field select:focus, .field input[type="number"]:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(215,6,8,0.10);
        }
        .field-hint {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            font-size: 11px;
            color: var(--gray-400);
        }
        .field input[type="range"] {
            width: 100%;
            accent-color: var(--red);
            margin-top: 4px;
        }

        /* ── TOGGLE SWITCH ── */
        .switch-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 0;
            border-bottom: 1px solid var(--gray-100);
        }
        .switch-row:last-child { border-bottom: none; }
        .switch-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-900);
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px; height: 24px;
            flex-shrink: 0;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: var(--gray-200);
            border-radius: 24px;
            transition: .3s;
        }
        .slider:before {
            content: "";
            position: absolute;
            width: 18px; height: 18px;
            left: 3px; bottom: 3px;
            background: var(--white);
            border-radius: 50%;
            transition: .3s;
            box-shadow: 0 1px 4px rgba(0,0,0,0.18);
        }
        input:checked + .slider { background: var(--red); }
        input:checked + .slider:before { transform: translateX(20px); }

        /* ── DIVIDER ── */
        .divider {
            height: 1px;
            background: var(--gray-100);
            margin: 16px 0;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .main-container {
                grid-template-columns: 1fr;
                margin: 20px auto;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .navbar { padding: 0 16px; height: 58px; }
            .navbar-title-main { font-size: 15px; }
            .navbar-title-sub  { display: none; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

    {{-- ── NAVBAR ── --}}
    <nav class="navbar">
        <a href="#" class="navbar-brand">
            {{-- Ganti src dengan path logo Anda, misal: asset('images/logo-polrestabes.png') --}}
            <img src="{{ asset('images/logo-libas.png') }}"
                 alt="Logo Polrestabes Semarang"
                 class="navbar-logo"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="navbar-logo-placeholder" style="display:none;">🚔</div>
            <div class="navbar-title">
                <span class="navbar-title-main">LIBAS</span>
                <span class="navbar-title-sub">Presisi Command Center — Petugas</span>
            </div>
        </a>

        <div class="navbar-right">
            <div class="live-indicator" id="navLiveBadge">
                <div class="live-dot"></div>
                LIVE
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    {{-- ── MAIN ── --}}
    <div class="main-container">

        {{-- Kolom Kiri --}}
        <div>

            {{-- Video Card --}}
            <div class="video-card">
                <div class="video-wrapper">
                    <video id="localVideo" autoplay muted controls playsinline></video>

                    <div class="video-badge" id="liveBadge">
                        <div class="video-badge-dot"></div>
                        LIVE
                    </div>
                    <div class="video-stream-id" id="streamIdOverlay"></div>
                </div>

                {{-- Status --}}
                <div class="status-bar" id="status">
                    Siap memulai siaran...
                </div>

                {{-- Input lokasi --}}
                <div id="locationBar" style="display:none;"
                    class="status-bar"
                    style="border-top: 1px solid var(--gray-100); background: #fffbea; color: #7a5c00; gap: 8px; font-size:13px;">
                    📍 <span id="locationText">Mengambil lokasi...</span>
                </div>

                {{-- Controls --}}
                <div class="action-row">
                    <button id="btnStart" class="btn-primary" onclick="startLive()">
                        ▶ Mulai Live
                    </button>
                    <button id="btnStop" class="btn-secondary" onclick="stopLive()" disabled>
                        ⏹ Stop
                    </button>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div id="statsBox" class="stats-grid">
                <div class="stat-card green">
                    <div class="stat-label">Bitrate Rata-rata</div>
                    <div id="statBitrateAvg" class="stat-value">—</div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-label">Bitrate Terkini</div>
                    <div id="statBitrateCurrent" class="stat-value">—</div>
                </div>
                <div class="stat-card red">
                    <div class="stat-label">Paket Hilang</div>
                    <div id="statPacketLost" class="stat-value">—</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-label">Jitter</div>
                    <div id="statJitter" class="stat-value">—</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-label">RTT</div>
                    <div id="statRtt" class="stat-value">—</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-label">FPS</div>
                    <div id="statFps" class="stat-value">—</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Resolusi Sumber</div>
                    <div id="statResSource" class="stat-value">—</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Resolusi Output</div>
                    <div id="statResOutput" class="stat-value">—</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-label">Level Audio</div>
                    <div id="statAudioLevel" class="stat-value">—</div>
                    <div class="audio-bar-bg">
                        <div id="audioLevelBar"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Settings --}}
        <div class="settings-card">
            <div class="settings-header">
                <div class="settings-header-icon">⚙️</div>
                <span class="settings-header-text">Pengaturan Stream</span>
            </div>
            <div class="settings-body">

                {{-- Tab Bar --}}
                <div class="tab-bar">
                    <button class="tab-btn active" id="tabVideo" onclick="switchTab('video')">🎥 Video</button>
                    <button class="tab-btn" id="tabAudio" onclick="switchTab('audio')">🎙️ Audio</button>
                </div>

                {{-- VIDEO PANEL --}}
                <div id="videoPanel">
                    <div class="field">
                        <label>📷 Pilih Kamera</label>
                        <select id="cameraSelect"></select>
                    </div>

                    <div class="field">
                        <label>📶 Bitrate (kbps)</label>
                        <input type="number" id="bitrateInput" value="1200" min="300" max="4000" step="100">
                        <div class="field-hint">
                            <span>300 kbps</span>
                            <span style="color:var(--gray-600); font-weight:600;">Default: 1200</span>
                            <span>4000 kbps</span>
                        </div>
                    </div>
                </div>

                {{-- AUDIO PANEL --}}
                <div id="audioPanel" style="display:none;">
                    <div class="field">
                        <label>🎙️ Pilih Mikrofon</label>
                        <select id="micSelect"></select>
                    </div>

                    <div class="field">
                        <label>
                            🔊 Volume Mikrofon —
                            <span id="micVolumeLabel" style="color:var(--red); font-weight:700;">100%</span>
                        </label>
                        <input type="range" id="micVolume" min="0" max="2" step="0.05" value="1"
                            oninput="document.getElementById('micVolumeLabel').innerText = Math.round(this.value * 100) + '%'">
                        <div class="field-hint">
                            <span>0%</span><span>100%</span><span>200%</span>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="field">
                        <div class="switch-row">
                            <span class="switch-label">🔇 Noise Suppression</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="noiseSuppression" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="switch-row">
                            <span class="switch-label">🔁 Echo Cancellation</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="echoCancellation" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Patch setLiveState to also update navbar live badge
        const _origSetLiveState = window.setLiveState;
        document.addEventListener('DOMContentLoaded', function() {
            // Hook into stream.js setLiveState via override after load
        });

        // Override status styling
        const _origLog = window.log;
        window.__patchStatus = function() {
            const statusEl = document.getElementById('status');
            const observer = new MutationObserver(() => {
                // handled inline via stream.js
            });
        };

        // Patch log to apply class-based status
        window.log = function(msg, type = 'info') {
            const el = document.getElementById('status');
            const navBadge = document.getElementById('navLiveBadge');
            el.className = 'status-bar ' + (type === 'success' ? 'success' : type === 'error' ? 'error' : type === 'warning' ? 'warning' : '');
            el.innerText = msg;

            if (type === 'success' && msg.includes('aktif')) {
                navBadge.classList.add('active');
                document.getElementById('liveBadge').classList.add('active');
                document.getElementById('streamIdOverlay').style.display = 'block';
            } else if (type === 'warning' || type === 'error') {
                navBadge.classList.remove('active');
                document.getElementById('liveBadge').classList.remove('active');
            }

            console.log(msg);
        };

        // Override setLiveState for new buttons
        window.setLiveState = function(isLive) {
            const btnStart  = document.getElementById('btnStart');
            const btnStop   = document.getElementById('btnStop');
            const statsBox  = document.getElementById('statsBox');
            const overlay   = document.getElementById('streamIdOverlay');
            const navBadge  = document.getElementById('navLiveBadge');
            const liveBadge = document.getElementById('liveBadge');

            if (isLive) {
                btnStart.disabled = true;
                btnStop.disabled  = false;
                btnStop.classList.add('active');
                liveBadge.classList.add('active');
                navBadge.classList.add('active');
                overlay.style.display = 'block';
                overlay.innerText     = `ID: ${window.streamId || ''}`;
                statsBox.style.display = 'grid';
                if (typeof startStats === 'function') startStats();
            } else {
                btnStart.disabled = false;
                btnStop.disabled  = true;
                btnStop.classList.remove('active');
                liveBadge.classList.remove('active');
                navBadge.classList.remove('active');
                overlay.style.display  = 'none';
                statsBox.style.display = 'none';
                if (typeof stopStats  === 'function') stopStats();
                if (typeof resetStats === 'function') resetStats();
            }
        };
    </script>

</body>
</html>