<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use Illuminate\Http\Request;
use App\Http\Requests\StoreInternRequest;
use App\Http\Requests\UpdateInternRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class InternController extends Controller
{
    public function index()
    {
        $interns = Intern::latest()->paginate(10);
        return view('admin.interns.index', compact('interns'));
    }

    public function create()
    {
        return view('admin.interns.create');
    }

    public function store(StoreInternRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);

        Intern::create($validatedData);
        return redirect()->route('admin.interns.index')->with('success', 'Intern created successfully.');
    }

    public function edit(Intern $intern)
    {
        return view('admin.interns.edit', compact('intern'));
    }

    public function update(StoreInternRequest $request, Intern $intern)
    {
        $validatedData = $request->validated();

        // Only update password if it's provided
        if (empty($validatedData['password'])) {
            unset($validatedData['password']);
        } else {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $intern->update($validatedData);
        return redirect()->route('admin.interns.index')->with('success', 'Intern updated.');
    }

    public function destroy(Intern $intern)
    {
        try {
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
