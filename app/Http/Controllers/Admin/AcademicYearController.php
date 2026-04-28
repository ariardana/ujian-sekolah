<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        $years = AcademicYear::with('semesters')->orderByDesc('year')->paginate(15);

        return view('admin.academic_years.index', compact('years'));
    }

    public function create(): View
    {
        return view('admin.academic_years.form', ['year' => new AcademicYear]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'year' => ['required', 'string', 'max:20', 'unique:academic_years,year'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($data) {
            $isActive = (bool) ($data['is_active'] ?? false);
            if ($isActive) {
                AcademicYear::query()->update(['is_active' => false]);
            }
            $year = AcademicYear::create(['year' => $data['year'], 'is_active' => $isActive]);
            foreach (['Ganjil', 'Genap'] as $name) {
                Semester::create([
                    'academic_year_id' => $year->id,
                    'name' => $name,
                    'is_active' => false,
                ]);
            }
        });

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran ditambahkan.');
    }

    public function edit(AcademicYear $academicYear): View
    {
        $academicYear->load('semesters');

        return view('admin.academic_years.form', ['year' => $academicYear]);
    }

    public function update(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $data = $request->validate([
            'year' => ['required', 'string', 'max:20', 'unique:academic_years,year,'.$academicYear->id],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($data, $academicYear) {
            $isActive = (bool) ($data['is_active'] ?? false);
            if ($isActive) {
                AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
            }
            $academicYear->update(['year' => $data['year'], 'is_active' => $isActive]);
        });

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun ajaran diperbarui.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        $academicYear->delete();

        return back()->with('success', 'Tahun ajaran dihapus.');
    }

    public function toggleSemester(Semester $semester): RedirectResponse
    {
        DB::transaction(function () use ($semester) {
            if (! $semester->is_active) {
                Semester::where('academic_year_id', $semester->academic_year_id)
                    ->update(['is_active' => false]);
            }
            $semester->update(['is_active' => ! $semester->is_active]);
        });

        return back()->with('success', 'Status semester diperbarui.');
    }
}
