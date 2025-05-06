<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Admin;
use App\Models\Intern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $messages = Message::where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->where('sender_type', get_class($user));
            })->orWhere(function ($query) use ($user) {
                $query->where('receiver_id', $user->id)
                    ->where('receiver_type', get_class($user));
            })
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $view = $user instanceof Admin ? 'admin.chat.index' : 'intern.chat.index';
            return view($view, compact('messages'));
        } catch (\Exception $e) {
            Log::error('Error loading chat index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading chat messages. Please try again.');
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::user();
            $otherUser = $user instanceof Admin ? Intern::findOrFail($id) : Admin::findOrFail($id);

            $messages = Message::where(function ($query) use ($user, $otherUser) {
                $query->where('sender_id', $user->id)
                    ->where('sender_type', get_class($user))
                    ->where('receiver_id', $otherUser->id)
                    ->where('receiver_type', get_class($otherUser));
            })->orWhere(function ($query) use ($user, $otherUser) {
                $query->where('sender_id', $otherUser->id)
                    ->where('sender_type', get_class($otherUser))
                    ->where('receiver_id', $user->id)
                    ->where('receiver_type', get_class($user));
            })
                ->with(['sender', 'receiver'])
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark unread messages as read
            $messages->where('receiver_id', $user->id)
                ->where('receiver_type', get_class($user))
                ->whereNull('read_at')
                ->each->markAsRead();

            $view = $user instanceof Admin ? 'admin.chat.show' : 'intern.chat.show';
            return view($view, compact('messages', 'otherUser'));
        } catch (\Exception $e) {
            Log::error('Error loading chat conversation: ' . $e->getMessage());
            return redirect()->route('chat.index')->with('error', 'Error loading chat conversation. Please try again.');
        }
    }

    public function store(Request $request, $id)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            $user = Auth::user();
            $otherUser = $user instanceof Admin ? Intern::findOrFail($id) : Admin::findOrFail($id);

            $message = Message::create([
                'message' => $request->message,
                'sender_id' => $user->id,
                'sender_type' => get_class($user),
                'receiver_id' => $otherUser->id,
                'receiver_type' => get_class($otherUser),
            ]);

            return redirect()->back()->with('success', 'Message sent successfully.');
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error sending message. Please try again.')->withInput();
        }
    }

    public function getUsers()
    {
        try {
            $user = Auth::user();

            if ($user instanceof Admin) {
                $users = Intern::all();
            } else {
                $users = Admin::all();
            }

            $view = $user instanceof Admin ? 'admin.chat.users' : 'chat.users';
            return view($view, compact('users'));
        } catch (\Exception $e) {
            Log::error('Error loading chat users: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading users. Please try again.');
        }
    }
}
