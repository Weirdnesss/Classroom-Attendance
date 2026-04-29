<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicTermController extends Controller
{
    public function index()
    {
        $terms = AcademicTerm::with('academicYear')->latest()->get();
        return view('admin.terms.index', compact('terms'));
    }

    public function create()
    {
        $years = AcademicYear::orderBy('label')->get();
        return view('admin.terms.create', compact('years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'label'            => 'required|string',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
        ]);

        AcademicTerm::create($request->only(
            'academic_year_id', 'label', 'start_date', 'end_date'
        ));

        return redirect()->route('admin.terms.index')
                         ->with('success', 'Term created.');
    }

    public function edit(AcademicTerm $term)
    {
        $years = AcademicYear::orderBy('label')->get();
        return view('admin.terms.edit', compact('term', 'years'));
    }

    public function update(Request $request, AcademicTerm $term)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'label'            => 'required|string',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
            'is_active'        => 'boolean',
        ]);

        if ($request->boolean('is_active')) {
            AcademicTerm::where('id', '!=', $term->id)
                        ->update(['is_active' => false]);
        }

        $term->update($request->only('academic_year_id', 'label', 'start_date', 'end_date') + [
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.terms.index')
                         ->with('success', 'Term updated.');
    }

    public function destroy(AcademicTerm $term)
    {
        $term->delete();
        return redirect()->route('admin.terms.index')
                         ->with('success', 'Term deleted.');
    }
}