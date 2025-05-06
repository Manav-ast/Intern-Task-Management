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
            $this->authorize('delete-interns');

            // Check if intern has any associated tasks
            if ($intern->tasks()->count() > 0) {
                return response()->json([
                    'error' => 'Cannot delete intern with assigned tasks. Please unassign tasks first.'
                ], 422);
            }

            $intern->delete();
            return response()->json(['message' => 'Intern deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting intern: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete intern. Please try again.'
            ], 500);
        }
    }
}
