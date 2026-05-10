/**
 * Engagement Analyzer Module
 * Handles calculation of engagement scores based on facial expressions and presence.
 */

export class EngagementAnalyzer {
    constructor() {
        this.weights = {
            happy: 1.0,
            surprised: 0.8,
            neutral: 0.6,
            sad: 0.2,
            angry: 0.1,
            disgusted: 0.1,
            fearful: 0.3
        };
    }

    /**
     * Calculate engagement score from expressions
     * @param {Object} expressions - Object containing emotion probabilities
     * @param {boolean} faceDetected - Whether a face was detected
     * @returns {Object} - Engagement metrics
     */
    analyze(expressions, faceDetected) {
        if (!faceDetected) {
            return {
                score: 0,
                status: 'absent',
                attention: 0
            };
        }

        // Weighted sum of positive vs negative emotions
        let score = 0;
        let totalWeight = 0;

        for (const [emotion, probability] of Object.entries(expressions)) {
            const weight = this.weights[emotion] || 0.5;
            score += probability * weight;
        }

        // Focus detection: If neutral is very high, it usually means high focus in learning
        const isFocused = expressions.neutral > 0.5 || expressions.happy > 0.2 || expressions.surprised > 0.1;
        
        // Confusion heuristic: Rapid switching or high 'sad/fearful' during technical content
        // (Simplified for this version)
        const isConfused = expressions.surprised > 0.4 && expressions.neutral < 0.3;

        return {
            score: Math.min(Math.round(score * 100), 100),
            status: isFocused ? 'focused' : (isConfused ? 'confused' : 'distracted'),
            attention: isFocused ? 1.0 : 0.4,
            primaryEmotion: this.getDominantEmotion(expressions)
        };
    }

    getDominantEmotion(expressions) {
        return Object.entries(expressions).reduce((a, b) => a[1] > b[1] ? a : b)[0];
    }
}
