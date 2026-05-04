<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('program.department', 'user')->latest()->get();
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $programs = Program::with('department')->where('is_active', true)->orderBy('code')->get();
        return view('admin.students.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'   => 'required|string',
            'last_name'    => 'required|string',
            'middle_name'  => 'nullable|string',
            'student_id'   => 'required|string|unique:students,student_id',
            'program_id'   => 'required|exists:programs,id',
            'year_level'   => 'required|integer|min:1|max:6',
            'is_irregular' => 'boolean'
        ]);

        DB::transaction(function () use ($request) {
            $userId = null;

            if ($request->filled('email')) {
                $user = User::create([
                    'name'     => $request->first_name . ' ' . $request->last_name,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password),
                    'role'     => 'student',
                ]);
                $userId = $user->id;
            }

            Student::create([
                'user_id'      => $userId,
                'program_id'   => $request->program_id,
                'student_id'   => $request->student_id,
                'first_name'   => $request->first_name,
                'last_name'    => $request->last_name,
                'middle_name'  => $request->middle_name,
                'year_level'   => $request->year_level,
                'is_irregular' => $request->boolean('is_irregular'),
            ]);
        });

    return redirect()->route('admin.students.index')
                     ->with('success', 'Student created.');

    return redirect()->route('admin.students.index')
                    ->with('success', 'Student created.');
    }

    public function edit(Student $student)
    {
        $programs = Program::with('department')->where('is_active', true)->orderBy('code')->get();
        return view('admin.students.edit', compact('student', 'programs'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'first_name'   => 'required|string',
            'last_name'    => 'required|string',
            'middle_name'  => 'nullable|string',
            'student_id'   => 'required|string|unique:students,student_id,' . $student->id,
            'program_id'   => 'required|exists:programs,id',
            'year_level'   => 'required|integer|min:1|max:6',
            'is_irregular' => 'boolean',
            'is_active'    => 'boolean',
        ]);

        DB::transaction(function () use ($request, $student) {

            $student->update([
                'program_id'   => $request->program_id,
                'student_id'   => $request->student_id,
                'first_name'   => $request->first_name,
                'last_name'    => $request->last_name,
                'middle_name'  => $request->middle_name,
                'year_level'   => $request->year_level,
                'is_irregular' => $request->boolean('is_irregular'),
                'is_active'    => $request->boolean('is_active'),
            ]);
        });

        return redirect()->route('admin.students.index')
                        ->with('success', 'Student updated.');
    }

    public function destroy(Student $student)
    {
        DB::transaction(function () use ($student) {
            $student->user->delete();
            $student->delete();
        });

        return redirect()->route('admin.students.index')
                         ->with('success', 'Student deleted.');
    }
}