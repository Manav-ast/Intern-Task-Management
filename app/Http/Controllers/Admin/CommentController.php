<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Task $task)
    {
        DB::beginTransaction();
        try {
            // Check if user is authenticated
            if (!Auth::guard('admin')->check()) {
                throw new \Exception('User not authenticated');
            }

            // Log the attempt
            Log::info('Attempting to create comment', [
                'task_id' => $task->id,
                'admin_id' => auth('admin')->id()
            ]);

            $this->authorize('create-comments');

            // Validate the request
            $validated = $request->validate([
                'message' => 'required|string|max:1000'
            ], [
                'message.required' => 'Please enter a comment message.',
                'message.max' => 'Comment message cannot be longer than 1000 characters.'
            ]);

            // Create and save the comment
            $comment = new Comment([
                'task_id' => $task->id,
                'message' => $validated['message']
            ]);

            $comment->commentable()->associate(auth('admin')->user());

            if (!$comment->save()) {
                throw new \Exception('Failed to save comment');
            }

            DB::commit();

            Log::info('Comment created successfully', [
                'comment_id' => $comment->id,
                'task_id' => $task->id
            ]);

            return response()->json([
                'id' => $comment->id,
                'message' => $comment->message,
                'created_at' => $comment->created_at->diffForHumans(),
                'author' => auth('admin')->user()->name,
                'deleteUrl' => route('admin.tasks.comments.destroy', [$task, $comment])
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            DB::rollBack();
            Log::error('Authorization error creating comment: ' . $e->getMessage());
            return response()->json(['error' => 'You are not authorized to create comments.'], 403);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Comment validation failed: ' . json_encode($e->errors()));
            return response()->json(['error' => $e->errors()['message'][0] ?? 'Invalid comment data'], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating comment: ' . $e->getMessage(), [
                'task_id' => $task->id,
                'admin_id' => auth('admin')->id() ?? 'not authenticated',
                'exception' => get_class($e)
            ]);
            return response()->json(['error' => 'Error creating comment. Please try again.'], 500);
        }
    }

    public function destroy(Task $task, Comment $comment)
    {
        DB::beginTransaction();
        try {
            // Check if user is authenticated
            if (!Auth::guard('admin')->check()) {
                throw new \Exception('User not authenticated');
            }

            // Log the attempt
            Log::info('Attempting to delete comment', [
                'comment_id' => $comment->id,
                'task_id' => $task->id,
                'admin_id' => auth('admin')->id()
            ]);

            $this->authorize('delete-comments');

            // Check if the comment belongs to the authenticated admin
            if ($comment->commentable_id !== auth('admin')->id()) {
                throw new \Illuminate\Auth\Access\AuthorizationException('You can only delete your own comments.');
            }

            // Check if the comment belongs to the task
            if ($comment->task_id !== $task->id) {
                throw new \Illuminate\Auth\Access\AuthorizationException('Comment does not belong to this task');
            }

            if (!$comment->delete()) {
                throw new \Exception('Failed to delete comment');
            }

            DB::commit();

            Log::info('Comment deleted successfully', [
                'comment_id' => $comment->id,
                'task_id' => $task->id
            ]);

            return response()->json(['message' => 'Comment deleted successfully']);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            DB::rollBack();
            Log::error('Authorization error deleting comment: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting comment: ' . $e->getMessage(), [
                'comment_id' => $comment->id,
                'task_id' => $task->id,
                'admin_id' => auth('admin')->id() ?? 'not authenticated',
                'exception' => get_class($e)
            ]);
            return response()->json(['error' => 'Error deleting comment. Please try again.'], 500);
        }
    }
}
