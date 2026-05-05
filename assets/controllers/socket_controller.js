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
            const messageId = messageWrapper.dataset.messageId;
            const senderId = messageWrapper.dataset.senderId;

            // CRITICAL: If this message is from the current user, SKIP IT
            // HTMX already inserted it immediately, so don't insert again via Socket.IO
            if (senderId === this.currentUserIdValue) {
                console.log('SocketController: Skipping own message (ID:', messageId, ') - HTMX already inserted');
                return;
            }

            // For other users' messages, check if already in DOM
            const existingMessage = document.querySelector(`[data-message-id="${messageId}"]`);
            if (existingMessage) {
                console.log('SocketController: Message already in DOM, skipping duplicate (ID:', messageId, ')');
                return;
            }

            console.log('SocketController: Inserting message from other user (ID:', messageId, ')');

            // Adjust styling for messages from other users
            messageWrapper.style.alignItems = 'flex-start';
            messageWrapper.classList.remove('message-mine');
            messageWrapper.classList.add('message-theirs');
            const content = messageWrapper.querySelector('.message-content');
            content.style.background = 'var(--m3-surface-container-high)';
            content.style.color = 'var(--m3-on-surface)';
            content.style.borderBottomRightRadius = '20px';
            content.style.borderBottomLeftRadius = '4px';

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
