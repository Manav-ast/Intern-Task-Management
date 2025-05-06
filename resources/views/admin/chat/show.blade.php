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
                        <div class="avatar" style="background-color: #4f46e5;">
                            {{ substr($otherUser->name, 0, 1) }}
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

                        <div class="message {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}">
                            <div class="message-sender">
                                @if ($message->sender_id !== auth()->id())
                                    <div class="flex items-center">
                                        <span
                                            class="text-xs sm:text-sm mr-1.5 sm:mr-2 text-gray-600">{{ $message->sender->name }}</span>
                                        <span class="sender-role intern text-xs sm:text-sm">Intern</span>
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <span
                                            class="text-xs sm:text-sm mr-1.5 sm:mr-2 text-gray-600">{{ auth()->user()->name }}</span>
                                        <span class="sender-role admin text-xs sm:text-sm">Admin</span>
                                    </div>
                                @endif
                            </div>
                            <div class="message-content">
                                <p class="mb-1 break-words">{{ $message->message }}</p>
                                <div class="message-time">
                                    <span>{{ $message->created_at->format('g:i A') }}</span>
                                    @if ($message->sender_id === auth()->id())
                                        @if ($message->read_at)
                                            <i class="fas fa-check-double text-xs sm:text-sm"></i>
                                        @else
                                            <i class="fas fa-check text-xs sm:text-sm"></i>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="chat-input">
                    <form id="message-form" action="{{ route('admin.chat.store', $otherUser->id) }}" method="POST">
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
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                // Scroll to bottom on load
                messagesContainer.scrollTop = messagesContainer.scrollHeight;

                // Handle form submission
                messageForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const message = messageInput.value.trim();
                    if (!message) return;

                    try {
                        const response = await fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                message: message,
                                _token: csrfToken
                            })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Failed to send message');
                        }

                        if (data.status === 'success' && data.message) {
                            appendMessage(data.message, true);
                            messageInput.value = '';
                            messageInput.focus();
                        } else {
                            throw new Error('Invalid response format');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        // Show a more user-friendly error message
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'error-message animate-fade-in';
                        errorMessage.textContent = 'Message could not be sent. Please try again.';
                        messageForm.insertAdjacentElement('beforebegin', errorMessage);

                        // Remove error message after 3 seconds
                        setTimeout(() => {
                            errorMessage.remove();
                        }, 3000);
                    }
                });

                // Handle message input on mobile
                messageInput.addEventListener('focus', function() {
                    setTimeout(() => {
                        window.scrollTo(0, 0);
                        document.body.scrollTop = 0;
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }, 100);
                });

                function appendMessage(message, isSent) {
                    const time = new Date(message.created_at).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const messageHtml = `
                        <div class="message ${isSent ? 'sent' : 'received'} animate-fade-in">
                            <div class="message-sender">
                                <div class="flex items-center">
                                    <span class="text-xs sm:text-sm mr-1.5 sm:mr-2 text-gray-600">${message.sender.name}</span>
                                    <span class="sender-role ${isSent ? 'admin' : 'intern'} text-xs sm:text-sm">
                                        ${isSent ? 'Admin' : 'Intern'}
                                    </span>
                                </div>
                            </div>
                            <div class="message-content">
                                <p class="mb-1 break-words">${message.message}</p>
                                <div class="message-time">
                                    <span>${time}</span>
                                    ${isSent ? '<i class="fas fa-check text-xs sm:text-sm"></i>' : ''}
                                </div>
                            </div>
                        </div>
                    `;

                    messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
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
