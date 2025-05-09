@extends('layouts.intern')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ Auth::id() }}">
    <meta name="user-type" content="{{ get_class(Auth::user()) }}">
    <meta name="user-name" content="{{ Auth::user()->name }}">
    <meta name="other-user-id" content="{{ $otherUser->id }}">
    <meta name="other-user-type" content="{{ get_class($otherUser) }}">
    <meta name="other-user-role" content="{{ $otherUser->isSuperAdmin() ? 'super-admin' : 'admin' }}">

    <div class="py-3 sm:py-6">
        <div class="chat-container">
            <div class="chat-card">
                <div class="chat-header">
                    <a href="{{ route('intern.chat.index') }}" class="back-button">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div class="flex items-center ml-3 sm:ml-4">
                        <div class="avatar inline-flex items-center justify-center"
                            style="background-color: {{ $otherUser->isSuperAdmin() ? '#dc2626' : '#4f46e5' }};">
                            <span class="text-white">{{ substr($otherUser->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-2 sm:ml-3">
                            <div class="flex items-center gap-2">
                                <h2
                                    class="text-base sm:text-lg font-semibold text-gray-900 truncate max-w-[150px] sm:max-w-none">
                                    {{ $otherUser->name }}
                                </h2>
                                @if ($otherUser->isSuperAdmin())
                                    <span class="role-badge super-admin">Super Admin</span>
                                @else
                                    <span class="role-badge admin">Admin</span>
                                @endif
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
                            $isSender =
                                $message->sender_id === auth()->id() &&
                                $message->sender_type === get_class(auth()->user());
                            $isSuperAdmin = !$isSender && $otherUser->isSuperAdmin();
                        @endphp

                        @if ($currentDate !== $messageDate)
                            <div class="date-divider">{{ $displayDate }}</div>
                            @php
                                $currentDate = $messageDate;
                            @endphp
                        @endif

                        <div class="message {{ $isSender ? 'sent' : 'received' }} {{ $isSuperAdmin ? 'super-admin-message' : '' }} animate-fade-in"
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
                    <form id="message-form" action="{{ route('intern.chat.store', ['id' => $otherUser->id]) }}"
                        class="flex w-full">
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
            $(document).ready(function() {
                // Mark the form to prevent global chat.js handling
                $('#message-form').attr('data-chat-initialized', 'true');

                const $messagesContainer = $('#chat-messages');
                const $messageForm = $('#message-form');
                const $messageInput = $('#message-input');
                const userId = $('meta[name="user-id"]').attr('content');
                const userType = $('meta[name="user-type"]').attr('content');
                const userName = $('meta[name="user-name"]').attr('content');
                const otherUserId = $('meta[name="other-user-id"]').attr('content');
                const otherUserType = $('meta[name="other-user-type"]').attr('content');
                const otherUserRole = $('meta[name="other-user-role"]').attr('content');

                console.log('Chat initialized with:', {
                    userId,
                    userType,
                    userName,
                    otherUserId,
                    otherUserType,
                    otherUserRole
                });

                // Scroll to bottom of messages
                function scrollToBottom() {
                    $messagesContainer.scrollTop($messagesContainer[0].scrollHeight);
                }
                scrollToBottom();

                // Add new message to the chat
                function appendMessage(message, isSender = true) {
                    console.log('Appending message:', {
                        message,
                        isSender,
                        otherUserRole
                    });

                    // Check if message already exists
                    const existingMessage = document.querySelector(`[data-message-id="${message.id}"]`);
                    if (existingMessage) {
                        console.log('Message already exists, skipping append:', message.id);
                        return;
                    }

                    const messageDiv = document.createElement('div');
                    const isSuperAdmin = !isSender && otherUserRole === 'super-admin';
                    messageDiv.className =
                        `message ${isSender ? 'sent' : 'received'} ${isSuperAdmin ? 'super-admin-message' : ''} animate-fade-in`;
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

                    $messagesContainer.append(messageDiv);
                    scrollToBottom();
                }

                // Listen for new messages
                const channelName = `chat.${Math.min(userId, otherUserId)}.${Math.max(userId, otherUserId)}`;
                console.log('Listening on channel:', channelName);

                window.Echo.channel(channelName)
                    .listen('ChatMessageEvent', (e) => {
                        console.log('Received WebSocket message:', e);

                        const isSender = e.sender_id === userId && e.sender_type === userType;

                        // Skip if this is our own message (we'll handle it in the send response)
                        if (isSender) {
                            console.log('Ignoring own message from WebSocket:', e.id);
                            return;
                        }

                        appendMessage({
                            id: e.id,
                            message: e.message,
                            created_at: e.created_at,
                            sender_id: e.sender_id,
                            sender_type: e.sender_type,
                            receiver_id: e.receiver_id,
                            receiver_type: e.receiver_type,
                            is_super_admin: e.is_super_admin
                        }, false);
                    });

                // Handle message submission
                let isSubmitting = false;

                $messageForm.on('submit', async function(e) {
                    e.preventDefault();

                    if (isSubmitting) {
                        console.log('Already submitting a message, please wait...');
                        return;
                    }

                    const message = $messageInput.val().trim();
                    if (!message) return;

                    isSubmitting = true;
                    const $submitButton = $(this).find('button[type="submit"]');
                    $submitButton.prop('disabled', true);
                    $messageInput.prop('disabled', true);

                    try {
                        console.log('Sending message:', {
                            message,
                            userId,
                            userType,
                            otherUserId,
                            otherUserType
                        });

                        const response = await fetch($messageForm.attr('action'), {
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
                        console.log('Server response:', data);

                        if (response.ok) {
                            // Clear input first
                            $messageInput.val('');

                            appendMessage({
                                id: data.id,
                                message: data.message,
                                created_at: data.created_at,
                                sender_id: userId,
                                sender_type: userType,
                                receiver_id: otherUserId,
                                receiver_type: otherUserType,
                                is_super_admin: false
                            }, true);
                        } else {
                            throw new Error(data.error || 'Failed to send message');
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        alert('Failed to send message. Please try again.');
                    } finally {
                        $messageInput.prop('disabled', false);
                        $submitButton.prop('disabled', false);
                        $messageInput.focus();
                        isSubmitting = false;
                    }
                });
            });
        </script>
    @endpush

    <style>
        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .role-badge.super-admin {
            background-color: #dc2626;
            color: white;
        }

        .role-badge.admin {
            background-color: #4f46e5;
            color: white;
        }

        .message.received.super-admin-message .message-content {
            background-color: #fee2e2;
            border: 1px solid #dc2626;
        }

        .avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
            transition: all 0.3s ease;
        }

        .avatar.super-admin {
            background-color: #dc2626;
        }

        .message.received.super-admin-message {
            position: relative;
        }

        .message.received.super-admin-message::before {
            content: 'Super Admin';
            position: absolute;
            top: -1.25rem;
            left: 0;
            font-size: 0.75rem;
            color: #dc2626;
            font-weight: 500;
        }

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
