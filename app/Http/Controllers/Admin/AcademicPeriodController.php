<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;

class AcademicPeriodController extends Controller
{
    public function index()
    {
        $periods = AcademicPeriod::with('term.academicYear')->latest()->get();
        return view('admin.periods.index', compact('periods'));
    }

    public function create()
    {
        $terms = AcademicTerm::with('academicYear')->orderBy('label')->get();
        return view('admin.periods.create', compact('terms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_term_id' => 'required|exists:academic_terms,id',
            'label'            => 'required|string',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
        ]);

        AcademicPeriod::create($request->only(
            'academic_term_id', 'label', 'start_date', 'end_date'
        ));

        return redirect()->route('admin.periods.index')
                         ->with('success', 'Period created.');
    }

    public function edit(AcademicPeriod $period)
    {
        $terms = AcademicTerm::with('academicYear')->orderBy('label')->get();
        return view('admin.periods.edit', compact('period', 'terms'));
    }

    public function update(Request $request, AcademicPeriod $period)
    {
        $request->validate([
            'academic_term_id' => 'required|exists:academic_terms,id',
            'label'            => 'required|string',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
        ]);

        $period->update($request->only(
            'academic_term_id', 'label', 'start_date', 'end_date'
        ));

        return redirect()->route('admin.periods.index')
                         ->with('success', 'Period updated.');
    }

    public function destroy(AcademicPeriod $period)
    {
        $period->delete();
        return redirect()->route('admin.periods.index')
                         ->with('success', 'Period deleted.');
    }
}