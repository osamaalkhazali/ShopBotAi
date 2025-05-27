document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const chatMessages = document.getElementById('aliexpress-chat-messages');
    const userInput = document.getElementById('aliexpress-user-input');
    const sendButton = document.getElementById('aliexpress-send-button');
    const chatForm = document.getElementById('aliexpress-chat-form');
    const newChatBtn = document.getElementById('new-chat-btn');
    const scrollToBottomBtn = document.getElementById('scroll-to-bottom');
    const toggleHistoryBtn = document.getElementById('toggle-history-btn');
    const chatHistorySidebar = document.getElementById('chat-history-sidebar');
    const sidebarCloseBtn = document.getElementById('sidebar-close-btn');
    const chatHistoryList = document.getElementById('chat-history-list');
    const clearHistoryBtn = document.getElementById('clear-history-btn');

    // State variables
    let isWaitingForResponse = false;
    let searchHistory = [];
    let currentSearchQuery = null;
    let userHasScrolled = false;
    let activeSessionId = null;

    // Add scroll event listener to chat messages
    chatMessages.addEventListener('scroll', function() {
        const isAtBottom = chatMessages.scrollHeight - chatMessages.clientHeight <= chatMessages.scrollTop + 100;

        if (!isAtBottom) {
            scrollToBottomBtn.classList.remove('hidden');
            scrollToBottomBtn.classList.add('visible');
            userHasScrolled = true;
        } else {
            scrollToBottomBtn.classList.remove('visible');
            scrollToBottomBtn.classList.add('hidden');
            userHasScrolled = false;
        }
    });

    scrollToBottomBtn.addEventListener('click', function() {
        chatMessages.scrollTo({
            top: chatMessages.scrollHeight,
            behavior: 'smooth'
        });
    });

    userInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight < 120 ? this.scrollHeight : 120) + 'px';

        if (this.value.trim().length > 0) {
            document.querySelector('.chat-form-container').classList.add('typing');
            this.classList.add('typing');
        } else {
            document.querySelector('.chat-form-container').classList.remove('typing');
            this.classList.remove('typing');
        }
    });

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (userInput.value.trim() !== '' && !isWaitingForResponse) {
            sendMessage(userInput.value.trim());
            userInput.value = '';
            userInput.style.height = 'auto';
            userInput.classList.remove('typing');
            document.querySelector('.chat-form-container').classList.remove('typing');
        }
    });

    newChatBtn.addEventListener('click', function() {
        startNewChat();
    });

    toggleHistoryBtn.addEventListener('click', function() {
        toggleHistorySidebar();
    });

    sidebarCloseBtn.addEventListener('click', function() {
        closeHistorySidebar();
    });

    clearHistoryBtn.addEventListener('click', function() {
        showConfirmDialog(
            'Clear All Chat History',
            'Are you sure you want to delete all chat history? This action cannot be undone.',
            function() {
                clearAllHistory();
            }
        );
    });

    window.suggestQuery = function(query) {
        userInput.value = query;
        chatForm.dispatchEvent(new Event('submit'));
    };

    function sendMessage(message) {
        message = message.trim();
        if (message === '') return;

        currentSearchQuery = message;
        addToSearchHistory(message);
        addUserMessage(message);
        showTypingIndicator();

        if (!activeSessionId) {
            createChatSession(message)
                .then(sessionId => {
                    activeSessionId = sessionId;
                    saveMessage(sessionId, 'user', message);
                    fetchAliExpressRecommendations(message);
                })
                .catch(error => {
                    hideTypingIndicator();
                    addBotMessage('Sorry, there was an error creating a chat session. Please try again later.');
                });
        } else {
            saveMessage(activeSessionId, 'user', message);
            fetchAliExpressRecommendations(message);
        }
    }

    function fetchAliExpressRecommendations(message) {
        isWaitingForResponse = true;

        fetch('/api/aliexpress/recommend', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                query: message
            }),
        })
        .then(response => response.json())
        .then(data => {
            hideTypingIndicator();

            if (data.success && data.data) {
                const friendlyMessage = data.data.friendly_message || '';
                addBotMessage(friendlyMessage, true);
                if (activeSessionId) {
                    saveMessage(activeSessionId, 'bot', friendlyMessage);
                }
                const skeletonId = showProductSkeletons();
                fetchProducts(message, skeletonId);
            } else {
                addBotMessage("Sorry, I encountered an error while processing your request. Please try again.", true);
                isWaitingForResponse = false;
            }
        })
        .catch(error => {
            hideTypingIndicator();
            addBotMessage("Sorry, I encountered an error while processing your request. Please try again.", true);
            isWaitingForResponse = false;
        });
    }

    function fetchProducts(message, skeletonId) {
        fetch('/api/aliexpress/fetch-products', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                query: message
            }),
        })
        .then(response => response.json())
        .then(productData => {
            removeProductSkeletons(skeletonId);

            if (productData.success && productData.data) {
                const products = productData.data.products || [];
                if (products.length > 0) {
                    appendProductResults(products);
                } else {
                    const noProductsMessage = `I couldn't find any products matching "${message}". Please try a different search.`;
                    addBotMessage(noProductsMessage, true);
                }
            } else {
                const errorMessage = "Sorry, I encountered an error while searching for products. Please try again.";
                addBotMessage(errorMessage, true);
            }

            isWaitingForResponse = false;
        })
        .catch(error => {
            removeProductSkeletons(skeletonId);
            const errorMessage = "Sorry, I encountered an error while searching for products. Please try again.";
            addBotMessage(errorMessage, true);
            isWaitingForResponse = false;
        });
    }

    function addUserMessage(content, useAnimation = true) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message user';

        if (useAnimation) {
            messageDiv.style.opacity = '0';
            messageDiv.style.transform = 'translateY(10px)';
        }

        messageDiv.innerHTML = `
            <div class="content">${content}</div>
            <div class="avatar">
                <i class="fas fa-user"></i>
            </div>
        `;

        chatMessages.appendChild(messageDiv);

        if (useAnimation) {
            setTimeout(() => {
                messageDiv.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                messageDiv.style.opacity = '1';
                messageDiv.style.transform = 'translateY(0)';
                scrollToBottom();
            }, 50);
        } else {
            scrollToBottom();
        }
    }

    function addBotMessage(content, useTypingEffect = false, useAnimation = true) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message bot';

        if (useAnimation) {
            messageDiv.style.opacity = '0';
            messageDiv.style.transform = 'translateY(10px)';
        }

        const avatarDiv = document.createElement('div');
        avatarDiv.className = 'avatar';
        avatarDiv.innerHTML = '<i class="fas fa-robot"></i>';

        const contentDiv = document.createElement('div');
        contentDiv.className = 'content';

        messageDiv.appendChild(avatarDiv);
        messageDiv.appendChild(contentDiv);

        chatMessages.appendChild(messageDiv);

        if (useAnimation) {
            setTimeout(() => {
                messageDiv.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                messageDiv.style.opacity = '1';
                messageDiv.style.transform = 'translateY(0)';
                scrollToBottom();
            }, 50);
        } else {
            scrollToBottom();
        }

        if (useTypingEffect) {
            content = formatMessageContent(content);
            contentDiv.classList.add('typing-content');
            let textToType = content;
            let currentHtml = '';
            let inTag = false;
            let i = 0;
            let speed = 20;
            let lastChar = '';

            function typeWriter() {
                if (i < textToType.length) {
                    const char = textToType.charAt(i);

                    if (char === '<') {
                        inTag = true;
                    }

                    if (inTag) {
                        currentHtml += char;
                        if (char === '>') {
                            inTag = false;
                        }
                    } else {
                        if (char === '.' || char === '!' || char === '?') {
                            speed = 300;
                        } else if (char === ',' || char === ';') {
                            speed = 150;
                        } else {
                            speed = 20 + Math.random() * 20;
                        }

                        currentHtml += char;
                        lastChar = char;
                    }

                    contentDiv.innerHTML = currentHtml + '<span class="typing-cursor">|</span>';
                    i++;
                    scrollToBottom();
                    setTimeout(typeWriter, speed);
                } else {
                    contentDiv.innerHTML = currentHtml;
                    isWaitingForResponse = false;
                }
            }

            typeWriter();
        } else {
            contentDiv.innerHTML = formatMessageContent(content);
        }
    }

    function formatMessageContent(content) {
        content = content.replace(/^•\s+(.*)/gm, '<div class="bullet-line"><span class="bullet-point">•</span><span>$1</span></div>');
        content = content.replace(/\n•\s+(.*)/gm, '</p><div class="bullet-line"><span class="bullet-point">•</span><span>$1</span></div><p>');
        content = content.replace(/• /g, '<span class="bullet-point">•</span> ');
        content = content.replace(
            /(https?:\/\/[^\s]+)/g,
            '<a href="$1" target="_blank" class="message-link">$1</a>'
        );
        content = content.replace(/\n\n/g, '</p><p>');
        content = content.replace(/\n(?!\<div)/g, '<br>');
        if (!content.startsWith('<p>') && !content.startsWith('<div class="bullet-line">')) {
            content = '<p>' + content + '</p>';
        }
        return content;
    }

    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot thinking-indicator';
        typingDiv.id = 'typing-indicator';
        typingDiv.innerHTML = `
            <div class="avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="thinking-content">
                <div class="thinking-text">ShopBot Thinking...</div>
                <div class="dots-container">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
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

    function appendProductResults(products, saveToHistory = true) {
        const resultsDiv = document.createElement('div');
        resultsDiv.classList.add('product-results');

        const productsGrid = document.createElement('div');
        productsGrid.classList.add('products-grid');

        products.forEach((product, index) => {
            const card = document.createElement('div');
            card.classList.add('product-card');
            card.style.animationDelay = `${index * 0.05}s`;
            card.setAttribute('data-product-id', product.product_id || '');

            const imageContainer = document.createElement('div');
            imageContainer.classList.add('product-image');
            const image = document.createElement('img');
            image.src = product.product_main_image_url || 'https://via.placeholder.com/150?text=No+Image';
            image.alt = product.product_title || 'Product image';
            image.referrerPolicy = 'no-referrer';
            image.loading = "lazy";
            imageContainer.appendChild(image);
            card.appendChild(imageContainer);

            const infoDiv = document.createElement('div');
            infoDiv.classList.add('product-info');

            const title = document.createElement('h4');
            title.classList.add('product-title');
            title.textContent = product.product_title || 'Unknown product';
            infoDiv.appendChild(title);

            const priceDiv = document.createElement('div');
            priceDiv.classList.add('product-price');
            const price = document.createElement('span');
            price.classList.add('price');
            price.textContent = `$${product.target_sale_price || '0.00'}`;
            priceDiv.appendChild(price);

            if (product.target_original_price && product.target_original_price > product.target_sale_price) {
                const originalPrice = document.createElement('span');
                originalPrice.classList.add('original-price');
                originalPrice.textContent = `$${product.target_original_price}`;
                priceDiv.appendChild(originalPrice);
            }

            infoDiv.appendChild(priceDiv);

            const actionsDiv = document.createElement('div');
            actionsDiv.classList.add('product-actions');

            const viewButton = document.createElement('a');
            viewButton.classList.add('view-product', 'view-btn');
            viewButton.href = product.promotion_link || product.product_detail_url || '#';
            viewButton.target = '_blank';
            viewButton.setAttribute('data-product-id', product.product_id || '');
            viewButton.innerHTML = '<i class="fas fa-external-link-alt"></i> View Product';

            viewButton.addEventListener('click', function(event) {
                const productId = String(product.product_id || '');
                recordAliExpressProductView(productId, event, product);
            });

            actionsDiv.appendChild(viewButton);

            const saveButton = document.createElement('button');
            saveButton.classList.add('save-product', 'save-btn');
            saveButton.setAttribute('data-product-id', product.product_id || '');
            saveButton.innerHTML = '<i class="far fa-bookmark"></i> Save';
            saveButton.id = `aliexpress-save-btn-${product.product_id}`;

            saveButton.addEventListener('click', function() {
                const productId = String(product.product_id || '');
                handleAliExpressSaveButtonClick(productId, this, product);
            });

            actionsDiv.appendChild(saveButton);
            infoDiv.appendChild(actionsDiv);

            card.appendChild(infoDiv);
            productsGrid.appendChild(card);
        });

        resultsDiv.appendChild(productsGrid);
        chatMessages.appendChild(resultsDiv);

        if (activeSessionId && saveToHistory) {
            const productsToSave = products.map(product => ({
                product_id: product.product_id,
                product_title: product.product_title,
                product_main_image_url: product.product_main_image_url,
                target_sale_price: product.target_sale_price,
                target_original_price: product.target_original_price,
                product_detail_url: product.product_detail_url || product.promotion_link,
            }));

            const productContent = JSON.stringify({
                type: 'product_grid',
                products: productsToSave
            });

            saveMessage(activeSessionId, 'bot', productContent);
        }

        setTimeout(() => {
            products.forEach(product => {
                if (product.product_id) {
                    const saveButton = document.getElementById(`aliexpress-save-btn-${product.product_id}`);
                    if (saveButton) {
                        checkIfAliExpressProductSaved(product.product_id, saveButton);
                    }
                }
            });
        }, 500);

        scrollToBottom();
    }

    function showProductSkeletons() {
        const skeletonContainerId = 'skeleton-products-' + Date.now();
        const skeletonContainer = document.createElement('div');
        skeletonContainer.classList.add('product-results', 'skeleton-loading');
        skeletonContainer.id = skeletonContainerId;

        const skeletonGrid = document.createElement('div');
        skeletonGrid.classList.add('products-grid');

        for (let i = 0; i < 6; i++) {
            const skeletonCard = document.createElement('div');
            skeletonCard.classList.add('product-card', 'skeleton-card');
            skeletonCard.style.animationDelay = `${i * 0.1}s`;

            const skeletonImage = document.createElement('div');
            skeletonImage.classList.add('product-image', 'skeleton-image');
            skeletonCard.appendChild(skeletonImage);

            const skeletonInfo = document.createElement('div');
            skeletonInfo.classList.add('product-info');

            const skeletonTitle = document.createElement('div');
            skeletonTitle.classList.add('skeleton-title');
            const titleLine1 = document.createElement('div');
            titleLine1.classList.add('skeleton-line', 'skeleton-line-long');
            const titleLine2 = document.createElement('div');
            titleLine2.classList.add('skeleton-line', 'skeleton-line-medium');
            skeletonTitle.appendChild(titleLine1);
            skeletonTitle.appendChild(titleLine2);
            skeletonInfo.appendChild(skeletonTitle);

            const skeletonPrice = document.createElement('div');
            skeletonPrice.classList.add('skeleton-line', 'skeleton-line-short', 'skeleton-price');
            skeletonInfo.appendChild(skeletonPrice);

            const skeletonButton = document.createElement('div');
            skeletonButton.classList.add('skeleton-button');
            skeletonInfo.appendChild(skeletonButton);

            skeletonCard.appendChild(skeletonInfo);
            skeletonGrid.appendChild(skeletonCard);
        }

        skeletonContainer.appendChild(skeletonGrid);
        chatMessages.appendChild(skeletonContainer);
        scrollToBottom();

        return skeletonContainerId;
    }

    function removeProductSkeletons(skeletonId) {
        const skeletonContainer = document.getElementById(skeletonId);
        if (skeletonContainer) {
            skeletonContainer.remove();
        }
    }

    function startNewChat() {
        clearChat();
        activeSessionId = null;
    }

    function clearChat(skipWelcome = false) {
        const allMessages = Array.from(chatMessages.querySelectorAll('.message, .product-results'));

        if (allMessages.length === 0) {
            if (!skipWelcome) {
                addWelcomeMessage();
            }
            return;
        }

        allMessages.forEach(msg => {
            msg.style.transition = 'all 0.3s ease';
            msg.style.opacity = '0';
            msg.style.transform = 'translateY(-10px)';
        });

        setTimeout(() => {
            while (chatMessages.firstChild) {
                chatMessages.removeChild(chatMessages.firstChild);
            }

            if (!skipWelcome) {
                addWelcomeMessage();
            }
        }, 300);
    }

    function clearChatSync(skipWelcome = false) {
        return new Promise((resolve) => {
            const allMessages = Array.from(chatMessages.querySelectorAll('.message, .product-results'));

            if (allMessages.length === 0) {
                if (!skipWelcome) {
                    addWelcomeMessage();
                }
                resolve();
                return;
            }

            allMessages.forEach(msg => {
                msg.style.transition = 'all 0.3s ease';
                msg.style.opacity = '0';
                msg.style.transform = 'translateY(-10px)';
            });

            setTimeout(() => {
                while (chatMessages.firstChild) {
                    chatMessages.removeChild(chatMessages.firstChild);
                }

                if (!skipWelcome) {
                    addWelcomeMessage();
                }
                resolve();
            }, 300);
        });
    }

    function addWelcomeMessage() {
        const welcomeMessage = document.createElement('div');
        welcomeMessage.classList.add('message', 'bot', 'welcome-message');
        welcomeMessage.style.opacity = '0';

        welcomeMessage.innerHTML = `
            <div class="avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="content">
                <h3 class="welcome-heading">👋 Welcome to AliExpress ShopBot!</h3>
                <p>I'm your AI shopping assistant. I can help you find amazing products on AliExpress based on your preferences. What are you looking for today?</p>
                <div class="suggestion-chips">
                    <button class="suggestion-chip" onclick="suggestQuery('Find me a stylish smartwatch under $50')">
                        <i class="fas fa-clock"></i> Smartwatch under $50
                    </button>
                    <button class="suggestion-chip" onclick="suggestQuery('Best wireless earbuds with noise cancellation')">
                        <i class="fas fa-headphones"></i> Wireless earbuds
                    </button>
                    <button class="suggestion-chip" onclick="suggestQuery('Trendy summer dresses for women')">
                        <i class="fas fa-tshirt"></i> Summer fashion
                    </button>
                </div>
            </div>
        `;

        chatMessages.appendChild(welcomeMessage);

        setTimeout(() => {
            welcomeMessage.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            welcomeMessage.style.opacity = '1';
            welcomeMessage.style.transform = 'translateY(0)';
        }, 50);
    }

    function addToSearchHistory(query) {
        const existingIndex = searchHistory.indexOf(query);
        if (existingIndex !== -1) {
            searchHistory.splice(existingIndex, 1);
        }
        searchHistory.unshift(query);
        if (searchHistory.length > 20) {
            searchHistory.pop();
        }
        localStorage.setItem('aliexpress-search-history', JSON.stringify(searchHistory));
    }

    function scrollToBottom() {
        const isAtBottom = chatMessages.scrollHeight - chatMessages.clientHeight <= chatMessages.scrollTop + 100;

        if (isAtBottom || !userHasScrolled) {
            chatMessages.scrollTo({
                top: chatMessages.scrollHeight,
                behavior: 'smooth'
            });
            scrollToBottomBtn.classList.remove('visible');
            scrollToBottomBtn.classList.add('hidden');
        } else if (userHasScrolled) {
            scrollToBottomBtn.classList.remove('hidden');
            scrollToBottomBtn.classList.add('visible');
        }
    }

    function resetInputPosition() {
        const userInput = document.getElementById('aliexpress-user-input');
        if (userInput) {
            userInput.style.height = 'auto';
            userInput.style.height = (userInput.scrollHeight) + 'px';
        }
    }

    function resetChatState() {
        userHasScrolled = false;
        scrollToBottomBtn.classList.remove('visible');
        scrollToBottomBtn.classList.add('hidden');
    }

    window.addEventListener('resize', resetInputPosition);
    resetInputPosition();

    window.addEventListener('load', resetChatState);

    loadChatSessions();

    setTimeout(() => {
        if (chatMessages.children.length === 0) {
            addWelcomeMessage();
        }
        resetChatState();
    }, 300);

    function loadChatSessions() {
        fetch('/api/aliexpress/chat/sessions', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include'
        })
        .then(response => {
            if (response.status === 401) {
                return null;
            }
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            updateHistorySidebar(data.sessions || []);
            if (data && data.sessions && data.sessions.length > 0) {
                const latestSession = data.sessions[0];
                loadSessionMessages(latestSession.id);
            }
        })
        .catch(error => {
            if (chatMessages.children.length === 0) {
                addWelcomeMessage();
            }
        });
    }

    function createChatSession(firstMessage) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        return new Promise((resolve, reject) => {
            fetch('/api/aliexpress/chat/sessions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'include',
                body: JSON.stringify({
                    name: firstMessage.substring(0, 50) + (firstMessage.length > 50 ? '...' : '')
                })
            })
            .then(response => {
                if (response.status === 401) {
                    throw new Error('Authentication required. Please log in to save your chat history.');
                }
                if (response.status === 419) {
                    throw new Error('Session expired. Please refresh the page and try again.');
                }
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(`Server error (${response.status}): ${errorData.details || errorData.message || 'Unknown error'}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.id) {
                    resolve(data.id);
                } else if (data.session && data.session.id) {
                    resolve(data.session.id);
                } else {
                    throw new Error('Invalid response: No session ID returned');
                }
            })
            .catch(error => {
                if (error.message.includes('Authentication required')) {
                    addBotMessage('⚠️ You need to be logged in to save your chat history. <a href="/login" class="message-link">Log in</a> to save your conversations.');
                } else if (error.message.includes('Session expired')) {
                    addBotMessage('⚠️ Your session has expired. Please <a href="javascript:window.location.reload();" class="message-link">refresh the page</a> and try again.');
                } else {
                    addBotMessage(`⚠️ Sorry, there was an error creating a chat session: ${error.message}. Please try again later.`);
                }
                reject(error);
            });
        });
    }

    function saveMessage(sessionId, sender, content) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('/api/aliexpress/chat/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include',
            body: JSON.stringify({
                session_id: sessionId,
                sender: sender,
                content: content
            })
        })
        .then(response => {
            if (response.status === 401) {
                return;
            }
            if (response.status === 419) {
                return;
            }
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
        })
        .catch(error => {
        });
    }

    function loadSessionMessages(sessionId) {
        activeSessionId = sessionId;

        clearChatSync(true).then(() => {
            showTypingIndicator();

            fetch(`/api/aliexpress/chat/${sessionId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                },
                credentials: 'include'
            })
            .then(response => {
                if (response.status === 401) {
                    throw new Error('Authentication required');
                }
                if (response.status === 404) {
                    throw new Error('Session not found');
                }
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                hideTypingIndicator();

                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach((message, index) => {
                        if (message.sender === 'user') {
                            addUserMessage(message.content, false);
                        } else {
                            try {
                                const contentObj = JSON.parse(message.content);
                                if (contentObj && contentObj.type === 'product_grid' && Array.isArray(contentObj.products)) {
                                    appendProductResults(contentObj.products, false);
                                } else {
                                    addBotMessage(message.content, false, false);
                                }
                            } catch (e) {
                                addBotMessage(message.content, false, false);
                            }
                        }
                    });
                    scrollToBottom();
                } else {
                    addBotMessage('This conversation has no messages yet.', false, false);
                }
            })
            .catch(error => {
                hideTypingIndicator();
                if (error.message.includes('Authentication required')) {
                    addBotMessage('You need to be logged in to view this conversation.', false, false);
                } else if (error.message.includes('Session not found')) {
                    addBotMessage('This conversation was not found. It may have been deleted.', false, false);
                } else {
                    addBotMessage('Sorry, there was an error loading the conversation. Please try again.', false, false);
                }
            });
        });
    }

    function showConfirmDialog(title, message, onConfirm, onCancel = null) {
        const overlay = document.createElement('div');
        overlay.className = 'confirm-dialog-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;

        const dialog = document.createElement('div');
        dialog.className = 'confirm-dialog';
        dialog.style.cssText = `
            background: white;
            border-radius: 12px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            transform: scale(0.9);
            transition: transform 0.3s ease;
        `;

        dialog.innerHTML = `
            <div class="dialog-header" style="margin-bottom: 16px;">
                <h3 style="margin: 0; color: #333; font-size: 18px; font-weight: 600;">${title}</h3>
            </div>
            <div class="dialog-body" style="margin-bottom: 24px;">
                <p style="margin: 0; color: #666; line-height: 1.5;">${message}</p>
            </div>
            <div class="dialog-actions" style="display: flex; gap: 12px; justify-content: flex-end;">
                <button class="cancel-btn" style="
                    padding: 8px 16px;
                    border: 1px solid #ddd;
                    background: white;
                    border-radius: 6px;
                    cursor: pointer;
                    color: #666;
                    font-weight: 500;
                    transition: all 0.2s ease;
                ">Cancel</button>
                <button class="confirm-btn" style="
                    padding: 8px 16px;
                    border: none;
                    background: #d32f2f;
                    color: white;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: 500;
                    transition: all 0.2s ease;
                ">Confirm</button>
            </div>
        `;

        overlay.appendChild(dialog);
        document.body.appendChild(overlay);

        setTimeout(() => {
            overlay.style.opacity = '1';
            dialog.style.transform = 'scale(1)';
        }, 10);

        const cancelBtn = dialog.querySelector('.cancel-btn');
        const confirmBtn = dialog.querySelector('.confirm-btn');

        cancelBtn.addEventListener('mouseenter', () => {
            cancelBtn.style.background = '#f5f5f5';
            cancelBtn.style.borderColor = '#ccc';
        });
        cancelBtn.addEventListener('mouseleave', () => {
            cancelBtn.style.background = 'white';
            cancelBtn.style.borderColor = '#ddd';
        });

        confirmBtn.addEventListener('mouseenter', () => {
            confirmBtn.style.background = '#b71c1c';
        });
        confirmBtn.addEventListener('mouseleave', () => {
            confirmBtn.style.background = '#d32f2f';
        });

        function closeDialog() {
            overlay.style.opacity = '0';
            dialog.style.transform = 'scale(0.9)';
            setTimeout(() => {
                document.body.removeChild(overlay);
            }, 300);
        }

        cancelBtn.addEventListener('click', () => {
            closeDialog();
            if (onCancel) onCancel();
        });

        confirmBtn.addEventListener('click', () => {
            closeDialog();
            if (onConfirm) onConfirm();
        });

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                closeDialog();
                if (onCancel) onCancel();
            }
        });
    }

    function toggleHistorySidebar() {
        if (chatHistorySidebar.classList.contains('open')) {
            closeHistorySidebar();
        } else {
            openHistorySidebar();
        }
    }

    function openHistorySidebar() {
        chatHistorySidebar.classList.add('open');
        refreshChatHistory();
    }

    function closeHistorySidebar() {
        chatHistorySidebar.classList.remove('open');
    }

    function updateHistorySidebar(sessions) {
        const historyList = document.getElementById('chat-history-list');

        if (!sessions || sessions.length === 0) {
            historyList.innerHTML = `
                <div class="empty-history-message">
                    <i class="fas fa-comments"></i>
                    <p>No chat history yet</p>
                </div>
            `;
            return;
        }

        historyList.innerHTML = '';

        sessions.forEach(session => {
            const historyItem = document.createElement('div');
            historyItem.className = 'chat-history-item';
            if (session.id === activeSessionId) {
                historyItem.classList.add('active');
            }

            const date = new Date(session.created_at);
            const formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

            historyItem.innerHTML = `
                <div class="history-item-content">
                    <div class="history-item-title">${session.name || 'Untitled Chat'}</div>
                    <div class="history-item-date">${formattedDate}</div>
                </div>
                <button class="delete-history-btn" data-session-id="${session.id}">
                    <i class="fas fa-trash"></i>
                </button>
            `;

            historyItem.querySelector('.history-item-content').addEventListener('click', () => {
                loadChatSession(session.id);
                closeHistorySidebar();
            });

            historyItem.querySelector('.delete-history-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                showConfirmDialog(
                    'Delete Chat Session',
                    'Are you sure you want to delete this chat session? This action cannot be undone.',
                    () => deleteChatSession(session.id)
                );
            });

            historyList.appendChild(historyItem);
        });
    }

    function refreshChatHistory() {
        fetch('/api/aliexpress/chat/sessions', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include'
        })
        .then(response => {
            if (response.status === 401) {
                return null;
            }
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data && data.sessions) {
                updateHistorySidebar(data.sessions);
            }
        })
        .catch(error => {
        });
    }

    function loadChatSession(sessionId) {
        activeSessionId = sessionId;
        clearChat(true);
        showTypingIndicator();
        loadSessionMessages(sessionId);

        document.querySelectorAll('.chat-history-item').forEach(item => {
            item.classList.remove('active');
        });

        document.querySelectorAll('.delete-history-btn').forEach(btn => {
            const itemSessionId = btn.getAttribute('data-session-id');
            if (itemSessionId === sessionId.toString()) {
                const historyItem = btn.closest('.chat-history-item');
                if (historyItem) {
                    historyItem.classList.add('active');
                }
            }
        });
    }

    function deleteChatSession(sessionId) {
        fetch(`/api/aliexpress/chat/sessions/${sessionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (sessionId === activeSessionId) {
                startNewChat();
            }
            refreshChatHistory();
            showNotification('Chat session deleted successfully', 'success');
        })
        .catch(error => {
            showNotification('Failed to delete chat session', 'error');
        });
    }

    function clearAllHistory() {
        fetch('/api/aliexpress/chat/history/clear', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            startNewChat();
            refreshChatHistory();
            showNotification('All chat history cleared successfully', 'success');
        })
        .catch(error => {
            showNotification('Failed to clear chat history', 'error');
        });
    }

    function showNotification(message, type = 'info') {
        const existingNotification = document.querySelector('.chat-notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        const notification = document.createElement('div');
        notification.className = `chat-notification ${type}`;

        const icon = type === 'error' ? 'fa-exclamation-circle' :
                    type === 'success' ? 'fa-check-circle' : 'fa-info-circle';

        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${icon}"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    function recordAliExpressProductView(productId, event, productData = null) {
        if (!productId || productId === '') {
            return;
        }

        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) {
            return;
        }

        const requestBody = {};
        if (productData) {
            requestBody.product_data = {
                title: productData.product_title || productData.title || 'Unknown Product',
                price: productData.target_sale_price || productData.price || '0.00',
                original_price: productData.target_original_price || productData.original_price || productData.target_sale_price || productData.price || '0.00',
                currency: 'USD',
                image_url: productData.product_main_image_url || productData.image_url || '',
                product_url: productData.promotion_link || productData.product_detail_url || '',
                description: productData.product_title || productData.title || '',
                rating: parseFloat(productData.evaluate_rate || productData.rating || 0),
                reviews_count: parseInt(productData.lastest_volume || productData.reviews_count || 0),
                shipping_info: 'Standard shipping',
                category: productData.first_level_category_name || productData.category || 'General'
            };
        }

        fetch(`/api/aliexpress-products/view/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify(requestBody)
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
            }
        })
        .then(data => {
        })
        .catch(error => {
        });
    }

    function handleAliExpressSaveButtonClick(productId, button, productData = null) {
        if (!productId || productId === '') {
            showAliExpressToast('Invalid product ID. Cannot save product.', 'error');
            return;
        }

        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) {
            showAliExpressToast('Authentication error. Please refresh the page.', 'error');
            return;
        }

        fetch(`/api/aliexpress-products/saved/check/${productId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': token.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.saved) {
                showAliExpressToast('Product is already saved! Click again to remove.', 'info');
                setTimeout(() => {
                    toggleAliExpressSaveProduct(productId, button, productData);
                }, 1000);
            } else {
                toggleAliExpressSaveProduct(productId, button, productData);
            }
        })
        .catch(error => {
            showAliExpressToast('Error checking save status. Trying to save anyway...', 'error');
            setTimeout(() => {
                toggleAliExpressSaveProduct(productId, button, productData);
            }, 1000);
        });
    }

    function toggleAliExpressSaveProduct(productId, button, productData = null) {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) {
            showAliExpressToast('Authentication error. Please refresh the page.', 'error');
            return;
        }

        button.disabled = true;

        const requestBody = {};
        if (productData) {
            requestBody.product_data = {
                title: productData.product_title || productData.title || 'Unknown Product',
                price: productData.target_sale_price || productData.price || '0.00',
                original_price: productData.target_original_price || productData.original_price || productData.target_sale_price || productData.price || '0.00',
                currency: 'USD',
                image_url: productData.product_main_image_url || productData.image_url || '',
                product_url: productData.promotion_link || productData.product_detail_url || '',
                description: productData.product_title || productData.title || '',
                rating: parseFloat(productData.evaluate_rate || productData.rating || 0),
                reviews_count: parseInt(productData.lastest_volume || productData.reviews_count || 0),
                shipping_info: 'Standard shipping',
                category: productData.first_level_category_name || productData.category || 'General'
            };
        }

        fetch(`/api/aliexpress-products/save/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify(requestBody)
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
            }
        })
        .then(data => {
            if (data.saved) {
                button.classList.remove('save-btn');
                button.classList.add('saved-btn');
                button.innerHTML = '<i class="fas fa-bookmark"></i> Saved';
            } else {
                button.classList.remove('saved-btn');
                button.classList.add('save-btn');
                button.innerHTML = '<i class="far fa-bookmark"></i> Save';
            }
            showAliExpressToast(data.message, 'success');
        })
        .catch(error => {
            showAliExpressToast('Error saving product. Please try again.', 'error');
        })
        .finally(() => {
            button.disabled = false;
        });
    }

    function checkIfAliExpressProductSaved(productId, button) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/api/aliexpress-products/saved/check/${productId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        })
        .then(response => response.json())
        .then(data => {
            if (data.saved) {
                button.classList.remove('save-btn');
                button.classList.add('saved-btn');
                button.innerHTML = '<i class="fas fa-bookmark"></i> Saved';
            } else {
                button.classList.remove('saved-btn');
                button.classList.add('save-btn');
                button.innerHTML = '<i class="far fa-bookmark"></i> Save';
            }
        })
        .catch(error => {});
    }

    function showAliExpressToast(message, type = 'success') {
        const existingToast = document.querySelector('.aliexpress-toast');
        if (existingToast) {
            existingToast.remove();
        }

        const toast = document.createElement('div');
        toast.className = `aliexpress-toast aliexpress-toast-${type}`;

        const icon = document.createElement('i');
        icon.className = type === 'success' ? 'fas fa-check-circle' :
                        type === 'error' ? 'fas fa-exclamation-circle' :
                        'fas fa-info-circle';

        const text = document.createElement('span');
        text.textContent = message;

        toast.appendChild(icon);
        toast.appendChild(text);
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('show');
        }, 100);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    function debugAliExpressProductInteraction(productId, action, productData = null) {
        // No-op: removed all console logs for debug
    }
});
