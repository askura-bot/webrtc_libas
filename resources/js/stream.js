// stream.js
import { WebRTCAdaptor } from '@antmedia/webrtc_adaptor';

const AMS_WS = "ws://localhost:5080/LiveApp/websocket";
let adaptor        = null;
let streamId       = null;
let statsInterval  = null;
let bitrateHistory = [];
let currentLocation = null; // { lat, lng, address }

// ── Helpers ───────────────────────────────────────────────────────────────────

function generateStreamId() {
    const ts     = Date.now();
    const random = Math.random().toString(36).substring(2, 8);
    return `stream-${ts}-${random}`;
}

function log(msg, type = 'info') {
    const el = document.getElementById('status');
    el.className = 'status-bar ' + (
        type === 'success' ? 'success' :
        type === 'error'   ? 'error'   :
        type === 'warning' ? 'warning' : ''
    );
    el.innerText = msg;
    console.log(msg);
}

function setLiveState(isLive) {
    const btnStart  = document.getElementById('btnStart');
    const btnStop   = document.getElementById('btnStop');
    const badge     = document.getElementById('liveBadge');
    const overlay   = document.getElementById('streamIdOverlay');
    const statsBox  = document.getElementById('statsBox');
    const navBadge  = document.getElementById('navLiveBadge');

    if (isLive) {
        btnStart.disabled = true;
        btnStop.disabled  = false;
        btnStop.classList.add('active');
        badge.classList.add('active');
        navBadge?.classList.add('active');
        overlay.style.display = 'block';
        overlay.innerText     = `ID: ${streamId}`;
        statsBox.style.display = 'grid';
        startStats();
    } else {
        btnStart.disabled = false;
        btnStop.disabled  = true;
        btnStop.classList.remove('active');
        badge.classList.remove('active');
        navBadge?.classList.remove('active');
        overlay.style.display  = 'none';
        statsBox.style.display = 'none';
        stopStats();
        resetStats();
        // Reset location bar
        updateLocationBar(null);
    }
}

// ── Device Population ─────────────────────────────────────────────────────────

window.populateDevices = function(devices) {
    const cameras = document.getElementById('cameraSelect');
    const mics    = document.getElementById('micSelect');
    cameras.innerHTML = '';
    mics.innerHTML    = '';
    devices.forEach(device => {
        const opt = document.createElement('option');
        opt.value = device.deviceId;
        opt.text  = device.label || `Device ${device.deviceId.substring(0, 8)}`;
        if (device.kind === 'videoinput') cameras.appendChild(opt);
        if (device.kind === 'audioinput') mics.appendChild(opt.cloneNode(true));
    });
}

window.switchTab = function(tab) {
    const videoPanel = document.getElementById('videoPanel');
    const audioPanel = document.getElementById('audioPanel');
    const tabVideo   = document.getElementById('tabVideo');
    const tabAudio   = document.getElementById('tabAudio');
    if (tab === 'video') {
        videoPanel.style.display = 'block';
        audioPanel.style.display = 'none';
        tabVideo.classList.add('active');
        tabAudio.classList.remove('active');
    } else {
        videoPanel.style.display = 'none';
        audioPanel.style.display = 'block';
        tabVideo.classList.remove('active');
        tabAudio.classList.add('active');
    }
}

// ── Media Constraints ─────────────────────────────────────────────────────────

function getMediaConstraints() {
    const cameraId  = document.getElementById('cameraSelect').value;
    const micId     = document.getElementById('micSelect').value;
    const noiseSupp = document.getElementById('noiseSuppression').checked;
    const echoCan   = document.getElementById('echoCancellation').checked;
    return {
        video: { deviceId: cameraId ? { exact: cameraId } : undefined },
        audio: {
            deviceId        : micId ? { exact: micId } : undefined,
            noiseSuppression: noiseSupp,
            echoCancellation: echoCan,
        }
    };
}

// ── Bitrate ───────────────────────────────────────────────────────────────────

function applyBitrate() {
    if (!adaptor) return;
    const bitrate = parseInt(document.getElementById('bitrateInput').value) || 1200;

    let attempts = 0;
    const maxAttempts = 15;

    const tryApply = () => {
        attempts++;
        try {
            const pc = adaptor.remotePeerConnection?.[streamId]
                    ?? adaptor.peerConnection
                    ?? null;
            if (!pc) throw new Error('PeerConnection belum siap');

            const senders = pc.getSenders();
            const videoSender = senders.find(s => s.track && s.track.kind === 'video');
            if (!videoSender) throw new Error('Video sender belum ada');

            const params = videoSender.getParameters();
            if (!params.encodings || params.encodings.length === 0) {
                params.encodings = [{}];
            }
            params.encodings.forEach(enc => {
                enc.maxBitrate = bitrate * 1000;
            });
            videoSender.setParameters(params)
                .then(() => console.log(`✅ Bitrate berhasil diset: ${bitrate} kbps`))
                .catch(e => console.warn('setParameters gagal:', e.message));

        } catch (e) {
            if (attempts < maxAttempts) {
                console.warn(`⏳ Mencoba set bitrate... (${attempts}/${maxAttempts})`);
                setTimeout(tryApply, 1000);
            } else {
                console.warn('⚠️ Bitrate tidak bisa diset, streaming tetap berjalan normal.');
            }
        }
    };
    setTimeout(tryApply, 2000);
}

// ── Geolocation ───────────────────────────────────────────────────────────────

function updateLocationBar(locationData) {
    const bar = document.getElementById('locationBar');
    const txt = document.getElementById('locationText');
    if (!bar || !txt) return;

    if (!locationData) {
        bar.style.display = 'none';
        return;
    }

    bar.style.display = 'flex';
    txt.innerText = locationData.address || `${locationData.lat}, ${locationData.lng}`;
}

async function reverseGeocode(lat, lng) {
    try {
        // Proxy via Laravel — hindari CORS & blokir Nominatim dari localhost
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const res   = await fetch(`/officer/geocode?lat=${lat}&lng=${lng}`, {
            headers: {
                'Accept'      : 'application/json',
                'X-CSRF-TOKEN': token || '',
            }
        });
        const data = await res.json();
        return data.address || `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    } catch (e) {
        console.warn('Reverse geocode gagal:', e);
        return `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    }
}

async function getLocation() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error('Geolocation tidak didukung browser ini'));
            return;
        }
        navigator.geolocation.getCurrentPosition(
            pos => resolve({ lat: pos.coords.latitude, lng: pos.coords.longitude }),
            err => reject(new Error(
                err.code === 1 ? 'Izin lokasi ditolak' :
                err.code === 2 ? 'Lokasi tidak tersedia' :
                'Timeout mengambil lokasi'
            )),
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
}

async function fetchAndSaveLocation() {
    log('📍 Mengambil lokasi...', 'info');
    try {
        const { lat, lng } = await getLocation();
        const address = await reverseGeocode(lat, lng);

        currentLocation = { lat, lng, address };
        updateLocationBar(currentLocation);

        // Kirim ke Laravel
        await saveLocationToServer(streamId, lat, lng, address);

        console.log(`✅ Lokasi: ${address} (${lat}, ${lng})`);
    } catch (e) {
        console.warn('Lokasi gagal:', e.message);
        updateLocationBar({ lat: null, lng: null, address: '⚠️ ' + e.message });
    }
}

async function saveLocationToServer(streamId, lat, lng, address) {
    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const res = await fetch('/officer/stream/location', {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'Accept'       : 'application/json',
                'X-CSRF-TOKEN' : token || '',
            },
            body: JSON.stringify({ stream_id: streamId, latitude: lat, longitude: lng, address }),
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        console.log('✅ Lokasi tersimpan ke server');
    } catch (e) {
        console.warn('Gagal simpan lokasi ke server:', e.message);
    }
}

// ── Statistics ────────────────────────────────────────────────────────────────

function setStat(id, value) {
    const el = document.getElementById(id);
    if (el) el.innerText = value;
}

function resetStats() {
    bitrateHistory = [];
    ['statBitrateAvg', 'statBitrateCurrent', 'statPacketLost',
     'statJitter', 'statRtt', 'statResSource',
     'statResOutput', 'statFps', 'statAudioLevel'
    ].forEach(id => setStat(id, '—'));
}

function startStats() {
    if (statsInterval) clearInterval(statsInterval);
    bitrateHistory = [];
    let prevBytesSent = 0;

    statsInterval = setInterval(async () => {
        if (!adaptor) return;
        try {
            const pc = adaptor.remotePeerConnection?.[streamId]
                    ?? adaptor.peerConnection
                    ?? null;
            if (!pc) return;

            const stats = await pc.getStats();
            let bitrateCurrent = 0, packetLost = 0, jitter = 0,
                rtt = 0, fps = 0, resWidth = 0, resHeight = 0, audioLevel = 0;

            stats.forEach(report => {
                if (report.type === 'outbound-rtp' && report.kind === 'video') {
                    const bytesSent = report.bytesSent || 0;
                    bitrateCurrent  = Math.round(((bytesSent - prevBytesSent) * 8) / 1000);
                    prevBytesSent   = bytesSent;
                    packetLost      = report.packetsLost || 0;
                    fps = report.framesPerSecond ? Math.round(report.framesPerSecond) : 0;
                }
                if (report.type === 'remote-inbound-rtp' && report.kind === 'video') {
                    jitter = report.jitter ? report.jitter.toFixed(4) : 0;
                    rtt    = report.roundTripTime ? report.roundTripTime.toFixed(4) : 0;
                }
                if (report.type === 'media-source' && report.kind === 'video') {
                    resWidth  = report.width  || 0;
                    resHeight = report.height || 0;
                    if (report.framesPerSecond) fps = Math.round(report.framesPerSecond);
                }
                if (report.type === 'media-source' && report.kind === 'audio') {
                    audioLevel = report.audioLevel ? Math.round(report.audioLevel * 100) : 0;
                }
            });

            if (bitrateCurrent > 0) bitrateHistory.push(bitrateCurrent);
            if (bitrateHistory.length > 30) bitrateHistory.shift();
            const bitrateAvg = bitrateHistory.length
                ? Math.round(bitrateHistory.reduce((a, b) => a + b, 0) / bitrateHistory.length) : 0;

            const videoEl   = document.getElementById('localVideo');
            const outWidth  = videoEl?.videoWidth  || 0;
            const outHeight = videoEl?.videoHeight || 0;

            setStat('statBitrateAvg',     bitrateAvg     > 0 ? `${bitrateAvg} Kbps`      : '—');
            setStat('statBitrateCurrent', bitrateCurrent > 0 ? `${bitrateCurrent} Kbps`   : '—');
            setStat('statPacketLost',     `${packetLost}`);
            setStat('statJitter',         jitter > 0          ? `${jitter} s`              : '—');
            setStat('statRtt',            rtt    > 0          ? `${rtt} s`                 : '—');
            setStat('statResSource',      resWidth  > 0       ? `${resWidth}×${resHeight}` : '—');
            setStat('statResOutput',      outWidth  > 0       ? `${outWidth}×${outHeight}` : '—');
            setStat('statFps',            fps > 0             ? `${fps} fps`               : '—');
            setStat('statAudioLevel',     `${audioLevel}%`);

            const bar = document.getElementById('audioLevelBar');
            if (bar) {
                bar.style.width      = `${Math.min(audioLevel, 100)}%`;
                bar.style.background = audioLevel > 80 ? '#f87171'
                                     : audioLevel > 50 ? '#fbbf24' : '#4ade80';
            }
        } catch(e) {
            console.warn('Stats error:', e);
    }
    }, 1000);
}

function stopStats() {
    if (statsInterval) { clearInterval(statsInterval); statsInterval = null; }
}

// ── Start / Stop ──────────────────────────────────────────────────────────────

let isStarting = false; // guard flag

window.startLive = function() {
    // Cegah double-click / double-call
    if (isStarting || adaptor) {
        console.warn('⚠️ startLive diabaikan: sudah berjalan atau sedang inisialisasi');
        return;
    }
    isStarting = true;

    // Nonaktifkan tombol langsung
    const btnStart = document.getElementById('btnStart');
    if (btnStart) {
        btnStart.disabled = true;
        btnStart.style.opacity = '0.6';
    }

    streamId = generateStreamId();
    log('Menginisialisasi kamera...', 'info');

    adaptor = new WebRTCAdaptor({
        websocket_url        : AMS_WS,
        mediaConstraints     : getMediaConstraints(),
        peerconnection_config: { iceServers: [{ urls: 'stun:stun.l.google.com:19302' }] },
        localVideoId         : "localVideo",
        debug                : true,

        callback: function(info, obj) {
            if (info === "initialized") {
                log('Terkoneksi ke server, memulai siaran...', 'info');
                adaptor.publish(streamId);
            }
            else if (info === "available_devices") {
                window.populateDevices(obj);
            }
            else if (info === "publish_started") {
                isStarting = false; // selesai inisialisasi
                log('🔴 Siaran langsung aktif!', 'success');
                setLiveState(true);
                applyBitrate();
                fetchAndSaveLocation();
            }
            else if (info === "publish_finished") {
                log('Siaran dihentikan.', 'warning');
                cleanupAdaptor();
                setLiveState(false);
            }
            else {
                console.log("callback:", info, obj);
            }
        },

        callbackError: function(error, message) {
            console.error(error, message);

            if (error === 'already_publishing') {
                // Stream ID sudah dipakai — generate ID baru dan coba lagi
                log('⚠️ Stream ID konflik, mencoba ulang...', 'warning');
                cleanupAdaptor();
                isStarting = false;
                setTimeout(() => window.startLive(), 1500);
                return;
            }

            if (error === 'publishTimeoutError') {
                // Timeout biasa setelah already_publishing — abaikan jika sudah ada stream aktif
                if (document.getElementById('liveBadge')?.classList.contains('active')) {
                    console.warn('publishTimeoutError diabaikan — stream sudah aktif');
                    return;
                }
            }

            log('❌ Error: ' + error, 'error');
            cleanupAdaptor();
            isStarting = false;
            setLiveState(false);
        }
    });
}

window.stopLive = function() {
    if (!adaptor) return;
    adaptor.stop(streamId);
    log('Menghentikan siaran...', 'warning');
    // cleanup akan dipanggil saat publish_finished callback
}

function cleanupAdaptor() {
    if (adaptor) {
        try { adaptor.closeWebSocket(); } catch(e) {}
        adaptor = null;
    }
    isStarting = false;
}

// ── Init ──────────────────────────────────────────────────────────────────────

window.addEventListener('DOMContentLoaded', function() {
    navigator.mediaDevices.enumerateDevices().then(devices => {
        window.populateDevices(devices);
    });
    window.switchTab('video');
});