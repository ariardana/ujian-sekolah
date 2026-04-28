<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamParticipant;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $classId = $user->student?->school_class_id;

        $activeExams = Exam::with(['mapel'])
            ->where('status', Exam::STATUS_PUBLISHED)
            ->where('end_at', '>=', now())
            ->when($classId, fn ($q) => $q->where(function ($q) use ($classId) {
                $q->where('school_class_id', $classId)->orWhereNull('school_class_id');
            }))
            ->orderBy('start_at')
            ->limit(6)
            ->get();

        $recentResults = Result::with(['participant.exam.mapel'])
            ->whereHas('participant', fn ($q) => $q->where('user_id', $user->id))
            ->latest('graded_at')
            ->limit(5)
            ->get();

        $stats = [
            'available' => $activeExams->count(),
            'completed' => ExamParticipant::where('user_id', $user->id)
                ->whereIn('status', [ExamParticipant::STATUS_SUBMITTED, ExamParticipant::STATUS_GRADED])
                ->count(),
            'avg_score' => round((float) Result::whereHas('participant', fn ($q) => $q->where('user_id', $user->id))
                ->avg('percentage'), 2),
        ];

        return view('student.dashboard', compact('activeExams', 'recentResults', 'stats'));
    }
}
