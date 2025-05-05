<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Intern;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('interns', 'admin')->latest()->paginate(10);
        return view('admin.tasks.index', compact('tasks'));
    }

    public function create()
    {
        $interns = Intern::all();
        return view('admin.tasks.create', compact('interns'));
    }

    public function show(Task $task)
    {
        $task->load(['interns', 'comments.commentable']);
        return view('admin.tasks.show', compact('task'));
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'created_by' => auth('admin')->id(),
        ]);

        $task->interns()->attach($request->interns);

        return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $interns = Intern::all();
        $selectedInterns = $task->interns->pluck('id')->toArray();
        return view('admin.tasks.edit', compact('task', 'interns', 'selectedInterns'));
    }

    public function update(StoreTaskRequest $request, Task $task)
    {
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
            $task->delete();
            return redirect()->route('admin.tasks.index')->with('success', 'Task deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.tasks.index')->with('error', 'Failed to delete task');
        }
    }
}
