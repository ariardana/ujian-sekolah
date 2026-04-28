<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JurusanController extends Controller
{
    public function index(Request $request): View
    {
        $jurusans = Jurusan::query()
            ->when($request->string('q')->toString(), fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$s}%")
                ->orWhere('code', 'like', "%{$s}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.jurusans.index', compact('jurusans'));
    }

    public function create(): View
    {
        return view('admin.jurusans.form', ['jurusan' => new Jurusan]);
    }

    public function store(Request $request): RedirectResponse
    {
        Jurusan::create($this->validated($request));

        return redirect()->route('admin.jurusans.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Jurusan $jurusan): View
    {
        return view('admin.jurusans.form', compact('jurusan'));
    }

    public function update(Request $request, Jurusan $jurusan): RedirectResponse
    {
        $jurusan->update($this->validated($request, $jurusan->id));

        return redirect()->route('admin.jurusans.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan): RedirectResponse
    {
        $jurusan->delete();

        return back()->with('success', 'Jurusan berhasil dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:jurusans,code'.($ignoreId ? ",{$ignoreId}" : '')],
            'name' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
