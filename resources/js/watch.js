import { WebRTCAdaptor } from '@antmedia/webrtc_adaptor';

const AMS_WS = "ws://localhost:5080/LiveApp/websocket";
const players = {};

window.watchStream = function(streamId) {
    if (players[streamId]) return;

    const videoEl = document.getElementById(`video-${streamId}`);
    if (!videoEl) return;

    players[streamId] = new WebRTCAdaptor({
        websocket_url: AMS_WS,
        remoteVideoElement: videoEl,  // pakai remoteVideoElement, bukan remoteVideoId
        callback: function(info, obj) {
            console.log("callback info:", info);
            if (info === "initialized") {
                players[streamId].play(streamId);
            }
            else if (info === "play_started") {
                console.log("Mulai tonton:", streamId);
            }
            else if (info === "play_finished") {
                console.log("Stream selesai:", streamId);
                delete players[streamId];
            }
        },
        callbackError: function(error, message) {
            console.error("Player error:", streamId, error, message);
        }
    });
}

window.stopWatch = function(streamId) {
    if (players[streamId]) {
        players[streamId].stop(streamId);
        delete players[streamId];
    }
}