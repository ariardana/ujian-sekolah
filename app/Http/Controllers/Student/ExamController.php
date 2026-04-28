<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamParticipant;
use App\Models\Question;
use App\Services\ExamScorer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $classId = $user->student?->school_class_id;

        $exams = Exam::with('mapel')
            ->where('status', Exam::STATUS_PUBLISHED)
            ->where('end_at', '>=', now())
            ->when($classId, fn ($q) => $q->where(function ($q) use ($classId) {
                $q->where('school_class_id', $classId)->orWhereNull('school_class_id');
            }))
            ->orderBy('start_at')
            ->paginate(10);

        return view('student.exams.index', compact('exams'));
    }

    public function show(Request $request, Exam $exam): View
    {
        $this->ensureEligible($request, $exam);
        $participant = $exam->participants()->where('user_id', $request->user()->id)->first();

        return view('student.exams.show', compact('exam', 'participant'));
    }

    public function start(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureEligible($request, $exam);

        if ($exam->token) {
            $request->validate(['token' => ['required', 'string']]);
            abort_if($request->string('token')->toString() !== $exam->token, 422, 'Token salah.');
        }

        $participant = DB::transaction(function () use ($request, $exam) {
            $participant = ExamParticipant::firstOrCreate(
                ['exam_id' => $exam->id, 'user_id' => $request->user()->id],
                ['status' => ExamParticipant::STATUS_NOT_STARTED]
            );

            if ($participant->status === ExamParticipant::STATUS_NOT_STARTED) {
                $questionIds = $exam->questions()->pluck('questions.id')->all();
                if ($exam->randomize_questions) {
                    shuffle($questionIds);
                }
                $participant->update([
                    'status' => ExamParticipant::STATUS_IN_PROGRESS,
                    'started_at' => now(),
                    'question_order' => $questionIds,
                ]);
            }

            return $participant;
        });

        return redirect()->route('student.exams.take', [$exam, 1]);
    }

    public function take(Request $request, Exam $exam, int $number): View|RedirectResponse
    {
        $this->ensureEligible($request, $exam);
        $participant = $exam->participants()
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (in_array($participant->status, [ExamParticipant::STATUS_SUBMITTED, ExamParticipant::STATUS_GRADED], true)) {
            return redirect()->route('student.exams.result', $exam);
        }

        if (now()->greaterThan($participant->deadline())) {
            return $this->doSubmit($participant);
        }

        $order = $participant->question_order ?? $exam->questions->pluck('id')->all();
        $total = count($order);
        $number = max(1, min($number, $total));
        $questionId = $order[$number - 1];

        $question = Question::with('options')->findOrFail($questionId);
        if ($exam->randomize_options) {
            $question->setRelation('options', $question->options->shuffle());
        }

        $answers = Answer::where('exam_participant_id', $participant->id)
            ->get()
            ->keyBy('question_id');

        $current = $answers->get($question->id);

        return view('student.exams.take', [
            'exam' => $exam,
            'participant' => $participant,
            'question' => $question,
            'number' => $number,
            'total' => $total,
            'order' => $order,
            'answers' => $answers,
            'current' => $current,
            'deadline' => $participant->deadline(),
        ]);
    }

    public function saveAnswer(Request $request, Exam $exam, Question $question): JsonResponse
    {
        $this->ensureEligible($request, $exam);
        $participant = $exam->participants()
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($participant->status !== ExamParticipant::STATUS_IN_PROGRESS) {
            return response()->json(['ok' => false, 'message' => 'Sesi ujian tidak aktif.'], 422);
        }

        if (now()->greaterThan($participant->deadline())) {
            return response()->json(['ok' => false, 'message' => 'Waktu habis.'], 422);
        }

        $data = $request->validate([
            'selected_option_id' => ['nullable', 'exists:question_options,id'],
            'answer_text' => ['nullable', 'string'],
            'is_flagged' => ['nullable', 'boolean'],
        ]);

        Answer::updateOrCreate(
            ['exam_participant_id' => $participant->id, 'question_id' => $question->id],
            $data
        );

        return response()->json(['ok' => true]);
    }

    public function submit(Request $request, Exam $exam): RedirectResponse
    {
        $this->ensureEligible($request, $exam);
        $participant = $exam->participants()
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return $this->doSubmit($participant);
    }

    public function result(Request $request, Exam $exam): View
    {
        $participant = $exam->participants()
            ->where('user_id', $request->user()->id)
            ->with('result')
            ->firstOrFail();

        return view('student.exams.result', [
            'exam' => $exam,
            'participant' => $participant,
            'result' => $participant->result,
        ]);
    }

    private function doSubmit(ExamParticipant $participant): RedirectResponse
    {
        if ($participant->status === ExamParticipant::STATUS_IN_PROGRESS) {
            $participant->update([
                'submitted_at' => now(),
                'status' => ExamParticipant::STATUS_SUBMITTED,
            ]);
        }

        app(ExamScorer::class)->grade($participant);

        return redirect()->route('student.exams.result', $participant->exam_id)
            ->with('success', 'Ujian berhasil disubmit.');
    }

    private function ensureEligible(Request $request, Exam $exam): void
    {
        abort_unless($exam->status === Exam::STATUS_PUBLISHED, 403, 'Ujian belum/tidak tersedia.');
        abort_if(now()->greaterThan($exam->end_at), 410, 'Ujian sudah berakhir.');
        $classId = $request->user()->student?->school_class_id;
        if ($exam->school_class_id && $exam->school_class_id !== $classId) {
            abort(403, 'Ujian tidak tersedia untuk kelas Anda.');
        }
    }
}
