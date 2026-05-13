import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["panel", "messages", "input", "indicator", "fab", "suggestions", "charCount"];
    static values = {
        chatUrl: String,
        uploadUrl: String,
        historyUrl: String,
        autoScheduleUrl: String,
        requestChangeUrl: String,
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
        this.loadState();
        
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

        if (this.chatId) {
            this.fetchHistory();
        }
    }

    loadState() {
        this.chatId = localStorage.getItem('learnway_ai_chat_id');
        this.currentTranslateX = parseFloat(localStorage.getItem('learnway_ai_pos_x') || 0);
        this.currentTranslateY = parseFloat(localStorage.getItem('learnway_ai_pos_y') || 0);
        this.element.style.transform = `translate(${this.currentTranslateX}px, ${this.currentTranslateY}px)`;
        
        const wasOpen = localStorage.getItem('learnway_ai_is_open') === 'true';
        if (wasOpen) {
            this.togglePanel(true);
        }
    }

    saveState() {
        if (this.chatId) localStorage.setItem('learnway_ai_chat_id', this.chatId);
        localStorage.setItem('learnway_ai_pos_x', this.currentTranslateX);
        localStorage.setItem('learnway_ai_pos_y', this.currentTranslateY);
        localStorage.setItem('learnway_ai_is_open', this.isOpen);
    }

    async fetchHistory() {
        try {
            const url = `${this.historyUrlValue}?chatId=${this.chatId}`;
            const response = await fetch(url);
            if (!response.ok) {
                if (response.status === 404) {
                    this.clearStaleChatSession();
                }
                return;
            }
            const data = await response.json();

            if (data.messages && data.messages.length > 0) {
                // Clear welcome message if we have history
                this.messagesTarget.innerHTML = '';
                data.messages.forEach(msg => {
                    this.appendMessage(msg.content, msg.role, true);
                });
            }
        } catch (err) {
            console.error("Failed to fetch history", err);
        }
    }

    onInput() {
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
            this.saveState();
        } else {
            this.togglePanel();
        }
    }

    togglePanel(forceOpen = null) {
        this.isOpen = forceOpen !== null ? forceOpen : !this.isOpen;
        const panel = this.panelTarget;
        const fab = this.fabTarget;

        if (this.isOpen) {
            panel.style.display = 'flex';
            requestAnimationFrame(() => {
                panel.style.opacity = '1';
                panel.style.transform = 'translateY(0) scale(1)';
            });
            fab.classList.add('open');
            if (forceOpen === null) this.inputTarget.focus();
        } else {
            panel.style.opacity = '0';
            panel.style.transform = 'translateY(16px) scale(0.97)';
            setTimeout(() => { panel.style.display = 'none'; }, 200);
            fab.classList.remove('open');
        }
        this.saveState();
    }

    closePanel(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        this.togglePanel(false);
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
            const response = await this.postMessage(message, this.chatId);

            if (!response.ok) {
                const payload = await response.json().catch(() => ({}));

                if (response.status === 404 && this.chatId && (payload.error || '').toLowerCase().includes('chat not found')) {
                    this.clearStaleChatSession();
                    const retryResponse = await this.postMessage(message, null);
                    await this.handleMessageResponse(retryResponse);
                    return;
                }

                this.showTypingIndicator(false);
                this.appendMessage('[ICON:warning] ' + (payload.error || 'Something went wrong.'), 'error');
                return;
            }

            await this.handleMessageResponse(response);
        } catch (err) {
            this.showTypingIndicator(false);
            this.appendMessage('[ICON:warning] Unable to reach Learnway AI. Please check your connection.', 'error');
        } finally {
            this.isTyping = false;
        }
    }

    async postMessage(message, chatId) {
        return fetch(this.chatUrlValue, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message, chatId })
        });
    }

    async handleMessageResponse(response) {
        const data = await response.json();
        this.showTypingIndicator(false);

        if (data.response) {
            this.chatId = data.chatId;
            this.saveState();
            let responseText = data.response;
            
            // Check for [AUTO_SCHEDULE] tag
            if (responseText.includes('[AUTO_SCHEDULE]')) {
                responseText = responseText.replace('[AUTO_SCHEDULE]', '').trim();
                if (responseText) {
                    this.appendMessage(responseText, 'assistant');
                }
                this.triggerAutoSchedule();
                return;
            }

            // Check for [SCHEDULE_CHANGE_REQUEST] tag
            const requestMatch = responseText.match(/\[SCHEDULE_CHANGE_REQUEST:([^\]]+)\]/);
            if (requestMatch) {
                const requestData = JSON.parse(requestMatch[1]);
                responseText = responseText.replace(requestMatch[0], '').trim();
                if (responseText) {
                    this.appendMessage(responseText, 'assistant');
                }
                this.submitScheduleChangeRequest(requestData);
                return;
            }

            // Check for [REDIRECT:/path] tag
            const redirectMatch = responseText.match(/\[REDIRECT:([^\]]+)\]/);
            if (redirectMatch) {
                const redirectPath = redirectMatch[1].trim();
                responseText = responseText.replace(redirectMatch[0], '').trim();
                
                if (responseText) {
                    this.appendMessage(responseText, 'assistant');
                }
                setTimeout(() => { window.location.href = redirectPath; }, 1500);
            } else {
                this.appendMessage(responseText, 'assistant');
            }
        } else {
            this.appendMessage('[ICON:warning] ' + (data.error || 'Something went wrong.'), 'error');
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

    async triggerAutoSchedule() {
        if (!this.autoScheduleUrlValue) {
            this.appendMessage("[ICON:warning] You don't have permission to perform this action.", 'assistant');
            return;
        }

        this.appendMessage("[ICON:settings] **Learnway Copilot:** I am now calling the Google OR-Tools optimization engine to generate your global schedule. Please wait a few seconds...", 'assistant');
        this.showTypingIndicator(true);

        try {
            const response = await fetch(this.autoScheduleUrlValue, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            });
            const data = await response.json();

            this.showTypingIndicator(false);

            if (data.success) {
                this.appendMessage("[ICON:check_circle] **Optimization Complete!** I have successfully generated the global schedule without any conflicts. Would you like to view it now? [REDIRECT:/admin/schedule]", 'assistant');
            } else {
                this.appendMessage("[ICON:error] **Optimization Failed:** " + (data.error || "The solver couldn't find a valid solution with current constraints."), 'assistant');
            }
        } catch (err) {
            this.showTypingIndicator(false);
            this.appendMessage("[ICON:warning] Failed to connect to the scheduling service.", 'error');
        }
    }

    async submitScheduleChangeRequest(data) {
        if (!this.requestChangeUrlValue) {
            this.appendMessage("[ICON:warning] You don't have permission to request schedule changes.", 'assistant');
            return;
        }

        this.showTypingIndicator(true);

        try {
            const response = await fetch(this.requestChangeUrlValue, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();

            this.showTypingIndicator(false);

            if (result.success) {
                this.appendMessage("[ICON:check_circle] **Request Submitted!** I have forwarded your schedule change request to the administrator. You will be notified once it has been reviewed.", 'assistant');
            } else {
                this.appendMessage("[ICON:error] **Failed to submit request:** " + (result.error || "Unknown error"), 'assistant');
            }
        } catch (err) {
            this.showTypingIndicator(false);
            this.appendMessage("[ICON:warning] Failed to connect to the request service.", 'error');
        }
    }

    appendMessage(text, role, skipAnimation = false) {
        if (role === 'assistant') {
            text = text.replace(/\[REDIRECT:[^\]]+\]/g, '').trim();
            if (!text) return;
        }

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

        if (skipAnimation) {
            wrap.classList.add('visible');
        } else {
            requestAnimationFrame(() => wrap.classList.add('visible'));
        }
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
        if (!text) return '';
        return text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    scrollToBottom() {
        this.messagesTarget.scrollTo({ top: this.messagesTarget.scrollHeight, behavior: 'smooth' });
    }

    clearChat() {
        this.chatId = null;
        localStorage.removeItem('learnway_ai_chat_id');
        this.messagesTarget.innerHTML = '';
        this.suggestionsTarget.style.display = 'flex';
        this.suggestionsTarget.style.opacity = '1';
        this.renderSuggestions();
        this.appendMessage("Chat cleared! How can I help you?", 'assistant');
    }

    clearStaleChatSession() {
        this.chatId = null;
        localStorage.removeItem('learnway_ai_chat_id');
    }
}
