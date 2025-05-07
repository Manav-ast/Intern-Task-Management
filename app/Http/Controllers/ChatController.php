<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Admin;
use App\Models\Intern;
use App\Events\ChatMessageEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

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

            $viewPrefix = $user instanceof Admin ? 'admin' : 'intern';
            return view($viewPrefix . '.chat.index', compact('messages'));
        } catch (\Exception $e) {
            Log::error('Error loading chat index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading chat messages. Please try again.');
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::user();
            $otherUser = $user instanceof Admin
                ? Intern::findOrFail($id)
                : Admin::findOrFail($id);

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

            $viewPrefix = $user instanceof Admin ? 'admin' : 'intern';
            return view($viewPrefix . '.chat.show', compact('messages', 'otherUser'));
        } catch (\Exception $e) {
            Log::error('Error loading chat conversation: ' . $e->getMessage());
            $routePrefix = $user instanceof Admin ? 'admin.chat' : 'intern.chat';
            return redirect()->route($routePrefix . '.index')
                ->with('error', 'Error loading chat conversation. Please try again.');
        }
    }

    public function store(Request $request, $id)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000',
            ]);

            $user = Auth::user();
            $otherUser = $user instanceof Admin
                ? Intern::findOrFail($id)
                : Admin::findOrFail($id);

            // Begin transaction to ensure atomicity
            DB::beginTransaction();

            $message = Message::create([
                'message' => $request->message,
                'sender_id' => $user->id,
                'sender_type' => get_class($user),
                'receiver_id' => $otherUser->id,
                'receiver_type' => get_class($otherUser),
            ]);

            $messageData = [
                'id' => $message->id,
                'message' => $message->message,
                'created_at' => $message->created_at,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id
            ];

            // Broadcast event only once and only to others
            broadcast(new ChatMessageEvent($messageData))->toOthers();

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($messageData);
            }

            return redirect()->back()->with('success', 'Message sent successfully.');
        } catch (ValidationException $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => $e->errors()['message'][0] ?? 'Invalid message'
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error sending message: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'Server error occurred while sending message'
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to send message. Please try again.');
        }
    }

    public function getUsers()
    {
        try {
            $user = Auth::user();
            $users = $user instanceof Admin ? Intern::paginate(20) : Admin::paginate(20);

            $viewPrefix = $user instanceof Admin ? 'admin' : 'intern';
            return view($viewPrefix . '.chat.users', compact('users'));
        } catch (\Exception $e) {
            Log::error('Error loading chat users: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading users. Please try again.');
        }
    }
}
