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
        });
    }

    disconnect() {
        if (this.socket) {
            this.socket.disconnect();
        }
    }
}
