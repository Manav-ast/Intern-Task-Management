@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">

    <div class="py-3 sm:py-6">
        <div class="chat-container">
            <div class="chat-card">
                <div class="chat-header">
                    <h2 class="text-base sm:text-xl font-semibold text-gray-900">Messages</h2>
                    <a href="{{ route('admin.chat.users') }}" class="new-chat-btn">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-xs sm:text-sm">New Chat</span>
                    </a>
                </div>

                @if ($messages->isEmpty())
                    <div class="p-6 sm:p-8 text-center">
                        <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <h3 class="mt-2 text-sm sm:text-base font-medium text-gray-900">No messages</h3>
                        <p class="mt-1 text-xs sm:text-sm text-gray-500">Get started by creating a new chat.</p>
                        <div class="mt-4 sm:mt-6">
                            <a href="{{ route('admin.chat.users') }}" class="new-chat-btn">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1.5 sm:mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="text-xs sm:text-sm">New Chat</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="divide-y divide-gray-200">
                        @foreach ($messages->groupBy(function ($message) {
            return $message->sender_id === auth()->id() ? $message->receiver_id : $message->sender_id;
        }) as $userId => $conversation)
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
                            <a href="{{ route('admin.chat.show', $otherUser->id) }}"
                                class="chat-list-item block hover:bg-gray-50">
                                <div class="flex items-center space-x-3 sm:space-x-4">
                                    <div class="avatar">
                                        {{ substr($otherUser->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm sm:text-base font-medium text-gray-900 truncate">
                                                {{ $otherUser->name }}
                                            </p>
                                            <span class="text-xs sm:text-sm text-gray-500">
                                                {{ $lastMessage->created_at->diffForHumans(['short' => true]) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between mt-1">
                                            <p
                                                class="text-xs sm:text-sm text-gray-500 truncate max-w-[200px] sm:max-w-[300px]">
                                                {{ $lastMessage->message }}
                                            </p>
                                            @if ($unreadCount > 0)
                                                <span class="unread-badge ml-2">{{ $unreadCount }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .chat-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .chat-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
        }

        .chat-header {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-list-item {
            padding: 0.75rem 1rem;
            transition: all 0.2s;
        }

        @media (min-width: 640px) {
            .chat-list-item {
                padding: 1rem 1.5rem;
            }
        }

        .avatar {
            width: 2.5rem;
            height: 2.5rem;
            background-color: #4f46e5;
            color: white;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        @media (min-width: 640px) {
            .avatar {
                width: 3rem;
                height: 3rem;
                font-size: 1rem;
            }
        }

        .new-chat-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            background-color: #4f46e5;
            color: white;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        @media (min-width: 640px) {
            .new-chat-btn {
                padding: 0.5rem 1rem;
            }
        }

        .new-chat-btn:hover {
            background-color: #4338ca;
        }

        .unread-badge {
            background-color: #4f46e5;
            color: white;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        @media (min-width: 640px) {
            .unread-badge {
                padding: 0.25rem 0.75rem;
            }
        }
    </style>
@endsection
