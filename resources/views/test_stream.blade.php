<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Test WebRTC</title>
    @vite(['resources/js/stream.js'])
</head>
<body style="background:#111; color:#fff; padding:20px; font-family:sans-serif;">

    <h2>🎥 Test Koneksi WebRTC → AMS</h2>

    <video id="localVideo" autoplay muted playsinline
           style="width:480px; height:270px; background:#000; border:2px solid #555;">
    </video>

    <br><br>
    <button onclick="startPublish()">▶ Start Publish</button>
    <button onclick="stopPublish()">⏹ Stop</button>

    <div id="status" style="margin-top:15px; padding:10px; background:#222; border-radius:5px;">
        Status: Menunggu...
    </div>

   <script>
    const AMS_WS  = "ws://localhost:5080/LiveApp/websocket";
    const STREAM_ID = "polisi-test-001";
    let adaptor = null;

    // Pindahkan log ke window supaya bisa diakses dari semua scope
    window.log = function(msg) {
        document.getElementById('status').innerText = 'Status: ' + msg;
        console.log(msg);
    }

    function startPublish() {
        adaptor = new window.WebRTCAdaptor({
            websocket_url: AMS_WS,
            mediaConstraints: { video: true, audio: true },
            peerconnection_config: {
                iceServers: [{ urls: 'stun:stun.l.google.com:19302' }]
            },
            localVideoId: "localVideo",
            debug: true,

            callback: function(info, obj) {
                if (info === "initialized") {
                    window.log("Terkoneksi ke AMS ✅ — memulai publish...");
                    adaptor.publish(STREAM_ID);
                }
                else if (info === "publish_started") {
                    window.log("🔴 Live! Stream ID: " + STREAM_ID);
                }
                else if (info === "publish_finished") {
                    window.log("Stream selesai.");
                }
                else {
                    console.log("callback:", info, obj);
                }
            },

            callbackError: function(error, message) {
                window.log("❌ ERROR: " + error + " — " + (message ?? ''));
                console.error(error, message);
            }
        });
    }

    function stopPublish() {
        if (adaptor) {
            adaptor.stop(STREAM_ID);
            window.log("Stream dihentikan.");
        }
    }
</script>
</body>
</html>