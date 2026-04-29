<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Department;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with('department')->latest()->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.subjects.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'code'          => 'required|string|unique:subjects,code',
            'name'          => 'required|string',
            'units'         => 'required|integer|min:1|max:6',
        ]);

        Subject::create($request->only('department_id', 'code', 'name', 'units'));

        return redirect()->route('admin.subjects.index')
                         ->with('success', 'Subject created.');
    }

    public function edit(Subject $subject)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.subjects.edit', compact('subject', 'departments'));
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'code'          => 'required|string|unique:subjects,code,' . $subject->id,
            'name'          => 'required|string',
            'units'         => 'required|integer|min:1|max:6',
            'is_active'     => 'boolean',
        ]);

        $subject->update($request->only('department_id', 'code', 'name', 'units') + [
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.subjects.index')
                         ->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('admin.subjects.index')
                         ->with('success', 'Subject deleted.');
    }
}