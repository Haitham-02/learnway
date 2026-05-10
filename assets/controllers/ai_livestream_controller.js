import { Controller } from '@hotwired/stimulus';
import { AIEngine } from '../js/ai/ai-engine.js';

export default class extends Controller {
    static values = {
        livestreamId: String,
        isTeacher: Boolean,
        apiUrl: String,
        modelsUrl: String
    }

    async connect() {
        if (this.isTeacherValue) {
            this.setupTeacherMonitoring();
        } else {
            await this.setupStudentDetection();
        }
    }

    // ================= STUDENT LOGIC =================

    async setupStudentDetection() {
        console.log('🤖 AI Controller: Initializing for student...');
        
        this.engine = new AIEngine({
            modelPath: this.modelsUrlValue,
            onAnalytics: (data) => this.sendAnalytics(data)
        });

        const initialized = await this.engine.initialize();
        if (initialized) {
            try {
                await this.engine.start(this.livestreamIdValue);
                this.showStatusIndicator();
            } catch (err) {
                console.warn('🤖 AI Controller: Webcam access denied or failed.', err);
            }
        }
    }

    async sendAnalytics(data) {
        console.log('🤖 AI Controller: Sending summarized analytics...', data);
        
        try {
            const response = await fetch(this.apiUrlValue, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) throw new Error('Failed to send AI data');
        } catch (err) {
            console.error('🤖 AI Controller: Error reporting analytics', err);
        }
    }

    showStatusIndicator() {
        const indicator = document.getElementById('student-ai-status');
        if (indicator) indicator.style.opacity = '1';
    }

    // ================= TEACHER LOGIC =================

    setupTeacherMonitoring() {
        console.log('🤖 AI Controller: Initializing for teacher...');
        // Monitoring is handled via Socket.io events in livestream_socket_controller.js
        // This controller can handle local UI updates for the AI panel if needed.
    }

    disconnect() {
        if (this.engine) {
            this.engine.stop();
        }
    }
}
