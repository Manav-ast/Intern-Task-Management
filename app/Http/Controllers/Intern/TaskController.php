<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = auth('intern')->user()->tasks()
            ->with(['admin', 'comments'])
            ->latest()
            ->paginate(10);

        return view('intern.tasks.index', compact('tasks'));
    }

    public function show(Task $task)
    {
        // Check if the intern is assigned to this task
        if (!$task->interns->contains(auth('intern')->id())) {
            abort(403, 'You are not authorized to view this task.');
        }

        return view('intern.tasks.show', compact('task'));
    }

    public function addComment(Request $request, Task $task)
    {
        try {
            // Verify the intern is assigned to this task
            if (!$task->interns->contains(auth('intern')->id())) {
                return response()->json([
                    'message' => 'You are not authorized to comment on this task.'
                ], 403);
            }

            // Validate the request
            $validated = $request->validate([
                'message' => 'required|string|max:1000'
            ], [
                'message.required' => 'Please enter a comment message.',
                'message.max' => 'Comment message cannot be longer than 1000 characters.'
            ]);

            $intern = auth('intern')->user();

            // Create the comment
            $comment = $task->comments()->create([
                'message' => $validated['message'],
                'commentable_id' => $intern->id,
                'commentable_type' => get_class($intern)
            ]);

            // Return success response
            return response()->json([
                'id' => $comment->id,
                'message' => $comment->message,
                'author' => $intern->name,
                'created_at' => $comment->created_at->diffForHumans(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating comment: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while posting your comment. Please try again.'
            ], 500);
        }
    }
}
