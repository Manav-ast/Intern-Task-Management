<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Task $task)
    {
        try {
            $this->authorize('create-comments');
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
        } catch (\Exception $e) {
            Log::error('Error creating comment: ' . $e->getMessage());
            return response()->json(['error' => 'Error creating comment. Please try again.'], 500);
        }
    }

    public function destroy(Task $task, Comment $comment)
    {
        try {
            $this->authorize('delete-comments');
            if ($comment->commentable_id !== auth('admin')->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $comment->delete();
            return response()->json(['message' => 'Comment deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting comment: ' . $e->getMessage());
            return response()->json(['error' => 'Error deleting comment. Please try again.'], 500);
        }
    }
}
