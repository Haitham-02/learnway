/**
 * AI Engine Module
 * Manages face-api.js lifecycle, webcam capture, and detection loop.
 */

import { EngagementAnalyzer } from './engagement-analyzer.js';

export class AIEngine {
    constructor(options = {}) {
        this.options = {
            modelPath: '/models',
            detectionInterval: 3000, // 3 seconds
            reportInterval: 10000,  // 10 seconds
            ...options
        };

        this.analyzer = new EngagementAnalyzer();
        this.isRunning = false;
        this.videoElement = null;
        this.stream = null;
        this.detectionLoopId = null;
        this.reportLoopId = null;
        this.dataBuffer = [];
        this.onAnalyticsCallback = options.onAnalytics || null;
    }

    /**
     * Initialize the AI Engine: Load models and setup webcam
     */
    async initialize() {
        console.log('🤖 AI Engine: Initializing with path:', this.options.modelPath);
        
        try {
            // Ensure faceapi is available from the global window object
            const api = window.faceapi;
            if (!api || !api.nets) {
                throw new Error('face-api.js library not found or incorrectly loaded');
            }

            // 1. Load Face-API models (TinyFaceDetector for performance)
            await Promise.all([
                api.nets.tinyFaceDetector.loadFromUri(this.options.modelPath),
                api.nets.faceExpressionNet.loadFromUri(this.options.modelPath)
            ]);
            
            this.faceapi = api;
            console.log('🤖 AI Engine: Models loaded successfully.');

            // 2. Setup hidden video element for capture
            this.videoElement = document.createElement('video');
            this.videoElement.id = 'ai-capture-video';
            // Hide the video element off-screen but keep opacity at 0.01 so browsers don't pause its rendering
            this.videoElement.style.cssText = 'position:fixed; top:-9999px; left:-9999px; width:640px; height:480px; opacity:0.01; pointer-events:none;';
            this.videoElement.muted = true;
            this.videoElement.playsInline = true;
            document.body.appendChild(this.videoElement);

            return true;
        } catch (error) {
            console.error('🤖 AI Engine: Initialization failed', error);
            return false;
        }
    }

    /**
     * Start the detection and reporting loops
     */
    async start(livestreamId) {
        if (this.isRunning) return;

        try {
            console.log('🤖 AI Engine: Requesting webcam access...');
            this.stream = await navigator.mediaDevices.getUserMedia({ 
                video: { width: 640, height: 480, frameRate: 15 } // Standard resolution for reliable detection
            });
            
            // Ensure the hidden video element has the correct HTML dimensions to match the stream
            this.videoElement.width = 640;
            this.videoElement.height = 480;
            this.videoElement.srcObject = this.stream;
            
            // Wait for video to be ready
            await new Promise((resolve) => {
                this.videoElement.onloadedmetadata = () => resolve();
            });
            
            await this.videoElement.play();

            this.isRunning = true;
            this.livestreamId = livestreamId;
            
            this.startDetectionLoop();
            this.startReportingLoop();
            
            console.log('🤖 AI Engine: Started detection loops.');
        } catch (error) {
            console.error('🤖 AI Engine: Failed to start webcam', error);
            throw error;
        }
    }

    startDetectionLoop() {
        const detect = async () => {
            if (!this.isRunning) return;

            try {
                // Lower scoreThreshold from 0.5 to 0.3 to catch faces even in poor lighting
                const detections = await this.faceapi.detectSingleFace(
                    this.videoElement, 
                    new this.faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.3 })
                ).withFaceExpressions();

                if (detections) {
                    const analysis = this.analyzer.analyze(detections.expressions, true);
                    this.dataBuffer.push({
                        timestamp: new Date().toISOString(),
                        emotion: analysis.primaryEmotion,
                        score: analysis.score,
                        status: analysis.status,
                        raw: detections.expressions
                    });
                } else {
                    this.dataBuffer.push({
                        timestamp: new Date().toISOString(),
                        emotion: 'absent',
                        score: 0,
                        status: 'absent'
                    });
                }
            } catch (err) {
                console.error('🤖 AI Engine: Detection error', err);
            }

            this.detectionLoopId = setTimeout(detect, this.options.detectionInterval);
        };
        detect();
    }

    startReportingLoop() {
        this.reportLoopId = setInterval(async () => {
            if (this.dataBuffer.length === 0) return;

            // Summarize buffer data to minimize payload
            const summary = this.summarizeBuffer();
            this.dataBuffer = []; // Clear buffer

            if (this.onAnalyticsCallback) {
                this.onAnalyticsCallback(summary);
            }
        }, this.options.reportInterval);
    }

    summarizeBuffer() {
        const count = this.dataBuffer.length;
        const emotions = {};
        let totalScore = 0;
        let presenceCount = 0;

        this.dataBuffer.forEach(item => {
            emotions[item.emotion] = (emotions[item.emotion] || 0) + 1;
            totalScore += item.score;
            if (item.status !== 'absent') presenceCount++;
        });

        // Find dominant emotion in this window
        const dominantEmotion = Object.entries(emotions).reduce((a, b) => a[1] > b[1] ? a : b)[0];

        return {
            livestreamId: this.livestreamId,
            emotion: dominantEmotion,
            confidence: (presenceCount / count),
            averageScore: Math.round(totalScore / count),
            additionalData: {
                samples: count,
                presenceRate: (presenceCount / count),
                emotionDistribution: emotions
            }
        };
    }

    stop() {
        this.isRunning = false;
        clearTimeout(this.detectionLoopId);
        clearInterval(this.reportLoopId);
        
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
        }
        if (this.videoElement) {
            this.videoElement.remove();
        }
        console.log('🤖 AI Engine: Stopped.');
    }
}
