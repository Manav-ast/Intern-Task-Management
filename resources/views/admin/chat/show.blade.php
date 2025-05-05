@extends('layouts.admin')

@section('content')
    <meta name="user-id" content="{{ auth()->id() }}">
    <meta name="user-type" content="{{ get_class(auth()->user()) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .chat-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .chat-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            height: calc(100vh - 200px);
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            background-color: white;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            background-color: #f3f4f6;
        }

        .message {
            margin-bottom: 1rem;
            max-width: 80%;
        }

        .message.sent {
            margin-left: auto;
        }

        .message-content {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            position: relative;
            word-wrap: break-word;
        }

        .message.sent .message-content {
            background-color: #059669;
            color: white;
        }

        .message.received .message-content {
            background-color: #4f46e5;
            color: white;
        }

        .message-time {
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .message.sent .message-time {
            color: rgba(255, 255, 255, 0.9);
            justify-content: flex-end;
        }

        .message.received .message-time {
            color: rgba(255, 255, 255, 0.9);
        }

        .date-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 1rem 0;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .date-divider::before,
        .date-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #e5e7eb;
            margin: 0 1rem;
        }

        .message-sender {
            font-size: 0.75rem;
            margin-bottom: 0.25rem;
            color: #6b7280;
        }

        .message.sent {
            margin-left: auto;
        }

        .message.received {
            margin-right: auto;
        }

        .role-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-weight: 500;
            margin-left: 0.5rem;
        }

        .role-badge.admin {
            background-color: #059669;
            color: white;
        }

        .role-badge.intern {
            background-color: #4f46e5;
            color: white;
        }

        .chat-input {
            padding: 1rem 1.5rem;
            background-color: white;
            border-top: 1px solid #e5e7eb;
        }

        .chat-input form {
            display: flex;
            gap: 0.5rem;
        }

        .chat-input input {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .chat-input input:focus {
            outline: none;
            border-color: #4f46e5;
            ring: 2px;
            ring-color: #e0e7ff;
        }

        .send-button {
            padding: 0.5rem 1rem;
            background-color: #4f46e5;
            color: white;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .send-button:hover {
            background-color: #4338ca;
        }

        .back-button {
            padding: 0.5rem;
            color: #4f46e5;
            border-radius: 0.375rem;
            margin-right: 1rem;
            transition: all 0.2s;
        }

        .back-button:hover {
            background-color: #f3f4f6;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background-color: #4f46e5;
            color: white;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }
    </style>

    <div class="py-6">
        <div class="chat-container">
            <div class="chat-card">
                <div class="chat-header">
                    <a href="{{ route('admin.chat.index') }}" class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div class="flex items-center ml-4">
                        <div class="avatar" style="background-color: #4f46e5;">
                            {{ substr($otherUser->name, 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <div class="flex items-center">
                                <h2 class="text-lg font-semibold text-gray-900">{{ $otherUser->name }}</h2>
                                <span class="role-badge intern">Intern</span>
                            </div>
                            <p class="text-sm text-gray-500">Online</p>
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
                            @if ($message->sender_id !== auth()->id())
                                <div class="message-sender">{{ $message->sender->name }} (Intern)</div>
                            @endif
                            <div class="message-content">
                                <p class="mb-1">{{ $message->message }}</p>
                                <div class="message-time">
                                    <span>{{ $message->created_at->format('g:i A') }}</span>
                                    @if ($message->sender_id === auth()->id())
                                        @if ($message->read_at)
                                            <i class="fas fa-check-double"></i>
                                        @else
                                            <i class="fas fa-check"></i>
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
                            <span>Send</span>
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
        <script src="{{ mix('js/chat.js') }}"></script>
    @endpush
@endsection
