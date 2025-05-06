@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">

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
                            <div class="message-sender">
                                @if ($message->sender_id !== auth()->id())
                                    <div class="flex items-center">
                                        <span class="mr-2">{{ $message->sender->name }}</span>
                                        <span class="sender-role intern">Intern</span>
                                    </div>
                                @else
                                    <div class="flex items-center">
                                        <span class="mr-2">{{ auth()->user()->name }}</span>
                                        <span class="sender-role admin">Admin</span>
                                    </div>
                                @endif
                            </div>
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
        @vite('resources/js/chat.js')
    @endpush
@endsection
