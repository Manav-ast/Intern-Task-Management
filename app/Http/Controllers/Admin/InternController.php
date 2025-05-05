<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use Illuminate\Http\Request;
use App\Http\Requests\StoreInternRequest;
use App\Http\Requests\UpdateInternRequest;
use Illuminate\Support\Facades\Hash;

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
        $intern->delete();
        return redirect()->route('admin.interns.index')->with('success', 'Intern deleted.');
    }
}
