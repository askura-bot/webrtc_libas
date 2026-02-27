<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Command Center</title>
    @vite(['resources/js/watch.js'])
</head>
<body style="background:#0f0f0f; color:#fff; font-family:sans-serif; margin:0; padding:0;">

    {{-- Navbar --}}
    <div style="background:#1a1a2e; padding:14px 24px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #333;">
        <span style="font-size:18px; font-weight:bold;">🖥️ Command Center</span>
        <div style="display:flex; align-items:center; gap:16px;">
            <span id="pollStatus" style="font-size:12px; color:#6b7280;">Memuat...</span>
            <a href="{{ route('admin.credentials') }}"
                style="color:#93c5fd; text-decoration:none; font-size:14px;">
        🔑      Kelola Kredensial
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="padding:8px 16px; background:#dc2626; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
                    Logout
                </button>
            </form>
        </div>
    </div>

    {{-- Main --}}
    <div style="padding:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0;">📡 Live Stream Aktif</h2>
            <span id="streamCount" style="background:#1e1e1e; padding:6px 14px; border-radius:20px; font-size:14px; color:#aaa;">
                0 stream aktif
            </span>
        </div>

        {{-- Grid stream cards --}}
        <div id="streamGrid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(400px, 1fr)); gap:20px;">
            {{-- Diisi oleh JavaScript --}}
        </div>

        {{-- Empty state --}}
        <div id="emptyState" style="text-align:center; padding:80px 20px; color:#4b5563;">
            <div style="font-size:48px; margin-bottom:16px;">📭</div>
            <p style="font-size:18px; margin:0;">Belum ada petugas yang sedang live</p>
            <p style="font-size:14px; margin-top:8px;">Halaman akan otomatis update setiap 5 detik</p>
        </div>
    </div>

    <script>
        let activeStreams = new Set();

        function formatTime(ms) {
            if (!ms) return '-';
            const date = new Date(ms);
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }

        function createStreamCard(stream) {
            return `
                <div id="card-${stream.streamId}"
                     style="background:#1a1a1a; border-radius:10px; overflow:hidden; border:1px solid #333;">

                    {{-- Video --}}
                    <div style="position:relative; background:#000;">
                        <video id="video-${stream.streamId}" autoplay playsinline
                               style="width:100%; height:220px; object-fit:cover; display:block;">
                        </video>
                        <div style="position:absolute; top:10px; left:10px; background:#dc2626;
                             color:#fff; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:bold;">
                            ● LIVE
                        </div>
                    </div>

                    {{-- Info --}}
                    <div style="padding:14px;">
                        <div style="font-size:13px; color:#9ca3af; font-family:monospace; margin-bottom:6px; word-break:break-all;">
                            ID: ${stream.streamId}
                        </div>
                        <div style="font-size:12px; color:#6b7280;">
                            Mulai: ${formatTime(stream.startTime)}
                        </div>
                        <button onclick="watchStream('${stream.streamId}')"
                                style="margin-top:12px; width:100%; padding:10px; background:#1d4ed8;
                                color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
                            ▶ Tonton
                        </button>
                    </div>
                </div>
            `;
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
                const newIds     = new Set(data.map(s => s.streamId));

                // Hapus card stream yang sudah tidak live
                activeStreams.forEach(id => {
                    if (!newIds.has(id)) {
                        const card = document.getElementById(`card-${id}`);
                        if (card) card.remove();
                        window.stopWatch(id);
                        activeStreams.delete(id);
                    }
                });

                // Tambah card stream baru
                data.forEach(stream => {
                    if (!activeStreams.has(stream.streamId)) {
                        grid.insertAdjacentHTML('beforeend', createStreamCard(stream));
                        activeStreams.add(stream.streamId);
                        // Auto-play stream begitu card muncul
                        setTimeout(() => window.watchStream(stream.streamId), 500);
                    }
                });

                // Update UI
                const count = activeStreams.size;
                countEl.innerText    = `${count} stream aktif`;
                emptyState.style.display = count === 0 ? 'block' : 'none';
                grid.style.display       = count === 0 ? 'none' : 'grid';

                document.getElementById('pollStatus').innerText = `Update: ${new Date().toLocaleTimeString('id-ID')}`;

            } catch (e) {
                document.getElementById('pollStatus').innerText = '❌ Error polling';
                console.error(e);
            }
        }

        // Poll pertama langsung, lalu tiap 5 detik
        pollStreams();
        setInterval(pollStreams, 5000);
    </script>

</body>
</html>