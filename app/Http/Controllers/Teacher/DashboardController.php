<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $mapelIds = $user->mapels()->pluck('mapels.id');

        $stats = [
            'mapels' => $mapelIds->count(),
            'questions' => Question::where('created_by', $user->id)->count(),
            'exams' => Exam::where('created_by', $user->id)->count(),
            'exams_active' => Exam::where('created_by', $user->id)
                ->where('status', Exam::STATUS_PUBLISHED)
                ->where('end_at', '>=', now())
                ->count(),
        ];

        $recentExams = Exam::with(['mapel', 'schoolClass'])
            ->where('created_by', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $mapels = $user->mapels()->withCount(['questions'])->get();

        return view('teacher.dashboard', compact('stats', 'recentExams', 'mapels'));
    }
}
