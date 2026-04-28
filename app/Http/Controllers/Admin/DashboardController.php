<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Mapel;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'students' => User::where('role', User::ROLE_STUDENT)->count(),
            'teachers' => User::where('role', User::ROLE_TEACHER)->count(),
            'classes' => SchoolClass::count(),
            'mapels' => Mapel::count(),
            'exams' => Exam::count(),
            'exams_published' => Exam::where('status', Exam::STATUS_PUBLISHED)->count(),
            'online_users' => DB::table('sessions')
                ->where('last_activity', '>=', Carbon::now()->subMinutes(5)->getTimestamp())
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count('user_id'),
            'avg_score' => round((float) Result::avg('percentage'), 2),
        ];

        $recentExams = Exam::with(['mapel', 'schoolClass'])
            ->latest()
            ->limit(5)
            ->get();

        $recentLogins = User::whereNotNull('last_login_at')
            ->orderByDesc('last_login_at')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentExams', 'recentLogins'));
    }
}
