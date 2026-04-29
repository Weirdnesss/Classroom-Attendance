<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Department;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::with('department')->withCount('students')->latest()->get();
        return view('admin.programs.index', compact('programs'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.programs.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'code'          => 'required|string|unique:programs,code',
            'name'          => 'required|string',
            'years'         => 'required|integer|min:1|max:6',
        ]);

        Program::create($request->only('department_id', 'code', 'name', 'years'));

        return redirect()->route('admin.programs.index')
                         ->with('success', 'Program created.');
    }

    public function edit(Program $program)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.programs.edit', compact('program', 'departments'));
    }

    public function update(Request $request, Program $program)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'code'          => 'required|string|unique:programs,code,' . $program->id,
            'name'          => 'required|string',
            'years'         => 'required|integer|min:1|max:6',
            'is_active'     => 'boolean',
        ]);

        $program->update($request->only('department_id', 'code', 'name', 'years') + [
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.programs.index')
                         ->with('success', 'Program updated.');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->route('admin.programs.index')
                         ->with('success', 'Program deleted.');
    }
}