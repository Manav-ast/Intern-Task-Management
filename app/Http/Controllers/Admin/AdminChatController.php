<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Intern;
use App\Events\NewMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminChatController extends Controller
{
    public function index()
    {
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
    }

    public function show($id)
    {
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
    }

    public function store(Request $request, $id)
    {
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

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $message->load('sender'),
            ]);
        }

        return redirect()->back()->with('success', 'Message sent successfully.');
    }

    public function getUsers()
    {
        $users = Intern::paginate(20);
        return view('admin.chat.users', compact('users'));
    }
}
