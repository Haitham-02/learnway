// Livestream Session Controller
// Handles Jitsi Meet and Basic Session Controls

// ========== JITSI MEET INTEGRATION ==========
let jitsiApi = null;
let micEnabled = true;
let cameraEnabled = true;
let jitsiInitialized = false;
let jitsiInitRetries = 0;
let maxJitsiRetries = 50; 

/**
 * Initialize Jitsi Meet with proper error handling and retries
 */
function initializeJitsi() {
    jitsiInitRetries++;
    
    if (jitsiInitRetries > maxJitsiRetries) {
        console.error('❌ Jitsi initialization failed');
        alert('Failed to load Jitsi Meet. Please refresh the page.');
        return;
    }

    if (jitsiInitialized) return;

    if (typeof JitsiMeetExternalAPI === 'undefined') {
        setTimeout(initializeJitsi, 100);
        return;
    }

    const container = document.getElementById('jitsi-container');
    if (!container) {
        setTimeout(initializeJitsi, 100);
        return;
    }

    try {
        const options = {
            roomName: meetingRoom,
            width: '100%',
            height: 600,
            parentNode: container,
            configOverwrite: {
                startAudioMuted: false,
                startVideoMuted: false,
                enableWelcomePage: false,
                prejoinPageEnabled: false,
            },
            userInfo: {
                displayName: userName,
            },
        };

        jitsiApi = new JitsiMeetExternalAPI('meet.jit.si', options);
        jitsiInitialized = true;

        jitsiApi.addEventListener('readyToClose', onReadyToClose);

    } catch (error) {
        console.error('❌ Error creating Jitsi instance:', error);
        setTimeout(initializeJitsi, 100);
    }
}

function onReadyToClose() {
    if (jitsiApi) {
        jitsiApi.dispose();
        jitsiApi = null;
        jitsiInitialized = false;
    }
}

// ========== CONTROL BUTTONS ==========

function toggleMic() {
    const btn = document.getElementById('mic-btn');
    micEnabled = !micEnabled;
    if (jitsiApi) jitsiApi.executeCommand('toggleAudio');
    btn.textContent = micEnabled ? '🎤 Mic On' : '🔇 Mic Off';
    btn.style.backgroundColor = micEnabled ? 'var(--m3-primary)' : '#f44336';
}

function toggleCamera() {
    const btn = document.getElementById('camera-btn');
    cameraEnabled = !cameraEnabled;
    if (jitsiApi) jitsiApi.executeCommand('toggleVideo');
    btn.textContent = cameraEnabled ? '📹 Camera On' : '📹 Camera Off';
    btn.style.backgroundColor = cameraEnabled ? 'var(--m3-primary)' : '#f44336';
}

function toggleScreenShare() {
    if (jitsiApi) jitsiApi.executeCommand('toggleShareScreen');
}

function endSession() {
    if (confirm('End this livestream session?')) {
        fetch(window.livestreamEndSessionUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(r => r.json())
        .then(() => {
            window.location.href = window.livestreamTeacherListUrl || '/livestream/teacher/livestreams';
        })
        .catch(err => alert('Error: ' + err));
    }
}

// ========== INITIALIZATION ==========
window.addEventListener('load', function() {
    if (!jitsiInitialized && typeof JitsiMeetExternalAPI !== 'undefined') {
        initializeJitsi();
    }
});

setTimeout(function() {
    if (!jitsiInitialized) initializeJitsi();
}, 1000);
