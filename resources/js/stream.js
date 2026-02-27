import { WebRTCAdaptor } from '@antmedia/webrtc_adaptor';

const AMS_WS = "ws://localhost:5080/LiveApp/websocket";
let adaptor       = null;
let streamId      = null;
let statsInterval = null;
let bitrateHistory = [];

// ── Helpers ───────────────────────────────────────────────────────────────────

function generateStreamId() {
    const ts     = Date.now();
    const random = Math.random().toString(36).substring(2, 8);
    return `stream-${ts}-${random}`;
}

function log(msg, type = 'info') {
    const el     = document.getElementById('status');
    const colors = { info: '#aaa', success: '#4ade80', error: '#f87171', warning: '#fbbf24' };
    el.style.color       = colors[type] || '#aaa';
    el.style.borderColor = colors[type] || '#444';
    el.innerText         = msg;
    console.log(msg);
}

function setLiveState(isLive) {
    const btnStart = document.getElementById('btnStart');
    const btnStop  = document.getElementById('btnStop');
    const badge    = document.getElementById('liveBadge');
    const overlay  = document.getElementById('streamIdOverlay');
    const statsBox = document.getElementById('statsBox');

    if (isLive) {
        btnStart.disabled         = true;
        btnStart.style.background = '#166534';
        btnStart.style.cursor     = 'not-allowed';
        btnStop.disabled          = false;
        btnStop.style.background  = '#dc2626';
        btnStop.style.color       = '#fff';
        btnStop.style.cursor      = 'pointer';
        badge.style.display       = 'block';
        overlay.style.display     = 'block';
        overlay.innerText         = `ID: ${streamId}`;
        statsBox.style.display    = 'grid';
        startStats();
    } else {
        btnStart.disabled         = false;
        btnStart.style.background = '#16a34a';
        btnStart.style.cursor     = 'pointer';
        btnStop.disabled          = true;
        btnStop.style.background  = '#374151';
        btnStop.style.color       = '#9ca3af';
        btnStop.style.cursor      = 'not-allowed';
        badge.style.display       = 'none';
        overlay.style.display     = 'none';
        statsBox.style.display    = 'none';
        stopStats();
        resetStats();
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

// ── Settings Panel Toggle ─────────────────────────────────────────────────────

window.toggleSettingsPanel = function() {
    const toggle     = document.getElementById('settingsToggle');
    const videoPanel = document.getElementById('videoPanel');
    const audioPanel = document.getElementById('audioPanel');
    const labelVideo = document.getElementById('labelVideo');
    const labelAudio = document.getElementById('labelAudio');
    const isVideo    = toggle.checked;

    videoPanel.style.display = isVideo ? 'block' : 'none';
    audioPanel.style.display = isVideo ? 'none'  : 'block';
    labelVideo.style.color   = isVideo ? '#fff'   : '#6b7280';
    labelAudio.style.color   = isVideo ? '#6b7280': '#fff';
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
    adaptor.changeBandwidth(bitrate, streamId);
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

    statsInterval = setInterval(async () => {
        if (!adaptor) return;

        try {
            const pc = adaptor.remotePeerConnection?.[streamId]
                    ?? adaptor.peerConnection
                    ?? null;

            if (!pc) return;

            const stats = await pc.getStats();
            let   bitrateCurrent = 0;
            let   packetLost     = 0;
            let   jitter         = 0;
            let   rtt            = 0;
            let   fps            = 0;
            let   resWidth       = 0;
            let   resHeight      = 0;
            let   audioLevel     = 0;
            let   prevBytesSent  = 0;

            stats.forEach(report => {

                // Video outbound → bitrate, packet lost, resolusi, fps
                if (report.type === 'outbound-rtp' && report.kind === 'video') {
                    const bytesSent = report.bytesSent || 0;
                    bitrateCurrent  = Math.round(((bytesSent - prevBytesSent) * 8) / 1000);
                    prevBytesSent   = bytesSent;
                    packetLost      = report.packetsLost || 0;
                    fps             = report.framesPerSecond
                                    ? Math.round(report.framesPerSecond)
                                    : (report.framesSent
                                        ? Math.round(report.framesSent / ((Date.now() - report.timestamp) / 1000))
                                        : 0);
                }

                // Remote inbound → jitter, rtt
                if (report.type === 'remote-inbound-rtp' && report.kind === 'video') {
                    jitter = report.jitter ? report.jitter.toFixed(4) : 0;
                    rtt    = report.roundTripTime ? report.roundTripTime.toFixed(4) : 0;
                }

                // Media source → resolusi sumber & fps akurat
                if (report.type === 'media-source' && report.kind === 'video') {
                    resWidth  = report.width  || 0;
                    resHeight = report.height || 0;
                    if (report.framesPerSecond) fps = Math.round(report.framesPerSecond);
                }

                // Audio level
                if (report.type === 'media-source' && report.kind === 'audio') {
                    audioLevel = report.audioLevel
                        ? Math.round(report.audioLevel * 100)
                        : 0;
                }
            });

            // Hitung bitrate rata-rata
            if (bitrateCurrent > 0) bitrateHistory.push(bitrateCurrent);
            if (bitrateHistory.length > 30) bitrateHistory.shift();
            const bitrateAvg = bitrateHistory.length
                ? Math.round(bitrateHistory.reduce((a, b) => a + b, 0) / bitrateHistory.length)
                : 0;

            // Resolusi output dari video element
            const videoEl  = document.getElementById('localVideo');
            const outWidth  = videoEl?.videoWidth  || 0;
            const outHeight = videoEl?.videoHeight || 0;

            // Update UI
            setStat('statBitrateAvg',     bitrateAvg     > 0 ? `${bitrateAvg} Kbps`       : '—');
            setStat('statBitrateCurrent', bitrateCurrent > 0 ? `${bitrateCurrent} Kbps`    : '—');
            setStat('statPacketLost',     `${packetLost}`);
            setStat('statJitter',         jitter > 0          ? `${jitter} s`               : '—');
            setStat('statRtt',            rtt    > 0          ? `${rtt} s`                  : '—');
            setStat('statResSource',      resWidth  > 0       ? `${resWidth}×${resHeight}`  : '—');
            setStat('statResOutput',      outWidth  > 0       ? `${outWidth}×${outHeight}`  : '—');
            setStat('statFps',            fps > 0             ? `${fps} fps`                : '—');
            setStat('statAudioLevel',     `${audioLevel}%`);

            // Audio level bar
            const bar = document.getElementById('audioLevelBar');
            if (bar) {
                bar.style.width = `${Math.min(audioLevel, 100)}%`;
                bar.style.background = audioLevel > 80 ? '#f87171'
                                     : audioLevel > 50 ? '#fbbf24'
                                     : '#4ade80';
            }

        } catch(e) {
            console.warn('Stats error:', e);
        }
    }, 1000);
}

function stopStats() {
    if (statsInterval) {
        clearInterval(statsInterval);
        statsInterval = null;
    }
}

// ── Start / Stop ──────────────────────────────────────────────────────────────

window.startLive = function() {
    streamId = generateStreamId();
    log('Menginisialisasi kamera...', 'info');

    adaptor = new WebRTCAdaptor({
        websocket_url    : AMS_WS,
        mediaConstraints : getMediaConstraints(),
        peerconnection_config: {
            iceServers: [{ urls: 'stun:stun.l.google.com:19302' }]
        },
        localVideoId: "localVideo",
        debug: true,

        callback: function(info, obj) {
            if (info === "initialized") {
                log('Terkoneksi ke server, memulai siaran...', 'info');
                adaptor.publish(streamId);
            }
            else if (info === "available_devices") {
                window.populateDevices(obj);
            }
            else if (info === "publish_started") {
                log('🔴 Siaran langsung aktif!', 'success');
                setLiveState(true);
                applyBitrate();
            }
            else if (info === "publish_finished") {
                log('Siaran dihentikan.', 'warning');
                setLiveState(false);
            }
            else {
                console.log("callback:", info, obj);
            }
        },

        callbackError: function(error, message) {
            log('❌ Error: ' + error, 'error');
            console.error(error, message);
            setLiveState(false);
        }
    });
}

window.stopLive = function() {
    if (adaptor) {
        adaptor.stop(streamId);
        log('Menghentikan siaran...', 'warning');
    }
}

// ── Init ──────────────────────────────────────────────────────────────────────

window.addEventListener('DOMContentLoaded', function() {
    navigator.mediaDevices.enumerateDevices().then(devices => {
        window.populateDevices(devices);
    });
    window.toggleSettingsPanel();
});