<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Mapel;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(Request $request): View
    {
        $exams = Exam::with(['mapel', 'schoolClass'])
            ->where('created_by', $request->user()->id)
            ->withCount('questions')
            ->latest()
            ->paginate(15);

        return view('teacher.exams.index', compact('exams'));
    }

    public function create(Request $request): View
    {
        $mapelIds = $request->user()->mapels()->pluck('mapels.id');

        return view('teacher.exams.form', [
            'exam' => new Exam(['status' => Exam::STATUS_DRAFT, 'duration_minutes' => 60, 'passing_score' => 70]),
            'mapels' => Mapel::whereIn('id', $mapelIds)->orderBy('name')->get(),
            'classes' => SchoolClass::with('jurusan')->orderBy('level')->orderBy('name')->get(),
            'semesters' => Semester::with('academicYear')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $this->ensureMapel($request, $data['mapel_id']);

        $exam = Exam::create($data + ['created_by' => $request->user()->id]);

        return redirect()->route('teacher.exams.questions', $exam)->with('success', 'Ujian dibuat. Tambahkan soal sekarang.');
    }

    public function edit(Request $request, Exam $exam): View
    {
        $this->ensureOwner($request, $exam);
        $mapelIds = $request->user()->mapels()->pluck('mapels.id');

        return view('teacher.exams.form', [
            'exam' => $exam,
            'mapels' => Mapel::whereIn('id', $mapelIds)->orderBy('name')->get(),
            'classes' => SchoolClass::with('jurusan')->orderBy('level')->orderBy('name')->get(),
            'semesters' => Semester::with('academicYear')->get(),
        ]);
    }

    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureOwner($request, $exam);
        $data = $this->validated($request);
        $this->ensureMapel($request, $data['mapel_id']);
        $exam->update($data);

        return redirect()->route('teacher.exams.index')->with('success', 'Ujian diperbarui.');
    }

    public function destroy(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureOwner($request, $exam);
        $exam->delete();

        return back()->with('success', 'Ujian dihapus.');
    }

    public function questions(Request $request, Exam $exam): View
    {
        $this->ensureOwner($request, $exam);
        $exam->load(['questions.options', 'mapel']);

        $available = Question::with('options')
            ->where('mapel_id', $exam->mapel_id)
            ->whereNotIn('id', $exam->questions->pluck('id'))
            ->paginate(10);

        return view('teacher.exams.questions', compact('exam', 'available'));
    }

    public function attachQuestions(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureOwner($request, $exam);
        $ids = $request->validate([
            'question_ids' => ['required', 'array'],
            'question_ids.*' => ['integer', 'exists:questions,id'],
        ])['question_ids'];

        DB::transaction(function () use ($exam, $ids) {
            $next = ($exam->questions()->max('exam_questions.order') ?? 0) + 1;
            foreach ($ids as $id) {
                $exam->questions()->syncWithoutDetaching([$id => ['order' => $next++]]);
            }
        });

        return back()->with('success', 'Soal ditambahkan ke ujian.');
    }

    public function detachQuestion(Request $request, Exam $exam, Question $question): RedirectResponse
    {
        $this->ensureOwner($request, $exam);
        $exam->questions()->detach($question->id);

        return back()->with('success', 'Soal dilepas dari ujian.');
    }

    public function publish(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureOwner($request, $exam);
        abort_if($exam->questions()->count() === 0, 422, 'Tambahkan minimal 1 soal sebelum publish.');
        $exam->update(['status' => Exam::STATUS_PUBLISHED]);

        return back()->with('success', 'Ujian dipublikasikan.');
    }

    public function close(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureOwner($request, $exam);
        $exam->update(['status' => Exam::STATUS_CLOSED]);

        return back()->with('success', 'Ujian ditutup.');
    }

    private function ensureMapel(Request $request, int $mapelId): void
    {
        abort_unless(
            $request->user()->mapels()->where('mapels.id', $mapelId)->exists(),
            403
        );
    }

    private function ensureOwner(Request $request, Exam $exam): void
    {
        abort_unless($exam->created_by === $request->user()->id, 403);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'mapel_id' => ['required', 'exists:mapels,id'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'token' => ['nullable', 'string', 'max:20'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'randomize_questions' => ['nullable', 'boolean'],
            'randomize_options' => ['nullable', 'boolean'],
            'show_result' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:draft,published,closed'],
        ]);
    }
}
