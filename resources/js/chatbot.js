function recordProductView(productId, event) {
    // Get CSRF token
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    console.log('Recording view for product ID:', productId);

    // Using the correct API endpoint with the productId in the URL
    fetch(`/api/products/view/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
    })
    .then(response => {
        if (response.ok) {
            console.log('Product view recorded successfully');
            return response.json();
        } else {
            console.error('Failed to record product view:', response.status);
            return response.text().then(text => {
                throw new Error(text);
            });
        }
    })
    .then(data => {
        console.log('Product view response:', data);
    })
    .catch(error => {
        console.error('Error recording product view:', error);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const chatMessages = document.getElementById('chat-messages');
    const userInput = document.getElementById('user-input');
    const maxPrice = document.getElementById('max-price');
    const sendButton = document.getElementById('send-button');
    const sessionsList = document.getElementById('sessions-list');
    const newChatBtn = document.getElementById('new-chat-btn');
    const modal = document.getElementById('rename-modal');
    const closeModal = document.querySelector('.close-modal');
    const sessionNameInput = document.getElementById('session-name-input');
    const sessionIdInput = document.getElementById('session-id-input');
    const renameSessionBtn = document.getElementById('rename-session-btn');
    const closeSidebarBtn = document.getElementById('close-sidebar-btn');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('chat-sidebar');

    // State variables
    let activeSessionId = null;

    // Load sessions when page loads
    loadChatSessions();

    // Auto-resize textarea
    userInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight < 120 ? this.scrollHeight : 120) + 'px';
    });

    // Send message on button click or enter key (shift+enter for new line)
    sendButton.addEventListener('click', sendMessage);
    userInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // New chat button
    newChatBtn.addEventListener('click', function() {
        clearChat();
        activeSessionId = null;
        // Remove active class from all sessions
        const sessionItems = document.querySelectorAll('.session-item');
        sessionItems.forEach(item => item.classList.remove('active'));
    });

    // Modal close button
    closeModal.addEventListener('click', function() {
        modal.classList.remove('show');
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.remove('show');
        }
    });

    // Rename session button
    renameSessionBtn.addEventListener('click', function() {
        const sessionId = sessionIdInput.value;
        const newName = sessionNameInput.value.trim();

        if (newName) {
            renameSession(sessionId, newName);
            modal.classList.remove('show');
        }
    });

    // Sidebar toggle button functionality
    sidebarToggle.addEventListener('click', function() {
        // For mobile view
        if (window.innerWidth <= 992) {
            sidebar.classList.toggle('expanded');
            localStorage.setItem('sidebarExpanded', sidebar.classList.contains('expanded'));
        }
        // For desktop view
        else {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }
    });

    // Close sidebar button
    closeSidebarBtn.addEventListener('click', function() {
        // Close sidebar on mobile
        if (window.innerWidth <= 992) {
            sidebar.classList.remove('expanded');
            localStorage.setItem('sidebarExpanded', false);
        }
        // Collapse sidebar on desktop
        else {
            sidebar.classList.add('collapsed');
            localStorage.setItem('sidebarCollapsed', true);
        }
    });

    // Initialize sidebar based on screen size and localStorage
    function initializeSidebar() {
        if (window.innerWidth <= 992) {
            // Mobile: Check if expanded state is stored
            const isExpanded = localStorage.getItem('sidebarExpanded') === 'true';
            if (isExpanded) {
                sidebar.classList.add('expanded');
            } else {
                sidebar.classList.remove('expanded');
            }
        } else {
            // Desktop: Check if collapsed state is stored
            // If no value in localStorage yet, default to collapsed (closed)
            const isCollapsed = localStorage.getItem('sidebarCollapsed');
            if (isCollapsed === null) {
                // First time visit - set to collapsed by default
                sidebar.classList.add('collapsed');
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                // Use stored preference
                if (isCollapsed === 'true') {
                    sidebar.classList.add('collapsed');
                } else {
                    sidebar.classList.remove('collapsed');
                }
            }
        }
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        initializeSidebar();
    });

    // Initialize sidebar on page load
    initializeSidebar();

    function sendMessage() {
        const keywords = userInput.value.trim();
        const price = maxPrice.value.trim();

        if (keywords) {
            // Add user message to chat
            addUserMessage(keywords);
            console.log('User message:', keywords);

            // Clear input and reset height
            userInput.value = '';
            userInput.style.height = 'auto';

            // Show typing indicator
            showTypingIndicator();

            // If no active session, create one before proceeding
            if (!activeSessionId) {
                createChatSession(keywords)
                    .then(sessionId => {
                        // Save user message
                        saveMessage(sessionId, 'user', keywords);

                        // Get recommendations from API
                        fetchRecommendations(keywords, price, sessionId);
                    })
                    .catch(error => {
                        console.error('Error creating chat session:', error);
                        hideTypingIndicator();
                        addBotMessage('Sorry, there was an error creating a chat session. Error: ' + (error.message || 'Unknown error'));
                    });
            } else {
                // Save user message to existing session
                saveMessage(activeSessionId, 'user', keywords);

                // Get recommendations from API
                fetchRecommendations(keywords, price, activeSessionId);
            }
        }
    }

    function addUserMessage(content) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message user';
        messageDiv.innerHTML = `
            <div class="content">${content}</div>
            <div class="avatar">
                <i class="fas fa-user"></i>
            </div>
        `;
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    function addBotMessage(content) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message bot';
        messageDiv.innerHTML = `
            <div class="avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="content">
                ${content}
            </div>
        `;
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot';
        typingDiv.id = 'typing-indicator';
        typingDiv.innerHTML = `
            <div class="avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="content typing">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        `;
        chatMessages.appendChild(typingDiv);
        scrollToBottom();
    }

    function hideTypingIndicator() {
        const typingIndicator = document.getElementById('typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    function addProductMessage(products, sessionId, aiMessage) {
        if (products.length === 0) {
            const noResultsMessage = "Sorry, I couldn't find any products matching your criteria.";
            addBotMessage(noResultsMessage);

            // Save bot message
            if (sessionId) {
                saveMessage(sessionId, 'bot', noResultsMessage);
            }
            return;
        }

        // Combine AI message and product recommendations in a single message
        let content = '';

        // Add the AI message directly without the ai-chat-message wrapper
        if (aiMessage && aiMessage.trim()) {
            content += aiMessage;
        }

        // Add product grid right after the AI message
        content += `<div class="product-grid" id="product-grid">`;

        // Only show the first 3 products initially
        const initialProductsToShow = Math.min(3, products.length);

        products.forEach((item, index) => {
            const product = item.product;
            const reason = item.reason;
            const isHidden = index >= initialProductsToShow;

            // Default image if none provided
            const imageUrl = product.imgUrl || 'https://via.placeholder.com/150?text=No+Image';

            content += `
                <div class="product-card${isHidden ? ' hidden-product' : ''}" data-product-index="${index}">
                    <img src="${imageUrl}" alt="${product.title}" class="product-image">
                    <div class="product-name">${product.title}</div>
                    <div>
                        <span class="product-price">${product.price} USD</span>
                        ${product.listPrice && parseFloat(product.listPrice) > parseFloat(product.price) ?
                          `<span class="original-price">${product.listPrice} USD</span>` : ''}
                    </div>
                    <div class="product-ratings">
                        ${'★'.repeat(Math.floor(product.stars || 0))}${product.stars % 1 >= 0.5 ? '½' : ''}${'☆'.repeat(5 - Math.ceil(product.stars || 0))}
                        <span class="text-secondary">(${product.reviews || 0})</span>
                    </div>
                    <div class="mt-1 mb-1">
                        ${product.category ? `<span class="badge small-badge">${product.category.category_name}</span>` : ''}
                        ${product.isBestSeller ? `<span class="badge small-badge">Best Seller</span>` : ''}
                    </div>
                    <div class="product-actions">
                        ${product.productURL ? `
                            <a href="${product.productURL}" target="_blank" class="product-link view-btn" data-product-id="${product.id}" onclick="recordProductView(${product.id}, event)">
                                <i class="fas fa-shopping-cart me-1"></i> View
                            </a>
                        ` : ''}
                        <button id="save-btn-${product.id}" class="product-link save-btn" data-product-id="${product.id}" onclick="handleSaveButtonClick(${product.id}, this)">
                            <i class="far fa-bookmark me-1"></i> Save
                        </button>
                    </div>
                    <p class="reason">${reason}</p>
                </div>
            `;
        });

        content += `</div>`;

        // Add "Show All" button if there are more than 3 products
        if (products.length > 3) {
            content += `
                <div class="show-all-container">
                    <button id="show-all-products-btn" class="show-all-btn" onclick="showAllProducts(this)">
                        Show All Products (${products.length - 3} more)
                    </button>
                </div>
            `;
        }

        // Add the combined content as a single bot message
        addBotMessage(content);

        // Save the combined message
        if (sessionId) {
            saveMessage(sessionId, 'bot', content);
        }

        // Check saved status for all product buttons after they're added to DOM
        setTimeout(() => {
            const saveButtons = document.querySelectorAll('.save-btn');
            saveButtons.forEach(button => {
                const productId = button.dataset.productId;
                if (productId) {
                    checkIfProductSaved(productId, button);
                }
            });
        }, 100);
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function clearChat() {
        // Remove all messages except the first welcome message
        while (chatMessages.children.length > 1) {
            chatMessages.removeChild(chatMessages.lastChild);
        }
    }

    function loadChatSessions() {
        fetch('/api/chat/sessions', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include'  // Add this to ensure cookies are sent
        })
        .then(response => {
            if (response.status === 401) {
                // User is not authenticated, handle accordingly
                console.log('User is not authenticated');
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.sessions) {
                // Clear loading indicator
                sessionsList.innerHTML = '';

                if (data.sessions.length === 0) {
                    sessionsList.innerHTML = '<div class="empty-sessions">No conversations yet</div>';
                    return;
                }

                // Add sessions to sidebar
                data.sessions.forEach(session => {
                    addSessionToSidebar(session);
                });
            }
        })
        .catch(error => {
            console.error('Error loading chat sessions:', error);
            sessionsList.innerHTML = '<div class="empty-sessions">Error loading conversations</div>';
        });
    }

    function addSessionToSidebar(session) {
        const date = new Date(session.created_at);
        const formattedDate = date.toLocaleDateString();
        const formattedTime = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        const sessionDiv = document.createElement('div');
        sessionDiv.className = 'session-item';
        sessionDiv.dataset.id = session.id;
        sessionDiv.innerHTML = `
            <div>
                <div class="session-name">${session.name}</div>
                <div class="session-date">${formattedDate} at ${formattedTime}</div>
            </div>
            <div class="session-actions">
                <button class="session-action-btn rename-btn" title="Rename">
                    <i class="fas fa-pen"></i>
                </button>
                <button class="session-action-btn delete-btn" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        // Add click event for loading session
        sessionDiv.addEventListener('click', function(e) {
            if (!e.target.closest('.session-action-btn')) {
                // Set as active session
                document.querySelectorAll('.session-item').forEach(item => {
                    item.classList.remove('active');
                });
                sessionDiv.classList.add('active');

                // Load session messages
                loadSessionMessages(session.id);
            }
        });

        // Add rename button click event
        const renameBtn = sessionDiv.querySelector('.rename-btn');
        renameBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            openRenameModal(session.id, session.name);
        });

        // Add delete button click event
        const deleteBtn = sessionDiv.querySelector('.delete-btn');
        deleteBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            openDeleteModal(session.id);
        });

        sessionsList.appendChild(sessionDiv);
    }

    function loadSessionMessages(sessionId) {
        // Set the active session
        activeSessionId = sessionId;

        // Clear current chat
        clearChat();

        // Close sidebar on small screens when entering a chat
        if (window.innerWidth <= 992) {
            document.getElementById('chat-sidebar').classList.remove('expanded');
        }

        // Show loading indicator
        showTypingIndicator();

        // Fetch session messages
        fetch(`/api/chat/${sessionId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            // Hide loading indicator
            hideTypingIndicator();

            if (data.messages && data.messages.length > 0) {
                // Add messages to chat
                data.messages.forEach(message => {
                    if (message.sender === 'user') {
                        addUserMessage(message.content);
                    } else {
                        addBotMessage(message.content);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading session messages:', error);
            hideTypingIndicator();
            addBotMessage('Sorry, there was an error loading the conversation.');
        });
    }

    function createChatSession(firstMessage) {
        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        return new Promise((resolve, reject) => {
            fetch('/api/chat/sessions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin', // Use same-origin instead of include
                body: JSON.stringify({
                    name: firstMessage.substring(0, 50) + (firstMessage.length > 50 ? '...' : '')
                })
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 401) {
                        // Handle authentication issues specifically
                        throw new Error('Authentication required. Please make sure you are logged in.');
                    }
                    return response.json().then(errData => {
                        throw new Error(JSON.stringify(errData));
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.session) {
                    // Add new session to sidebar
                    addSessionToSidebar(data.session);

                    // Activate the session
                    const sessionDiv = document.querySelector(`.session-item[data-id="${data.session.id}"]`);
                    if (sessionDiv) {
                        sessionDiv.classList.add('active');
                    }

                    // Set as active session
                    activeSessionId = data.session.id;

                    resolve(data.session.id);
                } else {
                    reject('Failed to create session: ' + JSON.stringify(data));
                }
            })
            .catch(error => {
                console.error('Error creating chat session:', error);
                hideTypingIndicator();

                // Check if it's an authentication error
                if (error.message && error.message.includes('Authentication required')) {
                    addBotMessage('⚠️ You need to be logged in to save conversations. Please <a href="/login">log in</a> or <a href="/register">register</a> to continue.');
                } else {
                    addBotMessage('Sorry, there was an error creating a chat session. Error: ' + (error.message || 'Unknown error'));
                }

                reject(error);
            });
        });
    }

    function saveMessage(sessionId, sender, content) {
        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/api/chat/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                session_id: sessionId,
                sender: sender,
                content: content
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Message saved successfully:', data);
        })
        .catch(error => {
            console.error('Error saving message:', error);
            addBotMessage('⚠️ Note: Your message was displayed but could not be saved. You may need to log in again.');
        });
    }

    function deleteSession(sessionId) {
        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/api/chat/${sessionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            // Remove session from sidebar
            const sessionDiv = document.querySelector(`.session-item[data-id="${sessionId}"]`);
            if (sessionDiv) {
                sessionDiv.remove();
            }

            // If this was the active session, clear chat
            if (activeSessionId === sessionId) {
                clearChat();
                activeSessionId = null;
            }
        })
        .catch(error => {
            console.error('Error deleting session:', error);
        });
    }

    function renameSession(sessionId, newName) {
        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/api/chat/${sessionId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include',
            body: JSON.stringify({
                name: newName
            })
        })
        .then(response => response.json())
        .then(data => {
            // Update session name in sidebar
            const sessionDiv = document.querySelector(`.session-item[data-id="${sessionId}"]`);
            if (sessionDiv) {
                const nameElement = sessionDiv.querySelector('.session-name');
                if (nameElement) {
                    nameElement.textContent = newName;
                }
            }
        })
        .catch(error => {
            console.error('Error renaming session:', error);
        });
    }

    function openRenameModal(sessionId, currentName) {
        sessionIdInput.value = sessionId;
        sessionNameInput.value = currentName;
        modal.classList.add('show');
        sessionNameInput.focus();
    }

    function openDeleteModal(sessionId) {
        const deleteModal = document.getElementById('delete-modal');
        const closeDeleteModal = document.querySelector('.close-delete-modal');
        const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
        const cancelDeleteBtn = document.getElementById('cancel-delete-btn');

        // Set the session ID to be deleted
        document.getElementById('delete-session-id').value = sessionId;

        // Show the delete modal
        deleteModal.classList.add('show');

        // Close delete modal
        closeDeleteModal.onclick = function() {
            deleteModal.classList.remove('show');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == deleteModal) {
                deleteModal.classList.remove('show');
            }
        }

        // Confirm delete action
        confirmDeleteBtn.onclick = function() {
            const sessionId = document.getElementById('delete-session-id').value;
            deleteSession(sessionId);
            deleteModal.classList.remove('show');
        }

        // Cancel delete action
        cancelDeleteBtn.onclick = function() {
            deleteModal.classList.remove('show');
        }
    }

    function fetchRecommendations(keywords, maxPrice, sessionId) {
        // Prepare request data
        const data = {
            keywords: keywords
        };

        if (maxPrice) {
            data.max_price = maxPrice;
        }

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Make API request
        fetch('/api/recommend-products', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            // Hide typing indicator
            hideTypingIndicator();

            if (data.error) {
                const errorMessage = 'Sorry, there was an error: ' + data.error;
                addBotMessage(errorMessage);

                // Save bot error message
                if (sessionId) {
                    saveMessage(sessionId, 'bot', errorMessage);
                }
            } else {
                // Show extracted keywords first if available
                if (data.extracted_keywords && data.extracted_keywords.length > 0) {
                    const keywordsMessage = `
                        <div class="extracted-keywords">
                            <p><strong>I searched for:</strong> ${data.extracted_keywords.map(kw =>
                                `<span class="keyword-badge">${kw}</span>`).join(' ')}
                            </p>
                        </div>
                    `;
                    addBotMessage(keywordsMessage);

                    // Save keyword message
                    if (sessionId) {
                        saveMessage(sessionId, 'bot', keywordsMessage);
                    }
                }

                // Add product recommendations to chat with AI message
                addProductMessage(data.recommendations, sessionId, data.message);
            }
        })
        .catch(error => {
            // Hide typing indicator
            hideTypingIndicator();
            const errorMessage = 'Sorry, there was an error connecting to the server.';
            addBotMessage(errorMessage);

            // Save bot error message
            if (sessionId) {
                saveMessage(sessionId, 'bot', errorMessage);
            }
            console.error('Error:', error);
        });
    }
});
