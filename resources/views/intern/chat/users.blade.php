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
            align-items: center;
            justify-content: space-between;
        }

        .user-list {
            padding: 1rem 0;
        }

        .user-item {
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .user-item:last-child {
            border-bottom: none;
        }

        .user-item:hover {
            background-color: #f9fafb;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background-color: #059669;
            color: white;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .back-button {
            padding: 0.5rem;
            color: #4f46e5;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }

        .back-button:hover {
            background-color: #f3f4f6;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination .page-item {
            list-style: none;
        }

        .pagination .page-link {
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            color: #4f46e5;
            background-color: white;
            transition: all 0.2s;
        }

        .pagination .page-link:hover {
            background-color: #f3f4f6;
        }

        .pagination .page-item.active .page-link {
            background-color: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .pagination .page-item.disabled .page-link {
            color: #9ca3af;
            pointer-events: none;
        }
    </style>

    <div class="py-6">
        <div class="chat-container">
            <div class="chat-card">
                <div class="chat-header">
                    <div class="flex items-center">
                        <a href="{{ route('intern.chat.index') }}" class="back-button mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </a>
                        <h2 class="text-xl font-semibold text-gray-800">Start New Chat</h2>
                    </div>
                </div>

                @if ($users->isEmpty())
                    <div class="p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No users available</h3>
                        <p class="mt-1 text-sm text-gray-500">There are no admins available to chat with at the moment.</p>
                    </div>
                @else
                    <div class="user-list">
                        @foreach ($users as $user)
                            <a href="{{ route('intern.chat.show', $user->id) }}" class="user-item hover:bg-gray-50">
                                <div class="avatar mr-4">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-500">
                                        {{ $user instanceof \App\Models\Admin ? 'Admin' : 'Intern' }}</p>
                                </div>
                                <div class="ml-4">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    @if ($users->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $users->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
