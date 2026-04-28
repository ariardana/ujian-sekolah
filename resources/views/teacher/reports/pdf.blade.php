<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil {{ $exam->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; }
        h1 { margin: 0 0 4px; font-size: 18px; }
        h2 { margin: 0 0 16px; font-size: 14px; color: #64748b; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; font-size: 11px; text-transform: uppercase; }
        .right { text-align: right; }
        .center { text-align: center; }
        .footer { margin-top: 24px; font-size: 10px; color: #64748b; }
    </style>
</head>
<body>
    <h1>{{ $exam->name }}</h1>
    <h2>{{ $exam->mapel?->name }} · {{ $exam->schoolClass ? $exam->schoolClass->level.' '.$exam->schoolClass->name : 'Semua kelas' }}</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>NISN</th>
                <th>Kelas</th>
                <th class="right">Skor</th>
                <th class="right">Persentase</th>
                <th class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($participants as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->user->name }}</td>
                    <td>{{ $p->user->nisn }}</td>
                    <td>
                        @if ($p->user->student?->schoolClass)
                            {{ $p->user->student->schoolClass->level }} {{ $p->user->student->schoolClass->name }}
                        @else - @endif
                    </td>
                    <td class="right">{{ $p->result?->total_score ?? '-' }} / {{ $p->result?->max_score ?? '-' }}</td>
                    <td class="right">{{ $p->result ? number_format($p->result->percentage, 1).'%' : '-' }}</td>
                    <td class="center">
                        @if ($p->result?->is_passed) Lulus
                        @elseif ($p->result) Tidak Lulus
                        @else Belum Dinilai @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">Dicetak pada {{ now()->format('d M Y H:i') }}</p>
</body>
</html>
