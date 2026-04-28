<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapelController extends Controller
{
    public function index(Request $request): View
    {
        $mapels = Mapel::with('teachers')
            ->withCount(['questions', 'chapters'])
            ->when($request->string('q')->toString(), fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$s}%")
                ->orWhere('code', 'like', "%{$s}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.mapels.index', compact('mapels'));
    }

    public function create(): View
    {
        $teachers = User::where('role', User::ROLE_TEACHER)->orderBy('name')->get();

        return view('admin.mapels.form', ['mapel' => new Mapel, 'teachers' => $teachers]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $teacherIds = $data['teacher_ids'] ?? [];
        unset($data['teacher_ids']);

        $mapel = Mapel::create($data);
        $mapel->teachers()->sync($teacherIds);

        return redirect()->route('admin.mapels.index')->with('success', 'Mapel berhasil ditambahkan.');
    }

    public function edit(Mapel $mapel): View
    {
        $teachers = User::where('role', User::ROLE_TEACHER)->orderBy('name')->get();
        $mapel->load('teachers');

        return view('admin.mapels.form', compact('mapel', 'teachers'));
    }

    public function update(Request $request, Mapel $mapel): RedirectResponse
    {
        $data = $this->validated($request, $mapel->id);
        $teacherIds = $data['teacher_ids'] ?? [];
        unset($data['teacher_ids']);

        $mapel->update($data);
        $mapel->teachers()->sync($teacherIds);

        return redirect()->route('admin.mapels.index')->with('success', 'Mapel berhasil diperbarui.');
    }

    public function destroy(Mapel $mapel): RedirectResponse
    {
        $mapel->delete();

        return back()->with('success', 'Mapel berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:mapels,code'.($ignoreId ? ",{$ignoreId}" : '')],
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:500'],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['integer', 'exists:users,id'],
        ]);
    }
}
