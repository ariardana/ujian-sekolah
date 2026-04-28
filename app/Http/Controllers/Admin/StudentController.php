<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = User::query()
            ->where('role', User::ROLE_STUDENT)
            ->with(['student.schoolClass.jurusan'])
            ->when($request->string('q')->toString(), fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$s}%")
                ->orWhere('nisn', 'like', "%{$s}%")))
            ->when($request->integer('class_id'), fn ($q, $id) => $q->whereHas('student', fn ($w) => $w->where('school_class_id', $id)))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $classes = SchoolClass::with('jurusan')->orderBy('level')->orderBy('name')->get();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create(): View
    {
        return view('admin.students.form', [
            'student' => new User(['role' => User::ROLE_STUDENT]),
            'classes' => SchoolClass::with('jurusan')->orderBy('level')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'role' => User::ROLE_STUDENT,
                'name' => $data['name'],
                'nisn' => $data['nisn'],
                'password' => Hash::make($data['password'] ?? $data['nisn']),
                'is_active' => true,
            ]);

            Student::create([
                'user_id' => $user->id,
                'school_class_id' => $data['school_class_id'] ?? null,
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? null,
                'birthdate' => $data['birthdate'] ?? null,
                'birthplace' => $data['birthplace'] ?? null,
                'address' => $data['address'] ?? null,
            ]);
        });

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(User $student): View
    {
        abort_unless($student->role === User::ROLE_STUDENT, 404);
        $student->load('student');

        return view('admin.students.form', [
            'student' => $student,
            'classes' => SchoolClass::with('jurusan')->orderBy('level')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $student): RedirectResponse
    {
        abort_unless($student->role === User::ROLE_STUDENT, 404);
        $data = $this->validated($request, $student->id);

        DB::transaction(function () use ($data, $student) {
            $student->update([
                'name' => $data['name'],
                'nisn' => $data['nisn'],
                'is_active' => (bool) ($data['is_active'] ?? true),
            ]);
            if (! empty($data['password'])) {
                $student->update(['password' => Hash::make($data['password'])]);
            }
            $student->student()->updateOrCreate(
                ['user_id' => $student->id],
                [
                    'school_class_id' => $data['school_class_id'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'birthdate' => $data['birthdate'] ?? null,
                    'birthplace' => $data['birthplace'] ?? null,
                    'address' => $data['address'] ?? null,
                ]
            );
        });

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroy(User $student): RedirectResponse
    {
        abort_unless($student->role === User::ROLE_STUDENT, 404);
        $student->delete();

        return back()->with('success', 'Siswa berhasil dihapus.');
    }

    public function resetPassword(User $student): RedirectResponse
    {
        abort_unless($student->role === User::ROLE_STUDENT, 404);
        $student->update(['password' => Hash::make($student->nisn)]);

        return back()->with('success', 'Password siswa direset ke NISN.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
        ]);

        $import = new StudentsImport($request->integer('school_class_id') ?: null);
        Excel::import($import, $request->file('file'));

        return back()->with('success', "Import selesai: {$import->imported} berhasil, {$import->failed} gagal.");
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'nisn' => ['required', 'string', 'max:30', 'unique:users,nisn'.($ignoreId ? ",{$ignoreId}" : '')],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'gender' => ['nullable', 'in:L,P'],
            'phone' => ['nullable', 'string', 'max:30'],
            'birthdate' => ['nullable', 'date'],
            'birthplace' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
