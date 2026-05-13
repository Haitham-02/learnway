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
            if (!messageWrapper) {
                return;
            }

            const messageId = messageWrapper.dataset.messageId;
            const senderId = messageWrapper.dataset.senderId;

            // CRITICAL: If this message is from the current user, SKIP IT
            // HTMX already inserted it immediately, so don't insert again via Socket.IO
            if (String(senderId) === String(this.currentUserIdValue)) {
                console.log('SocketController: Skipping own message (ID:', messageId, ') - HTMX already inserted');
                return;
            }

            // For other users' messages, check if already in DOM
            const existingMessage = document.querySelector(`[data-message-id="${messageId}"]`);
            if (existingMessage) {
                console.log('SocketController: Message already in DOM, replacing updated version (ID:', messageId, ')');
                existingMessage.outerHTML = messageWrapper.outerHTML;
                return;
            }

            console.log('SocketController: Inserting message from other user (ID:', messageId, ')');

            // No need to manually adjust styling anymore!
            // The server now renders the broadcasted HTML with `forceTheirs=true`
            // so it arrives perfectly styled as a received message.

            this.element.insertAdjacentHTML('beforeend', messageWrapper.outerHTML);
            this.element.scrollTop = this.element.scrollHeight;
        });

        this.socket.on('conversation-refresh', () => {
            this.refreshConversation();
        });
    }

    async refreshConversation() {
        try {
            const response = await fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                return;
            }

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const freshContainer = doc.querySelector('#message-container');

            if (!freshContainer) {
                return;
            }

            this.element.innerHTML = freshContainer.innerHTML;
            this.element.scrollTop = this.element.scrollHeight;
        } catch (error) {
            console.error('SocketController: Conversation refresh failed', error);
        }
    }

    disconnect() {
        if (this.socket) {
            this.socket.disconnect();
        }
    }
}
