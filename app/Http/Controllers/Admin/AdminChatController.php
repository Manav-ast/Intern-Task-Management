<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Intern;
use App\Events\NewMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AdminChatController extends Controller
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

            return view('admin.chat.index', compact('messages'));
        } catch (\Exception $e) {
            Log::error('Error loading chat index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading chat messages. Please try again.');
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::user();
            $otherUser = Intern::findOrFail($id);

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

            return view('admin.chat.show', compact('messages', 'otherUser'));
        } catch (\Exception $e) {
            Log::error('Error loading chat conversation: ' . $e->getMessage());
            return redirect()->route('admin.chat.index')->with('error', 'Error loading chat conversation. Please try again.');
        }
    }

    public function store(Request $request, $id)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            $user = Auth::user();
            $otherUser = Intern::findOrFail($id);

            $message = Message::create([
                'message' => $request->message,
                'sender_id' => $user->id,
                'sender_type' => get_class($user),
                'receiver_id' => $otherUser->id,
                'receiver_type' => get_class($otherUser),
            ]);

            broadcast(new NewMessage($message))->toOthers();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $message->load(['sender', 'receiver']),
                ]);
            }

            return redirect()->back()->with('success', 'Message sent successfully.');
        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->errors()['message'][0] ?? 'Invalid message',
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send message. Please try again.',
                ], 500);
            }
            return redirect()->back()->with('error', 'Error sending message. Please try again.')->withInput();
        }
    }

    public function getUsers()
    {
        try {
            $users = Intern::paginate(20);
            return view('admin.chat.users', compact('users'));
        } catch (\Exception $e) {
            Log::error('Error loading chat users: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading users. Please try again.');
        }
    }
}
