import { Controller } from '@hotwired/stimulus';
import { io } from 'socket.io-client';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        url: String,
        room: String,
        currentUserId: String
    }

    connect() {
        console.log('SocketController: Attempting to connect to:', this.urlValue);
        this.socket = io(this.urlValue, {
            withCredentials: true,
            transports: ['websocket', 'polling']
        });

        this.socket.on('connect', () => {
            console.log('SocketController: Connected! ID:', this.socket.id);
            console.log('SocketController: Joining room:', this.roomValue);
            this.socket.emit('join-room', this.roomValue);
        });

        this.socket.on('connect_error', (error) => {
            console.error('SocketController: Connection error:', error);
        });

        this.socket.on('new-message', (html) => {
            console.log('SocketController: Received new message');
            
            // Create a temporary element to manipulate the HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const messageWrapper = tempDiv.firstElementChild;
            const senderId = messageWrapper.dataset.senderId;

            // Adjust styling if it's not our message
            if (senderId !== this.currentUserIdValue) {
                messageWrapper.style.alignItems = 'flex-start';
                messageWrapper.classList.remove('message-mine');
                messageWrapper.classList.add('message-theirs');
                const content = messageWrapper.querySelector('.message-content');
                content.style.background = 'var(--m3-surface-container-high)';
                content.style.color = 'var(--m3-on-surface)';
                content.style.borderBottomRightRadius = '20px';
                content.style.borderBottomLeftRadius = '4px';
            } else {
                // If it IS our message (sent from another tab), ensure it looks like ours
                messageWrapper.style.alignItems = 'flex-end';
                messageWrapper.classList.add('message-mine');
                messageWrapper.classList.remove('message-theirs');
                const content = messageWrapper.querySelector('.message-content');
                content.style.background = 'var(--m3-primary)';
                content.style.color = 'white';
                content.style.borderBottomRightRadius = '4px';
                content.style.borderBottomLeftRadius = '20px';
            }

            this.element.insertAdjacentHTML('beforeend', messageWrapper.outerHTML);
            this.element.scrollTop = this.element.scrollHeight;
        });
    }

    disconnect() {
        if (this.socket) {
            this.socket.disconnect();
        }
    }
}
