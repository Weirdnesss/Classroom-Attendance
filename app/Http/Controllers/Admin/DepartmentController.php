<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount(['programs', 'subjects', 'teachers'])->latest()->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:departments,code',
            'name' => 'required|string',
        ]);

        Department::create($request->only('code', 'name'));

        return redirect()->route('admin.departments.index')
                         ->with('success', 'Department created.');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'code'      => 'required|string|unique:departments,code,' . $department->id,
            'name'      => 'required|string',
            'is_active' => 'boolean',
        ]);

        $department->update($request->only('code', 'name') + [
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.departments.index')
                         ->with('success', 'Department updated.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('admin.departments.index')
                         ->with('success', 'Department deleted.');
    }
}