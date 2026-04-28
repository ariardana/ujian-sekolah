<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamParticipant;
use App\Models\Question;
use App\Services\ExamScorer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradingController extends Controller
{
    public function index(Request $request, Exam $exam): View
    {
        abort_unless($exam->created_by === $request->user()->id, 403);

        $participants = $exam->participants()
            ->with(['user', 'result'])
            ->orderByDesc('submitted_at')
            ->paginate(20);

        return view('teacher.grading.index', compact('exam', 'participants'));
    }

    public function show(Request $request, Exam $exam, ExamParticipant $participant): View
    {
        abort_unless($exam->created_by === $request->user()->id, 403);
        abort_unless($participant->exam_id === $exam->id, 404);

        $participant->load(['user', 'answers.question.options', 'answers.selectedOption', 'result']);

        return view('teacher.grading.show', compact('exam', 'participant'));
    }

    public function gradeEssay(Request $request, Exam $exam, ExamParticipant $participant, Answer $answer): RedirectResponse
    {
        abort_unless($exam->created_by === $request->user()->id, 403);
        abort_unless($participant->exam_id === $exam->id, 404);
        abort_unless($answer->exam_participant_id === $participant->id, 404);
        abort_unless($answer->question->type === Question::TYPE_ESSAY, 422);

        $data = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:'.$answer->question->points],
            'teacher_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $answer->update([
            'score' => $data['score'],
            'teacher_note' => $data['teacher_note'] ?? null,
            'is_correct' => $data['score'] >= ($answer->question->points * 0.5),
        ]);

        app(ExamScorer::class)->grade($participant);

        return back()->with('success', 'Nilai disimpan.');
    }
}
