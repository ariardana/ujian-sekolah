<?php

namespace App\Http\Controllers\Teacher;

use App\Exports\ExamResultsExport;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $exams = Exam::with(['mapel', 'schoolClass'])
            ->where('created_by', $request->user()->id)
            ->withCount(['participants'])
            ->latest()
            ->paginate(15);

        return view('teacher.reports.index', compact('exams'));
    }

    public function show(Request $request, Exam $exam): View
    {
        abort_unless($exam->created_by === $request->user()->id, 403);

        $participants = $exam->participants()
            ->with(['user.student.schoolClass.jurusan', 'result'])
            ->get()
            ->sortByDesc(fn ($p) => $p->result?->percentage ?? -1)
            ->values();

        $stats = [
            'count' => $participants->count(),
            'avg' => round($participants->avg(fn ($p) => $p->result?->percentage ?? 0), 2),
            'max' => $participants->max(fn ($p) => $p->result?->percentage ?? 0),
            'min' => $participants->min(fn ($p) => $p->result?->percentage ?? 0),
            'passed' => $participants->filter(fn ($p) => $p->result?->is_passed)->count(),
        ];

        return view('teacher.reports.show', compact('exam', 'participants', 'stats'));
    }

    public function exportExcel(Request $request, Exam $exam): BinaryFileResponse
    {
        abort_unless($exam->created_by === $request->user()->id, 403);

        return Excel::download(
            new ExamResultsExport($exam),
            'hasil-ujian-'.$exam->id.'-'.now()->format('YmdHis').'.xlsx'
        );
    }

    public function exportPdf(Request $request, Exam $exam): Response
    {
        abort_unless($exam->created_by === $request->user()->id, 403);

        $participants = $exam->participants()
            ->with(['user.student.schoolClass.jurusan', 'result'])
            ->get()
            ->sortByDesc(fn ($p) => $p->result?->percentage ?? -1)
            ->values();

        $pdf = Pdf::loadView('teacher.reports.pdf', compact('exam', 'participants'));

        return $pdf->download('hasil-ujian-'.$exam->id.'.pdf');
    }
}
