<x-app-layout>
    <div class="chat-container">
        <!-- Chat History Sidebar -->
        <div class="chat-sidebar custom-scrollbar" id="chat-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-header-top">
                    <h3>Chat History</h3>
                    <button class="close-sidebar-btn" id="close-sidebar-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <button class="new-chat-btn" id="new-chat-btn">
                    <i class="fas fa-plus"></i> New Chat
                </button>
            </div>
            <div class="sessions-list" id="sessions-list">
                <!-- Sessions will be populated here -->
                <div class="loading-sessions">
                    <div class="spinner"></div>
                    <span>Loading conversations...</span>
                </div>
            </div>
        </div>

        <!-- Chat Content -->
        <div class="chat-content" id="chat-content">
            <!-- Toggle Button for Sidebar -->
            <button class="sidebar-toggle" id="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="chat-box">
                <div class="chat-messages custom-scrollbar" id="chat-messages">
                    <div class="message bot">
                        <div class="avatar">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="content">
                            Hello! I'm your shopping assistant. Tell me what products you're looking for, and I can recommend some options.
                        </div>
                    </div>
                </div>

                <div class="chat-form-container">
                    <div class="chat-form">
                        <textarea id="user-input" placeholder="Ask me about products..." rows="1" required></textarea>
                        <div class="input-actions">
                            <input type="hidden" id="max-price" placeholder="Max price">
                            <button type="submit" class="send-button" id="send-button">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Rename Modal -->
    <div id="rename-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h2>Rename Conversation</h2>
            <input type="text" id="session-name-input" placeholder="Enter a name">
            <input type="hidden" id="session-id-input">
            <button id="rename-session-btn">Save</button>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <span class="close-delete-modal">&times;</span>
            <h2>Delete Conversation</h2>
            <p>Are you sure you want to delete this conversation? This action cannot be undone.</p>
            <input type="hidden" id="delete-session-id">
            <div class="modal-actions">
                <button id="cancel-delete-btn" class="cancel-btn">Cancel</button>
                <button id="confirm-delete-btn" class="delete-btn">Delete</button>
            </div>
        </div>
    </div>

    <!-- Unsave Product Confirmation Modal -->
    <div id="unsave-product-modal" class="modal">
        <div class="modal-content">
            <span class="close-unsave-modal">&times;</span>
            <h2>Remove Saved Product</h2>
            <p>This product is already in your saved items. Would you like to remove it?</p>
            <input type="hidden" id="unsave-product-id">
            <input type="hidden" id="unsave-product-button">
            <div class="modal-actions">
                <button id="cancel-unsave-btn" class="cancel-btn">Cancel</button>
                <button id="confirm-unsave-btn" class="delete-btn">Remove</button>
            </div>
        </div>
    </div>

    <!-- Add inline script for the recordProductView function -->
    <script>
        // Global function to record product views
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

        // Function to toggle save/unsave product
        function toggleSaveProduct(productId, button) {
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Disable button during request
            button.disabled = true;

            fetch(`/api/products/save/${productId}`, {
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
                    return response.json();
                } else {
                    console.error('Failed to toggle save status:', response.status);
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }
            })
            .then(data => {
                // Update button appearance based on save status
                if (data.status === 'saved') {
                    button.classList.remove('bg-gray-600', 'hover:bg-gray-500');
                    button.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                    button.innerHTML = '<i class="fas fa-bookmark mr-1"></i> Saved';
                } else if (data.status === 'removed') {
                    button.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                    button.classList.add('bg-gray-600', 'hover:bg-gray-500');
                    button.innerHTML = '<i class="far fa-bookmark mr-1"></i> Save';
                }

                // Show improved toast notification
                const toast = document.createElement('div');
                toast.className = 'product-toast';

                // Create icon based on action
                const icon = document.createElement('i');
                icon.className = data.status === 'saved'
                    ? 'fas fa-check-circle toast-icon'
                    : 'fas fa-trash-alt toast-icon';

                // Create toast message
                const message = document.createElement('span');
                message.textContent = data.message;

                // Append elements to toast
                toast.appendChild(icon);
                toast.appendChild(message);
                document.body.appendChild(toast);

                // Remove toast after 3 seconds
                setTimeout(() => {
                    toast.classList.add('toast-hide');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving product. Please try again.');
            })
            .finally(() => {
                // Re-enable button
                button.disabled = false;
            });
        }

        // Function to check if a product is saved
        function checkIfProductSaved(productId, button) {
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/api/products/saved/check/${productId}`, {
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
                    button.classList.remove('bg-gray-600', 'hover:bg-gray-500');
                    button.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
                    button.innerHTML = '<i class="fas fa-bookmark mr-1"></i> Saved';
                } else {
                    button.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                    button.classList.add('bg-gray-600', 'hover:bg-gray-500');
                    button.innerHTML = '<i class="far fa-bookmark mr-1"></i> Save';
                }
            })
            .catch(error => console.error('Error checking if product is saved:', error));
        }

        // Function to handle save button click
        function handleSaveButtonClick(productId, button) {
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Check if already saved
            fetch(`/api/products/saved/check/${productId}`, {
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
                    // If already saved, show the confirmation modal
                    const modal = document.getElementById('unsave-product-modal');
                    document.getElementById('unsave-product-id').value = productId;
                    document.getElementById('unsave-product-button').value = button.id || '';
                    modal.style.display = 'block';
                } else {
                    // If not saved, save directly
                    toggleSaveProduct(productId, button);
                }
            })
            .catch(error => {
                console.error('Error checking if product is saved:', error);
                // In case of error, proceed with toggle
                toggleSaveProduct(productId, button);
            });
        }

        // Function to handle show all products button click
        function showAllProducts(button) {
            // Get all hidden products
            const hiddenProducts = document.querySelectorAll('.hidden-product');

            // Show each hidden product with a fade-in animation
            hiddenProducts.forEach(product => {
                product.classList.remove('hidden-product');
                product.style.animation = 'fadeIn 0.3s ease-in-out';
            });

            // Hide the button after showing all products
            button.parentElement.style.display = 'none';

            // Scroll to show more products
            setTimeout(() => {
                window.scrollBy({
                    top: 200,
                    behavior: 'smooth'
                });
            }, 300);
        }

        // Set up unsave modal event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const unsaveModal = document.getElementById('unsave-product-modal');
            const closeUnsaveModal = document.querySelector('.close-unsave-modal');
            const cancelUnsaveBtn = document.getElementById('cancel-unsave-btn');
            const confirmUnsaveBtn = document.getElementById('confirm-unsave-btn');

            // Close modal when clicking the X
            closeUnsaveModal.onclick = function() {
                unsaveModal.style.display = 'none';
            };

            // Close modal when clicking Cancel
            cancelUnsaveBtn.onclick = function() {
                unsaveModal.style.display = 'none';
            };

            // Handle confirm unsave
            confirmUnsaveBtn.onclick = function() {
                const productId = document.getElementById('unsave-product-id').value;
                const buttonId = document.getElementById('unsave-product-button').value;

                let button;
                if (buttonId) {
                    button = document.getElementById(buttonId);
                }

                if (!button) {
                    // If the button is not found by ID, we'll create a temporary button element
                    // This shouldn't happen in normal flow, but prevents errors
                    button = document.createElement('button');
                }

                toggleSaveProduct(productId, button);
                unsaveModal.style.display = 'none';
            };

            // Close modal when clicking outside of it
            window.onclick = function(event) {
                if (event.target == unsaveModal) {
                    unsaveModal.style.display = 'none';
                }
            };
        });
    </script>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chatbot.css') }}">
    @endpush

    @push('scripts')
    @vite('resources/js/chatbot.js')
    @endpush
</x-app-layout>
