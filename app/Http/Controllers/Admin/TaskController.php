<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Intern;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        try {
            $tasks = Task::with('interns', 'admin')->latest()->paginate(10);
            return view('admin.tasks.index', compact('tasks'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading tasks: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $this->authorize('create-tasks');
            $interns = Intern::all();
            return view('admin.tasks.create', compact('interns'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error accessing create task page: ' . $e->getMessage());
        }
    }

    public function show(Task $task)
    {
        try {
            $this->authorize('view-tasks');
            $task->load(['interns', 'comments.commentable']);
            return view('admin.tasks.show', compact('task'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error viewing task: ' . $e->getMessage());
        }
    }

    public function store(StoreTaskRequest $request)
    {
        try {
            $this->authorize('create-tasks');
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'due_date' => $request->due_date,
                'created_by' => auth('admin')->id(),
            ]);

            $task->interns()->attach($request->interns);

            return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating task: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Task $task)
    {
        try {
            $this->authorize('edit-tasks');
            $interns = Intern::all();
            $selectedInterns = $task->interns->pluck('id')->toArray();
            return view('admin.tasks.edit', compact('task', 'interns', 'selectedInterns'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error accessing edit task page: ' . $e->getMessage());
        }
    }

    public function update(StoreTaskRequest $request, Task $task)
    {
        $this->authorize('edit-tasks');
        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
        ]);

        $task->interns()->sync($request->interns);

        return redirect()->route('admin.tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        try {
            $this->authorize('delete-tasks');

            // Check if task has any comments
            if ($task->comments()->count() > 0) {
                // Delete associated comments first
                $task->comments()->delete();
            }

            // Delete task
            $task->delete();

            if (request()->wantsJson()) {
                return response()->json(['message' => 'Task deleted successfully']);
            }

            return redirect()->route('admin.tasks.index')->with('success', 'Task deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Error deleting task: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'error' => 'Failed to delete task. Please try again.'
                ], 500);
            }

            return redirect()->route('admin.tasks.index')->with('error', 'Failed to delete task');
        }
    }
}
