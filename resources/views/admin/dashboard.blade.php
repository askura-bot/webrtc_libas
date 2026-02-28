{{-- dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Command Center — Polrestabes Semarang</title>
    @vite(['resources/js/watch.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&family=Barlow+Condensed:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --red:       #D70608;
            --red-dark:  #A80406;
            --red-deep:  #7A0203;
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
            width: 44px; height: 44px;
            border-radius: 50%;
            object-fit: contain;
            background: var(--white);
            padding: 3px;
            border: 2px solid rgba(255,255,255,0.4);
            flex-shrink: 0;
        }
        .navbar-logo-placeholder {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: var(--white);
            border: 2px solid rgba(255,255,255,0.4);
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
            font-size: 11px; color: rgba(255,255,255,0.72);
            font-weight: 500; letter-spacing: 0.3px;
        }
        .navbar-right { display: flex; align-items: center; gap: 12px; }
        .poll-status {
            font-size: 12px; color: rgba(255,255,255,0.65);
            font-weight: 500; white-space: nowrap;
        }
        .btn-nav-link {
            display: flex; align-items: center; gap: 6px;
            padding: 8px 14px;
            background: rgba(255,255,255,0.12);
            color: var(--white);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px; font-weight: 600;
            transition: background .2s;
        }
        .btn-nav-link:hover { background: rgba(255,255,255,0.22); }
        .btn-logout {
            padding: 8px 18px;
            background: rgba(0,0,0,0.2);
            color: var(--white);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 8px; cursor: pointer;
            font-size: 13px; font-family: 'Barlow', sans-serif;
            font-weight: 600; transition: background .2s;
        }
        .btn-logout:hover { background: rgba(0,0,0,0.35); }

        /* ── PAGE HEADER ── */
        .page-header {
            padding: 28px 28px 0;
            display: flex; align-items: center; justify-content: space-between;
        }
        .page-header-left h1 {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 26px; font-weight: 800;
            color: var(--gray-900); letter-spacing: 0.3px;
        }
        .page-header-left p {
            font-size: 13px; color: var(--gray-600); margin-top: 3px;
        }
        .stream-count-badge {
            display: flex; align-items: center; gap: 8px;
            background: var(--white);
            border: 1.5px solid var(--gray-200);
            border-radius: 24px;
            padding: 7px 18px;
            box-shadow: var(--shadow-sm);
        }
        .stream-count-dot {
            width: 9px; height: 9px; border-radius: 50%;
            background: var(--gray-400);
            transition: background .3s;
        }
        .stream-count-dot.active {
            background: #22c55e;
            box-shadow: 0 0 0 3px rgba(34,197,94,0.2);
            animation: pulse-green 1.5s infinite;
        }
        @keyframes pulse-green {
            0%,100% { box-shadow: 0 0 0 3px rgba(34,197,94,0.2); }
            50%      { box-shadow: 0 0 0 6px rgba(34,197,94,0.08); }
        }
        .stream-count-text {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 15px; font-weight: 700;
            color: var(--gray-900);
        }

        /* ── MAIN GRID ── */
        .main-content { padding: 20px 28px 40px; }
        .stream-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--gray-400);
        }
        .empty-state-icon {
            font-size: 52px; margin-bottom: 16px;
            opacity: 0.6;
        }
        .empty-state h2 {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 20px; font-weight: 700;
            color: var(--gray-600); margin-bottom: 8px;
        }
        .empty-state p { font-size: 13px; color: var(--gray-400); }

        /* ── STREAM CARD ── */
        .stream-card {
            background: var(--white);
            border: 1.5px solid var(--gray-200);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: transform .2s, box-shadow .2s;
            animation: cardIn .3s ease;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .stream-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(167,2,6,0.18);
        }

        /* Video wrapper */
        .card-video-wrapper {
            position: relative;
            background: #1a0303;
        }
        .card-video {
            width: 100%; height: 220px;
            object-fit: cover; display: block;
        }
        .card-live-badge {
            position: absolute; top: 12px; left: 12px;
            background: var(--red);
            color: var(--white);
            padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700; letter-spacing: 1.5px;
            display: flex; align-items: center; gap: 5px;
            box-shadow: 0 2px 8px rgba(215,6,8,0.4);
        }
        .card-live-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--cream);
            animation: pulse-dot 1s infinite;
        }
        @keyframes pulse-dot {
            0%,100% { opacity:1; } 50% { opacity:0.3; }
        }
        .card-stream-id-overlay {
            position: absolute; top: 12px; right: 12px;
            background: rgba(0,0,0,0.55);
            color: rgba(255,255,255,0.7);
            padding: 3px 9px; border-radius: 5px;
            font-size: 10px; font-family: monospace;
        }

        /* Card body */
        .card-body { padding: 16px; }

        .card-info-row {
            display: flex; align-items: flex-start; gap: 8px;
            padding: 7px 0;
            border-bottom: 1px solid var(--gray-100);
            font-size: 13px;
        }
        .card-info-row:last-of-type { border-bottom: none; }
        .card-info-icon {
            font-size: 14px; flex-shrink: 0;
            margin-top: 1px;
        }
        .card-info-label {
            font-size: 10px; font-weight: 700;
            color: var(--gray-400);
            text-transform: uppercase; letter-spacing: 0.5px;
            white-space: nowrap;
            min-width: 52px;
        }
        .card-info-value {
            font-size: 13px; font-weight: 500;
            color: var(--gray-900);
            word-break: break-word;
            line-height: 1.4;
        }
        .card-info-value.mono {
            font-family: monospace; font-size: 11px;
            color: var(--gray-600);
        }
        .card-info-value.location {
            color: var(--red-dark);
            font-weight: 600;
        }
        .card-info-value.loading {
            color: var(--gray-400);
            font-style: italic;
        }

        /* Map link */
        .map-link {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 11px; color: var(--red);
            text-decoration: none; font-weight: 600;
            margin-top: 2px;
            transition: color .2s;
        }
        .map-link:hover { color: var(--red-dark); text-decoration: underline; }

        /* Watch button */
        .btn-watch {
            margin-top: 14px; width: 100%;
            padding: 11px 0;
            background: var(--red);
            color: var(--white);
            border: none; border-radius: 9px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 15px; font-weight: 700; letter-spacing: 0.5px;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(215,6,8,0.28);
            transition: background .2s, transform .2s, box-shadow .2s;
        }
        .btn-watch:hover {
            background: var(--red-dark);
            transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(215,6,8,0.35);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .navbar { padding: 0 16px; height: 58px; }
            .navbar-title-sub { display: none; }
            .page-header { padding: 20px 16px 0; flex-direction: column; align-items: flex-start; gap: 12px; }
            .main-content { padding: 16px 16px 40px; }
            .stream-grid { grid-template-columns: 1fr; }
            .poll-status { display: none; }
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
            <span class="poll-status" id="pollStatus">Memuat...</span>
            <a href="{{ route('admin.credentials') }}" class="btn-nav-link">
                🔑 Kredensial
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    {{-- ── PAGE HEADER ── --}}
    <div class="page-header">
        <div class="page-header-left">
            <h1>📡 Live Stream Aktif</h1>
            <p>Monitor semua siaran langsung dari petugas lapangan</p>
        </div>
        <div class="stream-count-badge">
            <div class="stream-count-dot" id="streamCountDot"></div>
            <span class="stream-count-text" id="streamCount">0 stream aktif</span>
        </div>
    </div>

    {{-- ── MAIN ── --}}
    <div class="main-content">

        {{-- Grid --}}
        <div id="streamGrid" class="stream-grid" style="display:none;"></div>

        {{-- Empty State --}}
        <div id="emptyState" class="empty-state">
            <div class="empty-state-icon">📭</div>
            <h2>Belum ada petugas yang sedang live</h2>
            <p>Halaman akan otomatis update setiap 5 detik</p>
        </div>

    </div>

    <script>
        let activeStreams = new Set();

        function formatTime(ms) {
            if (!ms) return '-';
            return new Date(ms).toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        }

        // Fetch lokasi stream dari database via endpoint Laravel
        async function fetchStreamLocation(streamId) {
            try {
                const res  = await fetch(`/admin/stream/location/${streamId}`);
                const data = await res.json();
                return data; // { address, latitude, longitude } atau null
            } catch (e) {
                return null;
            }
        }

        function createStreamCard(stream) {
            const shortId = stream.streamId.length > 24
                ? stream.streamId.substring(0, 24) + '…'
                : stream.streamId;

            return `
                <div id="card-${stream.streamId}" class="stream-card">

                    <div class="card-video-wrapper">
                        <video id="video-${stream.streamId}"
                               autoplay muted controls playsinline
                               class="card-video"></video>
                        <div class="card-live-badge">
                            <div class="card-live-dot"></div> LIVE
                        </div>
                        <div class="card-stream-id-overlay">${shortId}</div>
                    </div>

                    <div class="card-body">

                        <div class="card-info-row">
                            <span class="card-info-icon">🕐</span>
                            <span class="card-info-label">Mulai</span>
                            <span class="card-info-value">${formatTime(stream.startTime)}</span>
                        </div>

                        <div class="card-info-row">
                            <span class="card-info-icon">📍</span>
                            <span class="card-info-label">Lokasi</span>
                            <div style="flex:1;">
                                <div id="loc-text-${stream.streamId}"
                                     class="card-info-value location loading">
                                    Memuat lokasi...
                                </div>
                                <a id="loc-link-${stream.streamId}"
                                   href="#" target="_blank"
                                   class="map-link" style="display:none;">
                                    🗺️ Buka di Maps
                                </a>
                            </div>
                        </div>

                        <button class="btn-watch"
                                onclick="watchStream('${stream.streamId}')">
                            ▶ Tonton Live
                        </button>
                    </div>
                </div>
            `;
        }

        async function loadLocationForCard(streamId) {
            const locText = document.getElementById(`loc-text-${streamId}`);
            const locLink = document.getElementById(`loc-link-${streamId}`);
            if (!locText) return;

            const data = await fetchStreamLocation(streamId);

            if (data && data.address) {
                locText.classList.remove('loading');
                locText.innerText = data.address;
                if (data.latitude && data.longitude && locLink) {
                    locLink.href = `https://maps.google.com/?q=${data.latitude},${data.longitude}`;
                    locLink.style.display = 'inline-flex';
                }
            } else {
                locText.classList.remove('loading');
                locText.classList.remove('location');
                locText.style.color = 'var(--gray-400)';
                locText.innerText = 'Lokasi tidak tersedia';
            }
        }

        async function pollStreams() {
            try {
                const res  = await fetch('/admin/streams');
                const data = await res.json();

                if (data.error) {
                    document.getElementById('pollStatus').innerText = '❌ Gagal terhubung ke AMS';
                    return;
                }

                const grid       = document.getElementById('streamGrid');
                const emptyState = document.getElementById('emptyState');
                const countEl    = document.getElementById('streamCount');
                const countDot   = document.getElementById('streamCountDot');
                const newIds     = new Set(data.map(s => s.streamId));

                // Hapus card stream yang sudah tidak live
                activeStreams.forEach(id => {
                    if (!newIds.has(id)) {
                        const card = document.getElementById(`card-${id}`);
                        if (card) {
                            card.style.animation = 'none';
                            card.style.opacity   = '0';
                            card.style.transform = 'scale(0.95)';
                            card.style.transition = 'opacity .3s, transform .3s';
                            setTimeout(() => card.remove(), 300);
                        }
                        window.stopWatch(id);
                        activeStreams.delete(id);
                    }
                });

                // Tambah card stream baru
                data.forEach(stream => {
                    if (!activeStreams.has(stream.streamId)) {
                        grid.insertAdjacentHTML('beforeend', createStreamCard(stream));
                        activeStreams.add(stream.streamId);
                        // Auto-play
                        setTimeout(() => window.watchStream(stream.streamId), 500);
                        // Load lokasi async
                        loadLocationForCard(stream.streamId);
                    }
                });

                // Update UI
                const count = activeStreams.size;
                countEl.innerText = `${count} stream aktif`;
                countDot.classList.toggle('active', count > 0);
                emptyState.style.display = count === 0 ? 'block' : 'none';
                grid.style.display       = count === 0 ? 'none'  : 'grid';

                document.getElementById('pollStatus').innerText =
                    `Update: ${new Date().toLocaleTimeString('id-ID')}`;

            } catch (e) {
                document.getElementById('pollStatus').innerText = '❌ Error polling';
                console.error(e);
            }
        }

        pollStreams();
        setInterval(pollStreams, 5000);
    </script>

</body>
</html>