@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">

    <div class="py-3 sm:py-6">
        <div class="chat-container">
            <div class="chat-card">
                <div class="chat-header">
                    <div class="flex items-center">
                        <a href="{{ route('admin.chat.index') }}" class="back-button">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </a>
                        <h2 class="text-base sm:text-xl font-semibold text-gray-900 ml-3 sm:ml-4">New Chat</h2>
                    </div>
                </div>

                @if ($users->isEmpty())
                    <div class="p-6 sm:p-8 text-center">
                        <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm sm:text-base font-medium text-gray-900">No interns available</h3>
                        <p class="mt-1 text-xs sm:text-sm text-gray-500">There are no interns available to chat with at the
                            moment.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <a href="{{ route('admin.chat.show', $user->id) }}" class="user-item hover:bg-gray-50">
                                <div class="flex items-center space-x-3 sm:space-x-4">
                                    <div class="avatar">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm sm:text-base font-medium text-gray-900 truncate">
                                            {{ $user->name }}</h3>
                                        <p class="text-xs sm:text-sm text-gray-500 truncate">Intern</p>
                                    </div>
                                    <div class="flex items-center text-gray-400">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if ($users->hasPages())
                        <div class="px-4 py-3 sm:px-6 sm:py-4 border-t border-gray-200">
                            {{ $users->links() }}
                        </div>
                    @endif
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
        }

        @media (min-width: 640px) {
            .chat-header {
                padding: 1rem 1.5rem;
            }
        }

        .back-button {
            padding: 0.375rem;
            color: #4f46e5;
            border-radius: 0.375rem;
            transition: all 0.2s;
            touch-action: manipulation;
        }

        @media (min-width: 640px) {
            .back-button {
                padding: 0.5rem;
            }
        }

        .back-button:hover {
            background-color: #f3f4f6;
        }

        .user-item {
            padding: 0.75rem 1rem;
            transition: all 0.2s;
        }

        @media (min-width: 640px) {
            .user-item {
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
    </style>
@endsection
