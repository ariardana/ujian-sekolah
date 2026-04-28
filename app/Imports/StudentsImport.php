<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;

    public int $failed = 0;

    public function __construct(public ?int $defaultClassId = null) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $nisn = trim((string) ($row['nisn'] ?? ''));
            $name = trim((string) ($row['name'] ?? $row['nama'] ?? ''));

            if ($nisn === '' || $name === '' || ! ctype_digit($nisn)) {
                $this->failed++;

                continue;
            }

            try {
                DB::transaction(function () use ($row, $nisn, $name) {
                    $user = User::where('nisn', $nisn)->first();
                    if ($user) {
                        $user->update(['name' => $name]);
                    } else {
                        $user = User::create([
                            'role' => User::ROLE_STUDENT,
                            'name' => $name,
                            'nisn' => $nisn,
                            'password' => Hash::make($nisn),
                            'is_active' => true,
                        ]);
                    }

                    Student::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'school_class_id' => $row['class_id'] ?? $this->defaultClassId,
                            'gender' => in_array($row['gender'] ?? null, ['L', 'P'], true) ? $row['gender'] : null,
                            'phone' => $row['phone'] ?? null,
                        ]
                    );
                });
                $this->imported++;
            } catch (\Throwable) {
                $this->failed++;
            }
        }
    }
}
