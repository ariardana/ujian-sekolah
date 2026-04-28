<?php

namespace App\Services;

use App\Models\ExamParticipant;
use App\Models\Question;
use App\Models\Result;
use Illuminate\Support\Facades\DB;

class ExamScorer
{
    /**
     * Auto-grade multiple choice questions for a participant and persist a Result.
     * Essay questions still require manual scoring; their score remains null until graded.
     */
    public function grade(ExamParticipant $participant): Result
    {
        $participant->loadMissing(['exam.questions.options', 'answers']);

        $totalScore = 0.0;
        $maxScore = 0.0;
        $correct = 0;
        $wrong = 0;
        $unanswered = 0;

        $essayPending = false;

        return DB::transaction(function () use ($participant, &$totalScore, &$maxScore, &$correct, &$wrong, &$unanswered, &$essayPending) {
            foreach ($participant->exam->questions as $question) {
                $maxScore += $question->points;
                $answer = $participant->answers->firstWhere('question_id', $question->id);

                if (! $answer) {
                    $unanswered++;

                    continue;
                }

                if ($question->type === Question::TYPE_MC) {
                    $correctOptionId = $question->options->firstWhere('is_correct', true)?->id;
                    $isCorrect = $answer->selected_option_id !== null
                        && $answer->selected_option_id === $correctOptionId;
                    $score = $isCorrect ? (float) $question->points : 0.0;

                    $answer->update([
                        'is_correct' => $isCorrect,
                        'score' => $score,
                    ]);

                    $totalScore += $score;
                    $isCorrect ? $correct++ : $wrong++;

                    continue;
                }

                // Essay: score may already be set by a teacher.
                if ($answer->score === null && empty(trim((string) $answer->answer_text))) {
                    $unanswered++;

                    continue;
                }

                if ($answer->score === null) {
                    $essayPending = true;

                    continue;
                }

                $totalScore += (float) $answer->score;
                ((float) $answer->score >= ($question->points * 0.5)) ? $correct++ : $wrong++;
            }

            $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0;
            $isPassed = $percentage >= ($participant->exam->passing_score ?? 70);

            $result = Result::updateOrCreate(
                ['exam_participant_id' => $participant->id],
                [
                    'total_score' => $totalScore,
                    'max_score' => $maxScore,
                    'percentage' => $percentage,
                    'is_passed' => $isPassed,
                    'correct_count' => $correct,
                    'wrong_count' => $wrong,
                    'unanswered_count' => $unanswered,
                    'graded_at' => $essayPending ? null : now(),
                ]
            );

            $participant->update([
                'status' => $essayPending
                    ? ExamParticipant::STATUS_SUBMITTED
                    : ExamParticipant::STATUS_GRADED,
            ]);

            return $result;
        });
    }
}
