<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Stream — Petugas</title>
    @vite(['resources/js/stream.js'])
    <style>
        select, input[type="number"], input[type="range"] {
            width: 100%;
            padding: 8px 10px;
            background: #111;
            border: 1px solid #444;
            border-radius: 6px;
            color: #fff;
            font-size: 14px;
            box-sizing: border-box;
        }
        label { display: block; margin-bottom: 6px; font-size: 13px; color: #d1d5db; }
        .field { margin-bottom: 16px; }
        .toggle-switch {
            position: relative; display: inline-block; width: 52px; height: 28px;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute; cursor: pointer; inset: 0;
            background: #6b7280; border-radius: 28px; transition: .3s;
        }
        .slider:before {
            content: ""; position: absolute;
            width: 22px; height: 22px; left: 3px; bottom: 3px;
            background: white; border-radius: 50%; transition: .3s;
        }
        input:checked + .slider { background: #1d4ed8; }
        input:checked + .slider:before { transform: translateX(24px); }
        .checkbox-row {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 0; border-bottom: 1px solid #2a2a2a;
        }
        .checkbox-row:last-child { border-bottom: none; }
        .checkbox-row input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; accent-color: #1d4ed8; }
        .checkbox-row span { font-size: 14px; color: #d1d5db; }
    </style>
</head>
<body style="background:#0f0f0f; color:#fff; font-family:sans-serif; margin:0; padding:0;">

    {{-- Navbar --}}
    <div style="background:#1a1a2e; padding:14px 24px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #333;">
        <span style="font-size:18px; font-weight:bold;">🚔 Polisi Live System</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="padding:8px 16px; background:#dc2626; color:#fff; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
                Logout
            </button>
        </form>
    </div>

    {{-- Main --}}
    <div style="max-width:900px; margin:40px auto; padding:0 20px; display:grid; grid-template-columns:1fr 300px; gap:24px; align-items:start;">

        {{-- Kolom Kiri: Video + Controls --}}
        <div>
            <h2 style="margin-bottom:6px;">📡 Live Streaming TKP</h2>
            <p style="color:#aaa; margin-bottom:16px; font-size:14px;">
                Atur settings terlebih dahulu, lalu tekan <strong>Mulai Live</strong>.
            </p>

            {{-- Video --}}
            <div style="position:relative; background:#000; border-radius:10px; overflow:hidden; border:2px solid #333;">
                <video id="localVideo" autoplay muted playsinline
                       style="width:100%; display:block; min-height:320px; object-fit:cover;">
                </video>
                <div id="liveBadge" style="display:none; position:absolute; top:14px; left:14px;
                    background:#dc2626; color:#fff; padding:4px 12px; border-radius:20px;
                    font-size:13px; font-weight:bold; letter-spacing:1px;">
                    ● LIVE
                </div>
                <div id="streamIdOverlay" style="display:none; position:absolute; bottom:14px; left:14px;
                    background:rgba(0,0,0,0.6); color:#ddd; padding:4px 10px;
                    border-radius:6px; font-size:11px; font-family:monospace;">
                </div>
            </div>

            {{-- Status --}}
            <div id="status" style="margin-top:14px; padding:12px 16px; background:#1e1e1e;
                border-radius:8px; font-size:14px; color:#aaa; border-left:3px solid #444;">
                Siap memulai siaran...
            </div>

            {{-- Controls --}}
            <div style="margin-top:16px; display:flex; gap:12px;">
                <button id="btnStart" onclick="startLive()"
                    style="flex:1; padding:14px; background:#16a34a; color:#fff; border:none;
                    border-radius:8px; font-size:16px; cursor:pointer; font-weight:bold;">
                    ▶ Mulai Live
                </button>
                <button id="btnStop" onclick="stopLive()" disabled
                    style="flex:1; padding:14px; background:#374151; color:#9ca3af; border:none;
                    border-radius:8px; font-size:16px; cursor:not-allowed; font-weight:bold;">
                    ⏹ Stop
                </button>
            </div>

                    {{-- Stats Box --}}
        <div id="statsBox" style="display:none; margin-top:24px;
            grid-template-columns: repeat(3, 1fr); gap:12px;">

            {{-- Baris 1 --}}
            <div style="background:#1a1a1a; border:1px solid #333; border-radius:8px; padding:14px;">
                <div style="font-size:11px; color:#6b7280; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Bitrate Rata-rata</div>
                <div id="statBitrateAvg" style="font-size:20px; font-weight:bold; color:#4ade80;">—</div>
            </div>

            <div style="background:#1a1a1a; border:1px solid #333; border-radius:8px; padding:14px;">
                <div style="font-size:11px; color:#6b7280; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Bitrate Terkini</div>
                <div id="statBitrateCurrent" style="font-size:20px; font-weight:bold; color:#60a5fa;">—</div>
            </div>

            <div style="background:#1a1a1a; border:1px solid #333; border-radius:8px; padding:14px;">
                <div style="font-size:11px; color:#6b7280; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Paket Hilang</div>
                <div id="statPacketLost" style="font-size:20px; font-weight:bold; color:#f87171;">—</div>
            </div>

            {{-- Baris 2 --}}
            <div style="background:#1a1a1a; border:1px solid #333; border-radius:8px; padding:14px;">
                <div style="font-size:11px; color:#6b7280; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Jitter</div>
                <div id="statJitter" style="font-size:20px; font-weight:bold; color:#fbbf24;">—</div>
            </div>

            <div style="background:#1a1a1a; border:1px solid #333; border-radius:8px; padding:14px;">
                <div style="font-size:11px; color:#6b7280; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">RTT</div>
                <div id="statRtt" style="font-size:20px; font-weight:bold; color:#fbbf24;">—</div>
            </div>

            <div style="background:#1a1a1a; border:1px solid #333; border-radius:8px; padding:14px;">
                <div style="font-size:11px; color:#6b7280; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">FPS</div>
                <div id="statFps" style="font-size:20px; font-weight:bold; color:#a78bfa;">—</div>
            </div>

            {{-- Baris 3 --}}
            <div style="background:#1a1a1a; border:1px solid #333; border-radius:8px; padding:14px;">
                <div style="font-size:11px; color:#6b7280; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Resolusi Sumber</div>
                <div id="statResSource" style="font-size:20px; font-weight:bold; color:#fff;">—</div>
            </div>

            <div style="background:#1a1a1a; border:1px solid #333; border-radius:8px; padding:14px;">
                <div style="font-size:11px; color:#6b7280; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Resolusi Output</div>
                <div id="statResOutput" style="font-size:20px; font-weight:bold; color:#fff;">—</div>
            </div>

            {{-- Audio Level --}}
            <div style="background:#1a1a1a; border:1px solid #333; border-radius:8px; padding:14px;">
                <div style="font-size:11px; color:#6b7280; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Level Audio</div>
                <div id="statAudioLevel" style="font-size:20px; font-weight:bold; color:#4ade80; margin-bottom:8px;">—</div>
                <div style="background:#2a2a2a; border-radius:4px; height:6px; overflow:hidden;">
                    <div id="audioLevelBar" style="height:100%; width:0%; background:#4ade80; transition:width 0.3s;"></div>
                </div>
            </div>

        </div>
        </div>



        {{-- Kolom Kanan: Settings Panel --}}
        <div style="background:#1a1a1a; border:1px solid #333; border-radius:10px; padding:20px;">

            {{-- Toggle Switch Video | Audio --}}
            <div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid #2a2a2a;">
                <span id="labelVideo" style="font-size:14px; font-weight:bold; color:#fff;">🎥 Video</span>
                <label class="toggle-switch">
                    <input type="checkbox" id="settingsToggle" onchange="toggleSettingsPanel()">
                    <span class="slider"></span>
                </label>
                <span id="labelAudio" style="font-size:14px; font-weight:bold; color:#6b7280;">🎙️ Audio</span>
            </div>

            {{-- VIDEO PANEL --}}
            <div id="videoPanel">

                <div class="field">
                    <label>📷 Pilih Kamera</label>
                    <select id="cameraSelect"></select>
                </div>

                <div class="field">
                    <label>
                        📶 Bitrate (kbps)
                        <span style="color:#6b7280; font-weight:normal;">— default: 1200</span>
                    </label>
                    <input type="number" id="bitrateInput" value="1200" min="300" max="4000" step="100">
                    <div style="display:flex; justify-content:space-between; margin-top:4px; font-size:11px; color:#6b7280;">
                        <span>300</span><span>Rendah · Sedang · Tinggi</span><span>4000</span>
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
                        <span id="micVolumeLabel" style="color:#4ade80;">100%</span>
                    </label>
                    <input type="range" id="micVolume" min="0" max="2" step="0.05" value="1"
                           oninput="document.getElementById('micVolumeLabel').innerText = Math.round(this.value * 100) + '%'">
                    <div style="display:flex; justify-content:space-between; margin-top:4px; font-size:11px; color:#6b7280;">
                        <span>0%</span><span>100%</span><span>200%</span>
                    </div>
                </div>

                <div class="field">
                    <div class="checkbox-row">
                        <input type="checkbox" id="noiseSuppression" checked>
                        <span>🔇 Noise Suppression</span>
                    </div>
                    <div class="checkbox-row">
                        <input type="checkbox" id="echoCancellation" checked>
                        <span>🔁 Echo Cancellation</span>
                    </div>
                </div>

            </div>

        </div>
    </div>

</body>
</html>