import { Controller } from '@hotwired/stimulus';
import { io } from 'socket.io-client';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        url: String,
        livestreamId: String,
        currentUserId: String,
        currentUserName: String
    }

    connect() {
        console.log('🔌 LivestreamSocketController: Connecting to:', this.urlValue);
        
        this.socket = io(this.urlValue, {
            withCredentials: true,
            transports: ['websocket', 'polling']
        });

        this.socket.on('connect', () => {
            console.log('✅ LivestreamSocketController: Connected! ID:', this.socket.id);
            this.joinLivestream();
        });

        this.socket.on('connect_error', (error) => {
            console.error('❌ LivestreamSocketController: Connection error:', error);
        });

        // Listen for new chat messages
        this.socket.on('livestream-chat', (data) => {
            console.log('💬 Received livestream chat:', data);
            this.handleNewChatMessage(data);
        });

        // Listen for new Q&A questions
        this.socket.on('livestream-qa', (data) => {
            console.log('❓ Received livestream Q&A:', data);
            this.handleNewQuestion(data);
        });

        // Listen for Q&A answers
        this.socket.on('livestream-qa-answer', (data) => {
            console.log('✅ Received Q&A answer:', data);
            this.handleAnswerQuestion(data);
        });

        // Listen for AI Analytics updates (Teachers only)
        this.socket.on('livestream-ai-update', (data) => {
            console.log('🤖 Received AI update:', data);
            this.handleAIUpdate(data);
        });
    }

    joinLivestream() {
        const roomName = `livestream_${this.livestreamIdValue}_chat`;
        console.log('📍 Joining room:', roomName);
        this.socket.emit('join-room', roomName);

        // Teachers also join a private monitoring room
        const isTeacher = document.getElementById('livestream-session').dataset.livestreamIsTeacher === 'true';
        if (isTeacher) {
            const teacherRoom = `livestream_${this.livestreamIdValue}_teacher`;
            console.log('📍 Joining teacher room:', teacherRoom);
            this.socket.emit('join-room', teacherRoom);
        }
    }

    handleAIUpdate(data) {
        const list = document.getElementById('ai-monitoring-list');
        if (!list) return;

        // Clear "waiting" message if it's the first update
        if (list.querySelector('p')) {
            list.innerHTML = '';
        }

        // Update student list or create new entry
        const studentRowId = `ai-student-${data.studentId}`;
        let row = document.getElementById(studentRowId);

        if (!row) {
            row = document.createElement('div');
            row.id = studentRowId;
            row.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: var(--m3-surface-container-low); border-radius: 6px; font-size: 0.8125rem;';
            list.prepend(row);
        }

        const emotionEmoji = {
            happy: '😊',
            sad: '😢',
            angry: '😠',
            surprised: '😲',
            neutral: '😐',
            confused: '😕',
            absent: '❓'
        }[data.emotion] || '😐';

        row.innerHTML = `
            <span style="font-weight: 600;">${this.escapeHtml(data.studentName)}</span>
            <div style="display: flex; gap: 0.75rem; align-items: center;">
                <span>${emotionEmoji} ${data.emotion}</span>
                <span style="color: ${data.score > 60 ? '#4caf50' : '#ff9800'}; font-weight: bold;">${data.score}%</span>
                <span style="font-size: 0.65rem; color: var(--m3-on-surface-variant);">${data.timestamp}</span>
            </div>
        `;

        // Update aggregate score (Global Class Engagement)
        this.updateAggregateEngagement();
    }

    updateAggregateEngagement() {
        const list = document.getElementById('ai-monitoring-list');
        const rows = list.querySelectorAll('div[id^="ai-student-"]');
        
        if (rows.length === 0) return;

        let totalScore = 0;
        rows.forEach(row => {
            const scoreSpan = row.querySelector('span:nth-child(2) span');
            if (scoreSpan) {
                totalScore += parseInt(scoreSpan.textContent);
            }
        });

        const avg = Math.round(totalScore / rows.length);
        const bar = document.getElementById('focus-bar');
        const percent = document.getElementById('focus-percent');
        const distracted = document.getElementById('distracted-percent');
        const attendance = document.getElementById('attendance-count');

        if (bar) bar.style.width = `${avg}%`;
        if (percent) percent.textContent = `${avg}%`;
        if (distracted) distracted.textContent = `${100 - avg}%`;
        if (attendance) attendance.textContent = rows.length;

        // Update current dominant emotion display
        const emotionDisplay = document.getElementById('engagement-score');
        if (emotionDisplay) {
            emotionDisplay.textContent = avg > 75 ? 'High Engagement' : (avg > 45 ? 'Moderate' : 'Low');
        }
    }

    handleNewChatMessage(data) {
        // Skip if it's our own message (frontend already inserted it)
        if (String(data.userId) === String(this.currentUserIdValue)) {
            console.log('⏭️ Skipping own chat message');
            return;
        }

        // Check for duplicates
        const messageId = `chat-${data.id}`;
        if (document.getElementById(messageId)) {
            console.log('⏭️ Chat message already in DOM, skipping duplicate');
            return;
        }

        const chatMessagesDiv = document.getElementById('chat-messages');
        if (!chatMessagesDiv) return;

        const chatHTML = `
            <div id="${messageId}" style="
                padding: 0.75rem;
                background-color: var(--m3-surface);
                border-radius: 8px;
                display: flex;
                flex-direction: column;
            ">
                <p style="margin: 0 0 0.25rem 0; font-weight: 600; font-size: 0.875rem; color: var(--m3-primary);">
                    ${this.escapeHtml(data.userName)}
                </p>
                <p style="margin: 0; font-size: 0.875rem; color: var(--m3-on-surface);">
                    ${this.escapeHtml(data.message)}
                </p>
                <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem; color: var(--m3-on-surface-variant);">
                    ${data.createdAt}
                </p>
            </div>
        `;

        chatMessagesDiv.insertAdjacentHTML('beforeend', chatHTML);
        chatMessagesDiv.scrollTop = chatMessagesDiv.scrollHeight;
    }

    handleNewQuestion(data) {
        // Skip if it's our own question
        if (String(data.studentId) === String(this.currentUserIdValue)) {
            console.log('⏭️ Skipping own Q&A question');
            return;
        }

        // Check for duplicates
        const questionId = `qa-${data.id}`;
        if (document.getElementById(questionId)) {
            console.log('⏭️ Q&A question already in DOM, skipping duplicate');
            return;
        }

        const qaQuestionsDiv = document.getElementById('qa-questions');
        if (!qaQuestionsDiv) return;

        const qaHTML = `
            <div id="${questionId}" style="
                padding: 1rem;
                background-color: var(--m3-surface);
                border-radius: 8px;
                border-left: 4px solid #ff9800;
            ">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <p style="margin: 0 0 0.5rem 0; font-weight: 600; font-size: 0.875rem; color: var(--m3-primary);">
                            ${this.escapeHtml(data.studentName)}
                        </p>
                        <p style="margin: 0 0 0.75rem 0; font-size: 0.875rem; color: var(--m3-on-surface);">
                            ${this.escapeHtml(data.question)}
                        </p>
                    </div>
                </div>
            </div>
        `;

        qaQuestionsDiv.insertAdjacentHTML('beforeend', qaHTML);
        qaQuestionsDiv.scrollTop = qaQuestionsDiv.scrollHeight;
    }

    handleAnswerQuestion(data) {
        const questionId = `qa-${data.questionId}`;
        const questionElement = document.getElementById(questionId);
        
        if (!questionElement) return;

        // Update the question element to show it's answered
        questionElement.style.borderLeftColor = 'var(--m3-primary)';

        // Add answer if not already present
        if (!questionElement.querySelector('.answer-box')) {
            const answerHTML = `
                <div class="answer-box" style="
                    background-color: var(--m3-primary-container);
                    padding: 0.5rem;
                    border-radius: 6px;
                    font-size: 0.875rem;
                    color: var(--m3-on-primary-container);
                    margin-top: 0.75rem;
                ">
                    <p style="margin: 0 0 0.25rem 0; font-weight: 600;">Answered by ${this.escapeHtml(data.answeredByName)}</p>
                    <p style="margin: 0;">${this.escapeHtml(data.answer)}</p>
                </div>
            `;
            questionElement.insertAdjacentHTML('beforeend', answerHTML);
        }
    }

    // Utility function to prevent XSS attacks
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    disconnect() {
        if (this.socket) {
            this.socket.disconnect();
        }
    }
}
