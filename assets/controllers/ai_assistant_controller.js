import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["panel", "messages", "input", "indicator", "fab", "suggestions", "charCount"];
    static values = {
        chatUrl: String,
        uploadUrl: String,
        suggestions: Array,
        logoUrl: String
    };

    chatId = null;
    isOpen = false;
    isTyping = false;

    // Drag state
    isDragging = false;
    hasMoved = false;
    dragStartX = 0;
    dragStartY = 0;
    currentTranslateX = 0;
    currentTranslateY = 0;
    tempTranslateX = 0;
    tempTranslateY = 0;

    connect() {
        this.renderSuggestions();
        this.inputTarget.addEventListener('input', () => this.onInput());
        this.inputTarget.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Bind drag handlers
        this.dragMoveHandler = this.dragMove.bind(this);
        this.dragEndHandler = this.dragEnd.bind(this);
    }

    onInput() {
        const val = this.inputTarget.value;
        this.autoResize();
    }

    autoResize() {
        const el = this.inputTarget;
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 120) + 'px';
    }

    dragStart(e) {
        if (e.type === 'mousedown' && e.button !== 0) return;

        this.isDragging = true;
        this.hasMoved = false;
        
        const clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
        const clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;

        this.dragStartX = clientX;
        this.dragStartY = clientY;

        document.body.style.userSelect = 'none';
        this.element.style.transition = 'none';

        document.addEventListener('mousemove', this.dragMoveHandler);
        document.addEventListener('touchmove', this.dragMoveHandler, { passive: false });
        document.addEventListener('mouseup', this.dragEndHandler);
        document.addEventListener('touchend', this.dragEndHandler);
    }

    dragMove(e) {
        if (!this.isDragging) return;

        const clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
        const clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;

        const deltaX = clientX - this.dragStartX;
        const deltaY = clientY - this.dragStartY;

        if (Math.abs(deltaX) > 3 || Math.abs(deltaY) > 3) {
            this.hasMoved = true;
            if (e.type === 'touchmove') e.preventDefault();
        }

        if (this.hasMoved) {
            this.tempTranslateX = this.currentTranslateX + deltaX;
            this.tempTranslateY = this.currentTranslateY + deltaY;
            this.element.style.transform = `translate(${this.tempTranslateX}px, ${this.tempTranslateY}px)`;
        }
    }

    dragEnd(e) {
        if (!this.isDragging) return;
        this.isDragging = false;

        document.body.style.userSelect = '';
        this.element.style.transition = '';

        document.removeEventListener('mousemove', this.dragMoveHandler);
        document.removeEventListener('touchmove', this.dragMoveHandler);
        document.removeEventListener('mouseup', this.dragEndHandler);
        document.removeEventListener('touchend', this.dragEndHandler);

        if (this.hasMoved) {
            this.currentTranslateX = this.tempTranslateX;
            this.currentTranslateY = this.tempTranslateY;
        } else {
            this.togglePanel();
        }
    }

    togglePanel() {
        this.isOpen = !this.isOpen;
        const panel = this.panelTarget;
        const fab = this.fabTarget;

        if (this.isOpen) {
            panel.style.display = 'flex';
            requestAnimationFrame(() => {
                panel.style.opacity = '1';
                panel.style.transform = 'translateY(0) scale(1)';
            });
            fab.classList.add('open');
            this.inputTarget.focus();
        } else {
            panel.style.opacity = '0';
            panel.style.transform = 'translateY(16px) scale(0.97)';
            setTimeout(() => { panel.style.display = 'none'; }, 200);
            fab.classList.remove('open');
        }
    }

    renderSuggestions() {
        const suggestions = this.suggestionsValue || [];

        this.suggestionsTarget.innerHTML = suggestions.map(s => `
            <button class="copilot-suggestion" data-action="click->ai-assistant#useSuggestion" data-text="${this.escapeHtml(s.text)}">
                <span class="suggestion-icon material-symbols-rounded">${s.icon}</span>
                <span>${this.escapeHtml(s.text)}</span>
            </button>
        `).join('');
    }

    useSuggestion(event) {
        const text = event.currentTarget.dataset.text;
        this.inputTarget.value = text;
        this.autoResize();
        this.sendMessage();
    }

    async sendMessage() {
        const message = this.inputTarget.value.trim();
        if (!message || this.isTyping) return;

        // Hide suggestions after first message
        if (this.suggestionsTarget.style.display !== 'none') {
            this.suggestionsTarget.style.opacity = '0';
            setTimeout(() => { this.suggestionsTarget.style.display = 'none'; }, 200);
        }

        this.appendMessage(message, 'user');
        this.inputTarget.value = '';
        this.inputTarget.style.height = 'auto';
        this.isTyping = true;
        this.showTypingIndicator(true);

        try {
            const response = await fetch(this.chatUrlValue, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message, chatId: this.chatId })
            });

            const data = await response.json();
            this.showTypingIndicator(false);

            if (data.response) {
                this.chatId = data.chatId;
                this.appendMessage(data.response, 'assistant');
            } else {
                this.appendMessage('[ICON:warning] ' + (data.error || 'Something went wrong.'), 'error');
            }
        } catch (err) {
            this.showTypingIndicator(false);
            this.appendMessage('[ICON:warning] Unable to reach Learnway AI. Please check your connection.', 'error');
        } finally {
            this.isTyping = false;
        }
    }

    async uploadFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        const ext = file.name.split('.').pop().toUpperCase();
        const icon = ext === 'PDF' ? '[ICON:picture_as_pdf]' : ext === 'DOCX' ? '[ICON:description]' : '[ICON:draft]';

        this.appendMessage(`${icon} Uploading **${file.name}**...`, 'upload');
        this.showTypingIndicator(true);

        const formData = new FormData();
        formData.append('file', file);

        try {
            const response = await fetch(this.uploadUrlValue, { method: 'POST', body: formData });
            const data = await response.json();
            this.showTypingIndicator(false);

            if (data.status) {
                this.appendMessage(data.summary || `[ICON:check_circle_green] **${file.name}** has been indexed!`, 'assistant');
            } else {
                this.appendMessage(`[ICON:cancel] Upload failed: ${data.error}`, 'error');
            }
        } catch (err) {
            this.showTypingIndicator(false);
            this.appendMessage('[ICON:cancel] Upload error. Please try again.', 'error');
        } finally {
            event.target.value = '';
        }
    }

    appendMessage(text, role) {
        const wrap = document.createElement('div');
        wrap.className = `copilot-msg copilot-msg--${role}`;

        const isAI = role === 'assistant' || role === 'error';

        if (isAI) {
            wrap.innerHTML = `
                <div class="copilot-avatar" style="background: transparent; box-shadow: none;">
                    <img src="${this.logoUrlValue}" style="width: 20px; height: 20px; object-fit: contain;">
                </div>
                <div class="copilot-bubble copilot-bubble--ai">
                    ${this.renderMarkdown(text)}
                </div>
            `;
        } else if (role === 'upload') {
            wrap.innerHTML = `<div class="copilot-bubble copilot-bubble--upload">${this.renderMarkdown(text)}</div>`;
        } else {
            wrap.innerHTML = `<div class="copilot-bubble copilot-bubble--user">${this.escapeHtml(text)}</div>`;
        }

        this.messagesTarget.appendChild(wrap);
        this.scrollToBottom();

        // Animate in
        requestAnimationFrame(() => wrap.classList.add('visible'));
    }

    showTypingIndicator(show) {
        let indicator = this.messagesTarget.querySelector('.copilot-typing');
        if (show && !indicator) {
            indicator = document.createElement('div');
            indicator.className = 'copilot-msg copilot-msg--assistant copilot-typing';
            indicator.innerHTML = `
                <div class="copilot-avatar" style="background: transparent; box-shadow: none;">
                    <img src="${this.logoUrlValue}" style="width: 20px; height: 20px; object-fit: contain;">
                </div>
                <div class="copilot-bubble copilot-bubble--ai">
                    <span class="dot"></span><span class="dot"></span><span class="dot"></span>
                </div>
            `;
            this.messagesTarget.appendChild(indicator);
            requestAnimationFrame(() => indicator.classList.add('visible'));
            this.scrollToBottom();
        } else if (!show && indicator) {
            indicator.remove();
        }
    }

    renderMarkdown(text) {
        // Basic markdown-like rendering
        return this.escapeHtml(text)
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/`(.*?)`/g, '<code>$1</code>')
            .replace(/^#{1,3}\s(.+)$/gm, '<p class="copilot-heading">$1</p>')
            .replace(/^[-•]\s(.+)$/gm, '<li>$1</li>')
            .replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>')
            .replace(/\n\n/g, '</p><p>')
            .replace(/\n/g, '<br>')
            .replace(/\[ICON:check_circle_green\]/g, '<span class="material-symbols-rounded" style="vertical-align: middle; font-size: 1.2em; color: #4ade80;">check_circle</span>')
            .replace(/\[ICON:([a-z_]+)\]/g, '<span class="material-symbols-rounded" style="vertical-align: middle; font-size: 1.2em;">$1</span>');
    }

    escapeHtml(text) {
        return text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    scrollToBottom() {
        this.messagesTarget.scrollTo({ top: this.messagesTarget.scrollHeight, behavior: 'smooth' });
    }

    clearChat() {
        this.chatId = null;
        this.messagesTarget.innerHTML = '';
        this.suggestionsTarget.style.display = 'flex';
        this.suggestionsTarget.style.opacity = '1';
        this.renderSuggestions();
        this.appendMessage("Chat cleared! How can I help you?", 'assistant');
    }
}
