// Livestream Session Controller
// Handles Jitsi Meet, Facial Recognition, Chat, Q&A

// ========== JITSI MEET INTEGRATION ==========
let jitsiApi = null;
let micEnabled = true;
let cameraEnabled = true;

function initializeJitsi() {
    const options = {
        roomName: meetingRoom,
        width: '100%',
        height: 600,
        parentNode: document.getElementById('jitsi-container'),
        configOverwrite: {
            startAudioMuted: false,
            startVideoMuted: false,
            enableWelcomePage: false,
            disableAudioLevels: false,
        },
        interfaceConfigOverwrite: {
            DISABLE_FOCUS_INDICATOR: false,
            TOOLBAR_BUTTONS: [
                'microphone', 'camera', 'desktop', 'fullscreen',
                'fodeviceselection', 'hangup', 'profile', 'chat', 'recording',
                'livestream', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
                'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
                'tileview', 'toggle-camera', 'download-logs'
            ],
        },
        userInfo: {
            displayName: userName,
        },
    };

    jitsiApi = new JitsiMeetExternalAPI(
        'meet.jit.si',
        options
    );

    // Event listeners
    jitsiApi.addEventListener('videoConferenceJoined', onVideoConferenceJoined);
    jitsiApi.addEventListener('videoConferenceLeft', onVideoConferenceLeft);
    jitsiApi.addEventListener('participantJoined', onParticipantJoined);
    jitsiApi.addEventListener('participantLeft', onParticipantLeft);
}

function onVideoConferenceJoined() {
    console.log('✅ Joined video conference');
    updateStats();
}

function onVideoConferenceLeft() {
    console.log('👋 Left video conference');
}

function onParticipantJoined(participant) {
    console.log('👤 Participant joined:', participant);
    updateStats();
}

function onParticipantLeft(participant) {
    console.log('👋 Participant left:', participant);
    updateStats();
}

// ========== CONTROL BUTTONS ==========

function toggleMic() {
    const btn = document.getElementById('mic-btn');
    micEnabled = !micEnabled;

    if (jitsiApi) {
        jitsiApi.executeCommand('toggleAudio');
    }

    btn.textContent = micEnabled ? '🎤 Mic On' : '🔇 Mic Off';
    btn.style.backgroundColor = micEnabled ? 'var(--m3-primary)' : '#f44336';
}

function toggleCamera() {
    const btn = document.getElementById('camera-btn');
    cameraEnabled = !cameraEnabled;

    if (jitsiApi) {
        jitsiApi.executeCommand('toggleVideo');
    }

    btn.textContent = cameraEnabled ? '📹 Camera On' : '📹 Camera Off';
    btn.style.backgroundColor = cameraEnabled ? 'var(--m3-primary)' : '#f44336';
}

function toggleScreenShare() {
    if (jitsiApi) {
        jitsiApi.executeCommand('toggleShareScreen');
    }
}

function endSession() {
    if (confirm('End this livestream session?')) {
        fetch(`/livestream/teacher/livestreams/{{ livestream.id }}/end`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            alert('Session ended');
            window.location.href = '/livestream/teacher/livestreams';
        })
        .catch(err => alert('Error: ' + err));
    }
}

// ========== SIDEBAR TABS ==========

function switchSidebarTab(tab) {
    // Hide all panels
    document.getElementById('chat-panel').style.display = 'none';
    document.getElementById('qa-panel').style.display = 'none';
    document.getElementById('ai-panel').style.display = 'none';

    // Show selected panel
    document.getElementById(tab + '-panel').style.display = 'flex';

    // Update tab styling
    const tabs = ['chat', 'qa', 'ai'];
    tabs.forEach(t => {
        const btn = document.getElementById('tab-' + t);
        btn.style.color = t === tab ? 'var(--m3-primary)' : 'var(--m3-on-surface-variant)';
        btn.style.borderBottomColor = t === tab ? 'var(--m3-primary)' : 'transparent';
    });
}

// ========== CHAT FUNCTIONALITY ==========

function sendChat() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();

    if (!message) return;

    fetch(`/livestream/api/chat/{{ livestream.id }}/send`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'message=' + encodeURIComponent(message)
    })
    .then(r => r.json())
    .then(data => {
        const messagesDiv = document.getElementById('chat-messages');
        const chatHTML = `
            <div style="
                padding: 0.75rem;
                background-color: var(--m3-surface);
                border-radius: 8px;
                display: flex;
                flex-direction: column;
            ">
                <p style="margin: 0 0 0.25rem 0; font-weight: 600; font-size: 0.875rem; color: var(--m3-primary);">
                    ${data.user}
                </p>
                <p style="margin: 0; font-size: 0.875rem; color: var(--m3-on-surface);">${data.message}</p>
                <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem; color: var(--m3-on-surface-variant);">
                    ${data.created_at.split(' ')[1]}
                </p>
            </div>
        `;
        messagesDiv.innerHTML += chatHTML;
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        input.value = '';
    })
    .catch(err => console.error('Chat error:', err));
}

// Allow Enter key to send
document.addEventListener('DOMContentLoaded', () => {
    const chatInput = document.getElementById('chat-input');
    if (chatInput) {
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendChat();
            }
        });
    }
});

// ========== Q&A FUNCTIONALITY ==========

function askQuestion(e) {
    e.preventDefault();
    const input = document.getElementById('question-input');
    const question = input.value.trim();

    if (!question) return;

    fetch(`/livestream/api/question/{{ livestream.id }}/ask`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'question=' + encodeURIComponent(question)
    })
    .then(r => r.json())
    .then(data => {
        const questionsDiv = document.getElementById('qa-questions');
        const qaHTML = `
            <div style="
                padding: 1rem;
                background-color: var(--m3-surface);
                border-radius: 8px;
                border-left: 4px solid #ff9800;
            ">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <p style="margin: 0 0 0.5rem 0; font-weight: 600; font-size: 0.875rem; color: var(--m3-primary);">
                            ${data.student}
                        </p>
                        <p style="margin: 0; font-size: 0.875rem; color: var(--m3-on-surface);">
                            ${data.question}
                        </p>
                    </div>
                </div>
            </div>
        `;
        questionsDiv.innerHTML += qaHTML;
        questionsDiv.scrollTop = questionsDiv.scrollHeight;
        input.value = '';

        // Update question count
        const count = document.getElementById('question-count');
        count.textContent = parseInt(count.textContent) + 1;
    })
    .catch(err => console.error('Q&A error:', err));
}

function answerQuestion(e, questionId) {
    e.preventDefault();
    const form = e.target;
    const answer = form.querySelector('textarea').value.trim();

    if (!answer) return;

    fetch(`/livestream/api/question/${questionId}/answer`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'answer=' + encodeURIComponent(answer)
    })
    .then(r => r.json())
    .then(data => {
        alert('Answer posted!');
        location.reload();
    })
    .catch(err => alert('Error: ' + err));
}

// ========== FACIAL EXPRESSION TRACKING ==========

let facialTrackingActive = false;
let facialTrackingInterval = null;

function toggleFacialTracking() {
    const toggle = document.getElementById('facial-toggle');
    facialTrackingActive = toggle.checked;

    if (facialTrackingActive) {
        loadFaceApiModels();
    } else {
        stopFacialTracking();
    }
}

async function loadFaceApiModels() {
    try {
        // Models should be in /public/models/ directory
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';

        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
            faceapi.nets.faceExpressionNet.loadFromUri(MODEL_URL),
        ]);

        startFacialTracking();
    } catch (err) {
        console.error('Failed to load facial recognition models:', err);
        alert('Could not load facial recognition models. Please ensure models are in /public/models/');
        document.getElementById('facial-toggle').checked = false;
    }
}

async function startFacialTracking() {
    const statusDiv = document.getElementById('facial-status');
    const emotionDisplay = document.getElementById('emotion-display');

    statusDiv.innerHTML = '🟢 Tracking active...';
    statusDiv.style.color = '#4caf50';
    emotionDisplay.style.display = 'block';

    // Get video element from Jitsi
    const video = document.querySelector('iframe');
    if (!video) {
        console.warn('Video element not found in Jitsi container');
    }

    facialTrackingInterval = setInterval(async () => {
        try {
            // Use canvas element if available (workaround for iframe)
            const canvas = document.createElement('canvas');
            const detections = await faceapi
                .detectAllFaces(canvas, new faceapi.TinyFaceDetectorOptions())
                .withFaceExpressions();

            if (detections.length > 0) {
                const expressions = detections[0].expressions;
                const dominant = getDominantEmotion(expressions);

                document.getElementById('emotion-text').textContent = `${dominant.emotion} (${(dominant.confidence * 100).toFixed(0)}%)`;

                // Send to backend
                sendFacialData(dominant.emotion, dominant.confidence);

                // Update focus bar simulation
                const focusScore = Math.random() * 100;
                document.getElementById('focus-bar').style.width = focusScore + '%';
                document.getElementById('focus-percent').textContent = Math.round(focusScore) + '%';
                document.getElementById('distracted-percent').textContent = (100 - Math.round(focusScore)) + '%';
            }
        } catch (err) {
            console.warn('Facial tracking error:', err);
        }
    }, 3000); // Every 3 seconds
}

function getDominantEmotion(expressions) {
    const emotions = {
        'neutral': expressions.neutral || 0,
        'happy': expressions.happy || 0,
        'sad': expressions.sad || 0,
        'angry': expressions.angry || 0,
        'fearful': expressions.fearful || 0,
        'disgusted': expressions.disgusted || 0,
        'surprised': expressions.surprised || 0,
    };

    let dominant = { emotion: 'neutral', confidence: 0 };

    Object.entries(emotions).forEach(([emotion, confidence]) => {
        if (confidence > dominant.confidence) {
            dominant = { emotion, confidence };
        }
    });

    return dominant;
}

function sendFacialData(emotion, confidence) {
    fetch('/livestream/api/facial-data', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            livestreamId: livestreamId,
            emotion: emotion,
            confidence: confidence.toFixed(4),
            additionalData: null
        })
    })
    .catch(err => console.warn('Failed to send facial data:', err));
}

function stopFacialTracking() {
    if (facialTrackingInterval) {
        clearInterval(facialTrackingInterval);
        facialTrackingInterval = null;
    }

    const statusDiv = document.getElementById('facial-status');
    const emotionDisplay = document.getElementById('emotion-display');

    statusDiv.innerHTML = '🔴 Disabled';
    statusDiv.style.color = 'var(--m3-on-surface-variant)';
    emotionDisplay.style.display = 'none';
}

// ========== STATS & UPDATES ==========

function updateStats() {
    // These are demo values - in production, fetch from backend
    const stats = {
        engagement: Math.floor(Math.random() * 40 + 60), // 60-100%
        attendance: Math.floor(Math.random() * 15 + 8), // 8-23 out of 25
        avgResponse: (Math.random() * 2 + 1).toFixed(1), // 1-3 minutes
    };

    document.getElementById('engagement-score').textContent = stats.engagement + '%';
    document.getElementById('attendance-count').textContent = stats.attendance + '/25';
    document.getElementById('response-time').textContent = stats.avgResponse + 'm';
}

// Update stats every 10 seconds
setInterval(updateStats, 10000);
