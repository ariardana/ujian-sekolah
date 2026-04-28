<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $teachers = User::query()
            ->where('role', User::ROLE_TEACHER)
            ->with(['teacher', 'mapels'])
            ->when($request->string('q')->toString(), fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('admin.teachers.form', [
            'teacher' => new User(['role' => User::ROLE_TEACHER]),
            'mapels' => Mapel::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'role' => User::ROLE_TEACHER,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? 'password'),
                'is_active' => true,
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'nip' => $data['nip'] ?? null,
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ]);

            $user->mapels()->sync($data['mapel_ids'] ?? []);
        });

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit(User $teacher): View
    {
        abort_unless($teacher->role === User::ROLE_TEACHER, 404);
        $teacher->load(['teacher', 'mapels']);

        return view('admin.teachers.form', [
            'teacher' => $teacher,
            'mapels' => Mapel::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === User::ROLE_TEACHER, 404);
        $data = $this->validated($request, $teacher->id);

        DB::transaction(function () use ($data, $teacher) {
            $teacher->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => (bool) ($data['is_active'] ?? true),
            ]);

            if (! empty($data['password'])) {
                $teacher->update(['password' => Hash::make($data['password'])]);
            }

            $teacher->teacher()->updateOrCreate(
                ['user_id' => $teacher->id],
                [
                    'nip' => $data['nip'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null,
                ]
            );

            $teacher->mapels()->sync($data['mapel_ids'] ?? []);
        });

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === User::ROLE_TEACHER, 404);
        $teacher->delete();

        return back()->with('success', 'Guru berhasil dihapus.');
    }

    public function resetPassword(User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === User::ROLE_TEACHER, 404);
        $teacher->update(['password' => Hash::make('password')]);

        return back()->with('success', 'Password guru direset ke "password".');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'.($ignoreId ? ",{$ignoreId}" : '')],
            'nip' => ['nullable', 'string', 'max:30', 'unique:teachers,nip'.($ignoreId ? ",{$ignoreId},user_id" : '')],
            'gender' => ['nullable', 'in:L,P'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'mapel_ids' => ['nullable', 'array'],
            'mapel_ids.*' => ['integer', 'exists:mapels,id'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
