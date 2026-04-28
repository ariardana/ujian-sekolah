<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Mapel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChapterController extends Controller
{
    public function index(Request $request): View
    {
        $mapelIds = $request->user()->mapels()->pluck('mapels.id');

        $chapters = Chapter::with('mapel')
            ->whereIn('mapel_id', $mapelIds)
            ->orderBy('mapel_id')
            ->orderBy('order')
            ->paginate(20);

        $mapels = Mapel::whereIn('id', $mapelIds)->orderBy('name')->get();

        return view('teacher.chapters.index', compact('chapters', 'mapels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mapel_id' => ['required', 'exists:mapels,id'],
            'name' => ['required', 'string', 'max:191'],
            'order' => ['nullable', 'integer'],
        ]);

        abort_unless($request->user()->mapels()->where('mapels.id', $data['mapel_id'])->exists(), 403);
        Chapter::create($data);

        return back()->with('success', 'Bab ditambahkan.');
    }

    public function update(Request $request, Chapter $chapter): RedirectResponse
    {
        abort_unless($request->user()->mapels()->where('mapels.id', $chapter->mapel_id)->exists(), 403);
        $chapter->update($request->validate([
            'name' => ['required', 'string', 'max:191'],
            'order' => ['nullable', 'integer'],
        ]));

        return back()->with('success', 'Bab diperbarui.');
    }

    public function destroy(Request $request, Chapter $chapter): RedirectResponse
    {
        abort_unless($request->user()->mapels()->where('mapels.id', $chapter->mapel_id)->exists(), 403);
        $chapter->delete();

        return back()->with('success', 'Bab dihapus.');
    }
}
