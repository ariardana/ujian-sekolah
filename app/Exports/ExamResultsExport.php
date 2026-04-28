<?php

namespace App\Exports;

use App\Models\Exam;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExamResultsExport implements FromCollection, Responsable, WithHeadings, WithMapping
{
    use Exportable;

    private string $fileName;

    public function __construct(public Exam $exam)
    {
        $this->fileName = 'hasil-ujian-'.$exam->id.'-'.now()->format('YmdHis').'.xlsx';
    }

    public function collection()
    {
        return $this->exam->participants()
            ->with(['user.student.schoolClass.jurusan', 'result'])
            ->get();
    }

    public function headings(): array
    {
        return ['NISN', 'Nama', 'Kelas', 'Mulai', 'Submit', 'Benar', 'Salah', 'Kosong', 'Skor', 'Persentase', 'Status'];
    }

    public function map($participant): array
    {
        $student = $participant->user;
        $class = $student?->student?->schoolClass;
        $r = $participant->result;

        return [
            $student?->nisn,
            $student?->name,
            $class ? ($class->level.' '.($class->jurusan?->code ? $class->jurusan->code.' ' : '').$class->name) : '-',
            optional($participant->started_at)->format('Y-m-d H:i'),
            optional($participant->submitted_at)->format('Y-m-d H:i'),
            $r?->correct_count ?? 0,
            $r?->wrong_count ?? 0,
            $r?->unanswered_count ?? 0,
            $r ? $r->total_score.' / '.$r->max_score : '-',
            $r?->percentage ?? 0,
            $r?->is_passed ? 'LULUS' : 'TIDAK LULUS',
        ];
    }
}
