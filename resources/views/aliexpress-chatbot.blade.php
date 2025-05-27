<x-app-layout>
    <div class="aliexpress-chat-container">
        <!-- Chat History Sidebar -->
        <div class="chat-history-sidebar" id="chat-history-sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-history"></i> Chat History</h3>
                <button class="sidebar-close-btn" id="sidebar-close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="chat-history-list" id="chat-history-list">
                <div class="empty-history-message">
                    <i class="fas fa-comments"></i>
                    <p>No chat history yet</p>
                </div>
            </div>
            <div class="sidebar-footer">
                <button class="clear-history-btn" id="clear-history-btn">
                    <i class="fas fa-trash"></i> Clear All History
                </button>
            </div>
        </div>

        <div class="aliexpress-content full-width" id="aliexpress-content">
            <div class="aliexpress-chatbot-container">
                <div class="aliexpress-chat-content">
                    <div class="chat-box">
                        <!-- Chat header with history toggle -->
                        <div class="chat-header">
                            <button id="toggle-history-btn" class="toggle-history-btn">
                                <i class="fas fa-history"></i>
                            </button>
                            <h2>AliExpress Shopping Assistant</h2>
                            <button id="new-chat-btn" class="new-chat-btn">
                                <i class="fas fa-plus"></i> New Chat
                            </button>
                        </div>

                        <div class="chat-messages custom-scrollbar" id="aliexpress-chat-messages">
                            <!-- Welcome message will be added dynamically by JavaScript -->
                            <button id="scroll-to-bottom" class="scroll-to-bottom-btn hidden">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                        </div>

                        <div class="chat-form-container">
                            <div class="chat-form">
                                <form id="aliexpress-chat-form">
                                    <div class="input-group">
                                        <textarea id="aliexpress-user-input" placeholder="Describe what you're looking for..." rows="1" required></textarea>
                                        <div class="input-actions">
                                            <button type="submit" class="send-button" id="aliexpress-send-button">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-status-indicator"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/aliexpress-products.css') }}">
    <style>
        /* Notification styles */
        .chat-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            z-index: 9999;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .chat-notification.show {
            transform: translateY(0);
            opacity: 1;
        }

        .chat-notification.error {
            background: #d32f2f;
            border-left: 5px solid #b71c1c;
        }

        .chat-notification.success {
            background: #388e3c;
            border-left: 5px solid #1b5e20;
        }

        .notification-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notification-content i {
            font-size: 18px;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('js/aliexpress-chatbot.js') }}?v={{ time() }}"></script>
    @endpush
</x-app-layout>
