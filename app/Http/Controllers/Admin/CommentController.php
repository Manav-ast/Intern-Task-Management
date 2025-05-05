<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $comment = new Comment([
            'task_id' => $task->id,
            'message' => $request->message
        ]);

        $comment->commentable()->associate(auth('admin')->user());
        $comment->save();

        return response()->json([
            'id' => $comment->id,
            'message' => $comment->message,
            'created_at' => $comment->created_at->diffForHumans(),
            'author' => auth('admin')->user()->name
        ]);
    }

    public function destroy(Task $task, Comment $comment)
    {
        if ($comment->commentable_id !== auth('admin')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
