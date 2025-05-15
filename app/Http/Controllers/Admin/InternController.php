<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use Illuminate\Http\Request;
use App\Http\Requests\StoreInternRequest;
use App\Http\Requests\UpdateInternRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InternController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        try {
            $interns = Intern::latest()->paginate(10);
            return view('admin.interns.index', compact('interns'));
        } catch (\Exception $e) {
            Log::error('Error loading interns: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading interns. Please try again.');
        }
    }

    public function create()
    {
        try {
            $this->authorize('create-interns');
            return view('admin.interns.create');
        } catch (\Exception $e) {
            Log::error('Error loading create intern form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading create form. Please try again.');
        }
    }

    public function store(StoreInternRequest $request)
    {
        try {
            $this->authorize('create-interns');
            $validatedData = $request->validated();
            $validatedData['password'] = Hash::make($validatedData['password']);

            Intern::create($validatedData);
            return redirect()->route('admin.interns.index')->with('success', 'Intern created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating intern: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating intern. Please try again.')
                ->withInput();
        }
    }

    public function edit(Intern $intern)
    {
        try {
            $this->authorize('edit-interns');
            return view('admin.interns.edit', compact('intern'));
        } catch (\Exception $e) {
            Log::error('Error loading edit intern form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading edit form. Please try again.');
        }
    }

    public function update(StoreInternRequest $request, Intern $intern)
    {
        try {
            $this->authorize('edit-interns');
            $validatedData = $request->validated();

            // Only update password if it's provided
            if (empty($validatedData['password'])) {
                unset($validatedData['password']);
            } else {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            $intern->update($validatedData);
            return redirect()->route('admin.interns.index')->with('success', 'Intern updated.');
        } catch (\Exception $e) {
            Log::error('Error updating intern: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating intern. Please try again.')
                ->withInput();
        }
    }

    public function destroy(Intern $intern)
    {
        try {
            // Log the attempt with details
            Log::info('Attempting to delete intern', [
                'intern_id' => $intern->id,
                'intern_name' => $intern->name,
                'admin_id' => admin_id(),
                'admin_name' => admin() ? admin()->name : 'Not authenticated'
            ]);

            // Check authorization
            try {
                $this->authorize('delete-interns');
                Log::info('Admin has delete-interns permission');
            } catch (\Exception $authException) {
                Log::error('Authorization error: ' . $authException->getMessage());
                return response()->json([
                    'error' => 'You do not have permission to delete interns.'
                ], 403);
            }

            // Check if intern has any associated tasks
            $tasks = $intern->tasks;
            $tasksCount = $tasks->count();
            Log::info('Intern tasks count: ' . $tasksCount);

            if ($tasksCount > 0) {
                // Get task info for logging
                $taskInfo = $tasks->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'status' => $task->status
                    ];
                });

                Log::warning('Cannot delete intern with assigned tasks', [
                    'intern_id' => $intern->id,
                    'tasks_count' => $tasksCount,
                    'tasks' => $taskInfo
                ]);

                // Include the first few task titles in the message to help the admin
                $taskTitles = $tasks->take(3)->pluck('title')->implode('", "');
                $additionalInfo = $tasksCount > 3 ? " and " . ($tasksCount - 3) . " more" : "";

                $errorMessage = "Cannot delete intern with assigned tasks. Please unassign the following tasks first: \"$taskTitles\"$additionalInfo.";

                return response()->json([
                    'error' => $errorMessage,
                    'taskCount' => $tasksCount
                ], 422);
            }

            // Attempt to delete the intern
            $deleteResult = $intern->delete();
            Log::info('Delete operation result: ' . ($deleteResult ? 'Success' : 'Failed'));

            if (!$deleteResult) {
                throw new \Exception('Failed to delete intern record');
            }

            return response()->json(['message' => 'Intern deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting intern: ' . $e->getMessage(), [
                'intern_id' => $intern->id ?? 'unknown',
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to delete intern. Please try again.'
            ], 500);
        }
    }
}
