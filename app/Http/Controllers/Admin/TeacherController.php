<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('department', 'user')->latest()->get();
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.teachers.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
            'middle_name'   => 'nullable|string',
            'employee_id'   => 'required|string|unique:teachers,employee_id',
            'department_id' => 'required|exists:departments,id',
            'rfid_tag'      => 'nullable|string|unique:teachers,rfid_tag',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->first_name . ' ' . $request->last_name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'teacher',
            ]);

            Teacher::create([
                'user_id'       => $user->id,
                'department_id' => $request->department_id,
                'employee_id'   => $request->employee_id,
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'middle_name'   => $request->middle_name,
                'rfid_tag'      => $request->rfid_tag,
            ]);
        });

        return redirect()->route('admin.teachers.index')
                         ->with('success', 'Teacher created.');
    }

    public function edit(Teacher $teacher)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.teachers.edit', compact('teacher', 'departments'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
            'middle_name'   => 'nullable|string',
            'employee_id'   => 'required|string|unique:teachers,employee_id,' . $teacher->id,
            'department_id' => 'required|exists:departments,id',
            'rfid_tag'      => 'nullable|string|unique:teachers,rfid_tag,' . $teacher->id,
            'email'         => 'required|email|unique:users,email,' . $teacher->user_id,
            'password'      => 'nullable|min:6',
            'is_active'     => 'boolean',
        ]);

        DB::transaction(function () use ($request, $teacher) {
            $userData = [
                'name'  => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $teacher->user->update($userData);

            $teacher->update([
                'department_id' => $request->department_id,
                'employee_id'   => $request->employee_id,
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'middle_name'   => $request->middle_name,
                'rfid_tag'      => $request->rfid_tag,
                'is_active'     => $request->boolean('is_active'),
            ]);
        });

        return redirect()->route('admin.teachers.index')
                         ->with('success', 'Teacher updated.');
    }

    public function destroy(Teacher $teacher)
    {
        DB::transaction(function () use ($teacher) {
            $teacher->user->delete();
            $teacher->delete();
        });

        return redirect()->route('admin.teachers.index')
                         ->with('success', 'Teacher deleted.');
    }
}