<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Mapel;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(Request $request): View
    {
        $mapelIds = $request->user()->mapels()->pluck('mapels.id');

        $questions = Question::with(['mapel', 'chapter', 'options'])
            ->whereIn('mapel_id', $mapelIds)
            ->when($request->integer('mapel_id'), fn ($q, $id) => $q->where('mapel_id', $id))
            ->when($request->string('type')->toString(), fn ($q, $t) => $q->where('type', $t))
            ->when($request->string('q')->toString(), fn ($q, $s) => $q->where('body', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $mapels = Mapel::whereIn('id', $mapelIds)->orderBy('name')->get();

        return view('teacher.questions.index', compact('questions', 'mapels'));
    }

    public function create(Request $request): View
    {
        $mapelIds = $request->user()->mapels()->pluck('mapels.id');

        return view('teacher.questions.form', [
            'question' => new Question(['type' => Question::TYPE_MC, 'points' => 1, 'difficulty' => 'medium']),
            'mapels' => Mapel::whereIn('id', $mapelIds)->orderBy('name')->get(),
            'chapters' => Chapter::whereIn('mapel_id', $mapelIds)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $this->ensureCanManageMapel($request, $data['mapel_id']);

        DB::transaction(function () use ($data, $request) {
            $question = Question::create([
                'mapel_id' => $data['mapel_id'],
                'chapter_id' => $data['chapter_id'] ?? null,
                'created_by' => $request->user()->id,
                'type' => $data['type'],
                'body' => $data['body'],
                'points' => $data['points'] ?? 1,
                'difficulty' => $data['difficulty'] ?? 'medium',
                'image' => $request->hasFile('image')
                    ? $request->file('image')->store('questions', 'public')
                    : null,
            ]);

            if ($data['type'] === Question::TYPE_MC) {
                foreach ($data['options'] as $i => $opt) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'label' => chr(65 + $i),
                        'body' => $opt['body'],
                        'is_correct' => (int) ($data['correct'] ?? 0) === $i,
                    ]);
                }
            }
        });

        return redirect()->route('teacher.questions.index')->with('success', 'Soal ditambahkan.');
    }

    public function edit(Request $request, Question $question): View
    {
        $this->ensureCanManageMapel($request, $question->mapel_id);
        $question->load('options');
        $mapelIds = $request->user()->mapels()->pluck('mapels.id');

        return view('teacher.questions.form', [
            'question' => $question,
            'mapels' => Mapel::whereIn('id', $mapelIds)->orderBy('name')->get(),
            'chapters' => Chapter::whereIn('mapel_id', $mapelIds)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $this->ensureCanManageMapel($request, $question->mapel_id);
        $data = $this->validated($request);
        $this->ensureCanManageMapel($request, $data['mapel_id']);

        DB::transaction(function () use ($data, $question, $request) {
            if ($request->hasFile('image')) {
                if ($question->image) {
                    Storage::disk('public')->delete($question->image);
                }
                $question->image = $request->file('image')->store('questions', 'public');
            }
            $question->fill([
                'mapel_id' => $data['mapel_id'],
                'chapter_id' => $data['chapter_id'] ?? null,
                'type' => $data['type'],
                'body' => $data['body'],
                'points' => $data['points'] ?? 1,
                'difficulty' => $data['difficulty'] ?? 'medium',
            ])->save();

            if ($data['type'] === Question::TYPE_MC) {
                $question->options()->delete();
                foreach ($data['options'] as $i => $opt) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'label' => chr(65 + $i),
                        'body' => $opt['body'],
                        'is_correct' => (int) ($data['correct'] ?? 0) === $i,
                    ]);
                }
            } else {
                $question->options()->delete();
            }
        });

        return redirect()->route('teacher.questions.index')->with('success', 'Soal diperbarui.');
    }

    public function destroy(Request $request, Question $question): RedirectResponse
    {
        $this->ensureCanManageMapel($request, $question->mapel_id);
        if ($question->image) {
            Storage::disk('public')->delete($question->image);
        }
        $question->delete();

        return back()->with('success', 'Soal dihapus.');
    }

    private function ensureCanManageMapel(Request $request, int $mapelId): void
    {
        abort_unless(
            $request->user()->mapels()->where('mapels.id', $mapelId)->exists(),
            403,
            'Anda tidak mengampu mapel tersebut.'
        );
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'mapel_id' => ['required', 'exists:mapels,id'],
            'chapter_id' => ['nullable', 'exists:chapters,id'],
            'type' => ['required', 'in:multiple_choice,essay'],
            'body' => ['required', 'string'],
            'points' => ['nullable', 'integer', 'min:1', 'max:100'],
            'difficulty' => ['nullable', 'in:easy,medium,hard'],
            'image' => ['nullable', 'image', 'max:2048'],
            'options' => ['required_if:type,multiple_choice', 'array', 'min:2', 'max:5'],
            'options.*.body' => ['required_with:options', 'string'],
            'correct' => ['required_if:type,multiple_choice', 'nullable', 'integer'],
        ]);
    }
}
