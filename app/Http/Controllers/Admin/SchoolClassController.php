<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::with('jurusan')
            ->withCount('students')
            ->when($request->string('q')->toString(), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('level')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.classes.index', compact('classes'));
    }

    public function create(): View
    {
        return view('admin.classes.form', [
            'class' => new SchoolClass,
            'jurusans' => Jurusan::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        SchoolClass::create($this->validated($request));

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(SchoolClass $class): View
    {
        return view('admin.classes.form', [
            'class' => $class,
            'jurusans' => Jurusan::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $class->update($this->validated($request));

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'jurusan_id' => ['nullable', 'exists:jurusans,id'],
            'level' => ['required', 'in:X,XI,XII'],
            'name' => ['required', 'string', 'max:50'],
            'homeroom_teacher' => ['nullable', 'string', 'max:191'],
        ]);
    }
}
