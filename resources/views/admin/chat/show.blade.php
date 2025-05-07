@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="py-3 sm:py-6">
        <div class="chat-container">
            <div class="chat-card">
                <div class="chat-header">
                    <a href="{{ route('admin.chat.index') }}" class="back-button">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div class="flex items-center ml-3 sm:ml-4">
                        <div class="avatar inline-flex items-center justify-center" style="background-color: #4f46e5;">
                            <span class="text-white">{{ substr($otherUser->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-2 sm:ml-3">
                            <div class="flex items-center">
                                <h2
                                    class="text-base sm:text-lg font-semibold text-gray-900 truncate max-w-[150px] sm:max-w-none">
                                    {{ $otherUser->name }}</h2>
                                <span class="role-badge intern">Intern</span>
                            </div>
                            <p class="text-xs sm:text-sm text-gray-500">Online</p>
                        </div>
                    </div>
                </div>

                <div class="chat-messages" id="chat-messages">
                    @php
                        $currentDate = null;
                    @endphp
                    @foreach ($messages as $message)
                        @php
                            $messageDate = $message->created_at->format('Y-m-d');
                            $displayDate = $message->created_at->calendar(null, [
                                'sameDay' => '[Today]',
                                'lastDay' => '[Yesterday]',
                                'lastWeek' => '[Last] l',
                                'sameElse' => 'F j, Y',
                            ]);
                        @endphp

                        @if ($currentDate !== $messageDate)
                            <div class="date-divider">{{ $displayDate }}</div>
                            @php
                                $currentDate = $messageDate;
                            @endphp
                        @endif

                        <div class="message {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}"
                            data-message-id="{{ $message->id }}">
                            <div class="message-content">
                                <p class="mb-1 break-words">{{ $message->message }}</p>
                                <div class="message-time">
                                    <span>{{ $message->created_at->format('g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="chat-input">
                    <form id="message-form" class="flex w-full">
                        @csrf
                        <input type="text" id="message-input" name="message" placeholder="Type a message..." required
                            class="focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit" class="send-button">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18l9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const messagesContainer = document.getElementById('chat-messages');
                const messageForm = document.getElementById('message-form');
                const messageInput = document.getElementById('message-input');
                const userId = {{ Auth::id() }};
                const otherUserId = {{ $otherUser->id }};

                // Scroll to bottom of messages
                function scrollToBottom() {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
                scrollToBottom();

                // Add new message to the chat
                function appendMessage(message, isSender = true) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `message ${isSender ? 'sent' : 'received'}`;
                    messageDiv.setAttribute('data-message-id', message.id);

                    const contentDiv = document.createElement('div');
                    contentDiv.className = 'message-content';

                    const messageText = document.createElement('p');
                    messageText.className = 'mb-1 break-words';
                    messageText.textContent = message.message;

                    const timeDiv = document.createElement('div');
                    timeDiv.className = 'message-time';

                    const timeSpan = document.createElement('span');
                    const messageDate = new Date(message.created_at);
                    timeSpan.textContent = messageDate.toLocaleTimeString('en-US', {
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: true
                    });

                    timeDiv.appendChild(timeSpan);
                    contentDiv.appendChild(messageText);
                    contentDiv.appendChild(timeDiv);
                    messageDiv.appendChild(contentDiv);

                    const messagesContainer = document.getElementById('chat-messages');
                    messagesContainer.appendChild(messageDiv);
                    scrollToBottom();
                }

                // Listen for new messages
                const channelName = `chat.${Math.min(userId, otherUserId)}.${Math.max(userId, otherUserId)}`;

                // Message queue to handle concurrent messages
                const messageQueue = new Set();

                window.Echo.private(channelName)
                    .listen('ChatMessageEvent', (e) => {
                        // Only handle messages from the other user
                        if (e.sender_id === userId) return;

                        // Generate a unique key for this message
                        const messageKey = `${e.id}-${e.created_at}`;

                        // Check if we've already processed this message
                        if (messageQueue.has(messageKey)) {
                            console.log('Message already in queue, skipping:', e.id);
                            return;
                        }

                        // Check if message already exists in the DOM
                        const existingMessage = document.querySelector(`[data-message-id="${e.id}"]`);
                        if (existingMessage) {
                            console.log('Message already displayed, skipping:', e.id);
                            return;
                        }

                        // Add to queue
                        messageQueue.add(messageKey);

                        // Remove from queue after 5 seconds
                        setTimeout(() => {
                            messageQueue.delete(messageKey);
                        }, 5000);

                        // Clear any existing timeout
                        if (this.messageAddTimeout) {
                            clearTimeout(this.messageAddTimeout);
                        }

                        // Add message with small delay to prevent race conditions
                        this.messageAddTimeout = setTimeout(() => {
                            appendMessage({
                                id: e.id,
                                message: e.message,
                                created_at: e.created_at,
                                sender_id: e.sender_id
                            }, false);
                        }, 50);
                    });

                // Handle message submission
                messageForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const message = messageInput.value.trim();
                    if (!message) return;

                    // Generate a temporary ID for this message
                    const tempId = `temp-${Date.now()}`;

                    // Disable the input and button while sending
                    messageInput.disabled = true;
                    const submitButton = messageForm.querySelector('button[type="submit"]');
                    submitButton.disabled = true;

                    try {
                        const response = await fetch('/admin/chat/{{ $otherUser->id }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                message: message
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            // Clear input first
                            messageInput.value = '';

                            // Check if message already exists before appending
                            const existingMessage = document.querySelector(
                                `[data-message-id="${data.id}"]`);
                            if (!existingMessage) {
                                appendMessage({
                                    id: data.id,
                                    message: data.message,
                                    created_at: data.created_at,
                                    sender_id: userId
                                }, true);
                            }
                        } else {
                            throw new Error(data.error || 'Failed to send message');
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        showError(error.message ||
                            'Network error. Please check your connection and try again.');
                    } finally {
                        // Re-enable the input and button
                        messageInput.disabled = false;
                        submitButton.disabled = false;
                        messageInput.focus();
                    }
                });

                function showError(message) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.textContent = message;
                    messageForm.insertAdjacentElement('beforebegin', errorDiv);
                    setTimeout(() => errorDiv.remove(), 5000);
                }
            });
        </script>
    @endpush

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        /* Prevent pull-to-refresh on mobile */
        body {
            overscroll-behavior-y: contain;
        }

        /* Improve touch scrolling */
        .chat-messages {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }

        /* Hide scrollbar on mobile */
        @media (max-width: 640px) {
            .chat-messages::-webkit-scrollbar {
                display: none;
            }

            .chat-messages {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        }

        /* Prevent text selection on double tap */
        .chat-header,
        .message-content {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }

        /* Allow text selection in message content */
        .message-content p {
            -webkit-user-select: text;
            user-select: text;
        }

        .error-message {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 0.75rem;
            margin: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            text-align: center;
        }
    </style>
@endsection
