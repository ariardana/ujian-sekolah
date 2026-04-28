<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Chapter;
use App\Models\Jurusan;
use App\Models\Mapel;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'role' => User::ROLE_ADMIN,
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // Jurusan
        $rpl = Jurusan::firstOrCreate(['code' => 'RPL'], ['name' => 'Rekayasa Perangkat Lunak']);
        $tkj = Jurusan::firstOrCreate(['code' => 'TKJ'], ['name' => 'Teknik Komputer dan Jaringan']);
        Jurusan::firstOrCreate(['code' => 'MM'], ['name' => 'Multimedia']);

        // Tahun ajaran
        $year = AcademicYear::firstOrCreate(['year' => '2024/2025'], ['is_active' => true]);
        Semester::firstOrCreate(['academic_year_id' => $year->id, 'name' => 'Ganjil'], ['is_active' => true]);
        Semester::firstOrCreate(['academic_year_id' => $year->id, 'name' => 'Genap'], ['is_active' => false]);

        // Kelas
        $kelas = SchoolClass::firstOrCreate([
            'level' => 'XI',
            'name' => '1',
            'jurusan_id' => $rpl->id,
        ]);
        SchoolClass::firstOrCreate([
            'level' => 'XI',
            'name' => '2',
            'jurusan_id' => $tkj->id,
        ]);
        SchoolClass::firstOrCreate([
            'level' => 'XII',
            'name' => '1',
            'jurusan_id' => $rpl->id,
        ]);

        // Mapel
        $mtk = Mapel::firstOrCreate(['code' => 'MTK'], ['name' => 'Matematika']);
        $bind = Mapel::firstOrCreate(['code' => 'BIND'], ['name' => 'Bahasa Indonesia']);
        Mapel::firstOrCreate(['code' => 'BING'], ['name' => 'Bahasa Inggris']);
        $pkn = Mapel::firstOrCreate(['code' => 'PKN'], ['name' => 'PPKn']);

        // Guru
        $guru = User::updateOrCreate(
            ['email' => 'guru@sekolah.test'],
            [
                'role' => User::ROLE_TEACHER,
                'name' => 'Pak Budi',
                'email' => 'guru@sekolah.test',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
        Teacher::updateOrCreate(['user_id' => $guru->id], [
            'nip' => '198001012005011001',
            'gender' => 'L',
            'phone' => '081234567890',
        ]);
        $guru->mapels()->syncWithoutDetaching([$mtk->id, $bind->id]);

        // Bab + soal contoh
        $bab1 = Chapter::firstOrCreate(['mapel_id' => $mtk->id, 'name' => 'Bab 1: Aljabar Linear'], ['order' => 1]);
        Chapter::firstOrCreate(['mapel_id' => $mtk->id, 'name' => 'Bab 2: Trigonometri'], ['order' => 2]);

        if (Question::where('mapel_id', $mtk->id)->count() === 0) {
            $q1 = Question::create([
                'mapel_id' => $mtk->id,
                'chapter_id' => $bab1->id,
                'created_by' => $guru->id,
                'type' => Question::TYPE_MC,
                'body' => 'Berapakah hasil dari 2 + 3 × 4?',
                'points' => 10,
                'difficulty' => 'easy',
            ]);
            foreach ([
                ['A', '20', false], ['B', '14', true], ['C', '24', false], ['D', '10', false],
            ] as [$label, $body, $correct]) {
                QuestionOption::create([
                    'question_id' => $q1->id,
                    'label' => $label,
                    'body' => $body,
                    'is_correct' => $correct,
                ]);
            }

            $q2 = Question::create([
                'mapel_id' => $mtk->id,
                'chapter_id' => $bab1->id,
                'created_by' => $guru->id,
                'type' => Question::TYPE_ESSAY,
                'body' => 'Jelaskan perbedaan vektor dan skalar beserta contohnya.',
                'points' => 20,
                'difficulty' => 'medium',
            ]);
        }

        // Siswa
        $siswa = User::updateOrCreate(
            ['nisn' => '0012345678'],
            [
                'role' => User::ROLE_STUDENT,
                'name' => 'Siti Rahma',
                'nisn' => '0012345678',
                'password' => Hash::make('0012345678'),
                'is_active' => true,
            ]
        );
        Student::updateOrCreate(['user_id' => $siswa->id], [
            'school_class_id' => $kelas->id,
            'gender' => 'P',
            'phone' => '081200000001',
        ]);
    }
}
