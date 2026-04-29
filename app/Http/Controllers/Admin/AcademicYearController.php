<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::latest()->get();
        return view('admin.academic-years.index', compact('years'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'label'      => 'required|string|unique:academic_years,label',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        AcademicYear::create($request->only('label', 'start_date', 'end_date'));

        return redirect()->route('admin.academic-years.index')
                         ->with('success', 'Academic year created.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'label'      => 'required|string|unique:academic_years,label,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
            'is_active'  => 'boolean',
        ]);

        // Only one active year at a time
        if ($request->boolean('is_active')) {
            AcademicYear::where('id', '!=', $academicYear->id)
                        ->update(['is_active' => false]);
        }

        $academicYear->update($request->only('label', 'start_date', 'end_date') + [
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.academic-years.index')
                         ->with('success', 'Academic year updated.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return redirect()->route('admin.academic-years.index')
                         ->with('success', 'Academic year deleted.');
    }
}