@extends('layouts.intern')

@section('content')
    <style>
        .chat-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .chat-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .chat-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-list-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .chat-list-item:hover {
            background-color: #f9fafb;
        }

        .chat-list-item:last-child {
            border-bottom: none;
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

        .message-preview {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .timestamp {
            color: #9ca3af;
            font-size: 0.75rem;
        }

        .unread-badge {
            background-color: #4f46e5;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .new-chat-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background-color: #4f46e5;
            color: white;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .new-chat-btn:hover {
            background-color: #4338ca;
            color: white;
            text-decoration: none;
        }
    </style>

    <div class="py-6">
        <div class="chat-container">
            <div class="chat-card">
                <div class="chat-header">
                    <h2 class="text-xl font-semibold text-gray-800">Messages</h2>
                    <a href="{{ route('intern.chat.users') }}" class="new-chat-btn">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        New Chat
                    </a>
                </div>

                @if ($messages->isEmpty())
                    <div class="p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No messages</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new chat.</p>
                        <div class="mt-6">
                            <a href="{{ route('intern.chat.users') }}" class="new-chat-btn">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                New Chat
                            </a>
                        </div>
                    </div>
                @else
                    <div class="divide-y divide-gray-200">
                        @foreach ($messages->groupBy(function ($message) {
            return $message->sender_id === auth()->id() ? $message->receiver_id : $message->sender_id;
        }) as $conversation)
                            @php
                                $lastMessage = $conversation->first();
                                $otherUser =
                                    $lastMessage->sender_id === auth()->id()
                                        ? $lastMessage->receiver
                                        : $lastMessage->sender;
                                $unreadCount = $conversation
                                    ->where('receiver_id', auth()->id())
                                    ->whereNull('read_at')
                                    ->count();
                            @endphp
                            <a href="{{ route('intern.chat.show', $otherUser->id) }}"
                                class="chat-list-item block hover:bg-gray-50">
                                <div class="flex items-center space-x-4">
                                    <div class="avatar">
                                        {{ substr($otherUser->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $otherUser->name }}
                                            </p>
                                            <span class="timestamp">
                                                {{ $lastMessage->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <p class="message-preview truncate">
                                                @if ($lastMessage->sender_id === auth()->id())
                                                    <svg class="inline w-4 h-4 mr-1 text-blue-500" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                                        <path fill-rule="evenodd"
                                                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                                {{ $lastMessage->message }}
                                            </p>
                                            @if ($unreadCount > 0)
                                                <span class="unread-badge">
                                                    {{ $unreadCount }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    @if ($messages->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $messages->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
