import { Controller } from '@hotwired/stimulus';
import { io } from 'socket.io-client';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        url: String,
        userId: Number
    }

    connect() {
        if (!this.hasUserIdValue || !this.userIdValue) {
            return;
        }

        this.socket = io(this.urlValue, {
            withCredentials: true,
            transports: ['websocket', 'polling']
        });

        this.socket.on('connect', () => {
            this.socket.emit('join-room', `notifications_${this.userIdValue}`);
        });

        this.socket.on('notification-refresh', () => {
            if (window.htmx) {
                htmx.trigger(document.body, 'refreshNotifications');
            }

            this.showToast('New message received');
        });
    }

    showToast(message) {
        let container = document.getElementById('lw-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'lw-toast-container';
            container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.cssText = 'min-width: 240px; max-width: 320px; padding: 12px 16px; border-radius: 14px; background: var(--m3-surface); color: var(--m3-on-surface); border: 1px solid var(--m3-outline); box-shadow: 0 10px 24px rgba(0,0,0,0.16); font-size: 0.875rem; font-weight: 600; transform: translateY(-6px); opacity: 0; transition: opacity 180ms ease, transform 180ms ease;';

        container.appendChild(toast);
        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        });

        window.setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-6px)';
            window.setTimeout(() => toast.remove(), 180);
        }, 3500);
    }

    disconnect() {
        if (this.socket) {
            this.socket.disconnect();
        }
    }
}
