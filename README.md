# Ujian Sekolah

Aplikasi web ujian sekolah profesional berbasis **Laravel 12**, dirancang untuk
sekolah menengah dengan dukungan multi-peran (admin, guru, siswa), bank soal
modular, sistem ujian online dengan timer & autosave, penilaian otomatis +
manual, serta laporan ekspor Excel/PDF.

## Stack

- **Laravel 12** (PHP ^8.2)
- **Laravel Breeze** (auth scaffolding)
- **Blade + Tailwind CSS** (`darkMode: 'class'`)
- **Alpine.js** (sidebar, autosave, timer)
- **MySQL** (production) — SQLite untuk dev cepat
- **Maatwebsite Excel** + **DomPDF** (export)

## Fitur Utama

| Modul | Detail |
| --- | --- |
| Auth | Single login multi-field: email → guru, NISN (digit) → siswa, lainnya → username admin. Single-session enforcement untuk siswa, rate-limited login. |
| Master Data | Jurusan, Kelas, Mapel, Bab, Tahun Ajaran + Semester, Guru pengampu mapel. |
| User | CRUD siswa & guru, import siswa via Excel, reset password. |
| Bank Soal | Pilihan ganda + essay, upload gambar, kategori bab, tingkat kesulitan. |
| Ujian | Token opsional, randomize soal/jawaban, status draft/published/closed, manage soal per ujian. |
| Pengerjaan | Timer countdown (auto-submit saat habis), navigator soal, tandai ragu, autosave per soal (debounced fetch + JSON), progress per warna. |
| Penilaian | Auto-score PG, manual essay, kalkulasi total + persentase + lulus/tidak otomatis via `ExamScorer`. |
| Laporan | Ranking peserta, statistik (rata-rata, tertinggi, terendah, lulus), export Excel & PDF. |
| UI | Tailwind modern, dark mode (toggle topbar, persisted in `localStorage`), responsive sidebar (mobile drawer), toast alert. |

## Struktur Modul

```
app/
├── Http/
│   ├── Controllers/{Admin,Teacher,Student}/
│   ├── Middleware/EnsureRole.php
│   └── Middleware/EnsureSingleSession.php
├── Imports/StudentsImport.php
├── Exports/ExamResultsExport.php
├── Models/  (User, Jurusan, SchoolClass, Mapel, Chapter, Question, Exam, …)
└── Services/ExamScorer.php

resources/views/
├── layouts/{app,guest,sidebar,topbar}.blade.php
├── components/  (page-title, card, btn, stat-card, form-input, form-select)
├── auth/login.blade.php       (single field "identifier")
├── admin/  (dashboard + master data CRUD)
├── teacher/ (dashboard, chapters, questions, exams, grading, reports)
└── student/ (dashboard, exams index/show/take/result)
```

## Instalasi

```bash
git clone <repo-url> ujian-sekolah
cd ujian-sekolah
composer install
npm install
cp .env.example .env
php artisan key:generate

# Konfigurasi MySQL di .env, lalu:
php artisan migrate --seed
php artisan storage:link
npm run build      # atau: npm run dev
php artisan serve
```

## Akun Default (Seeder)

| Peran | Identifier | Password |
| --- | --- | --- |
| Admin | `admin` | `password` |
| Guru | `guru@sekolah.test` | `password` |
| Siswa | `0012345678` | `0012345678` |

Login pakai field tunggal `identifier`; sistem otomatis mendeteksi
email/NISN/username.

## Skrip Penting

```bash
php artisan migrate:fresh --seed   # reset DB + sample data
./vendor/bin/pint                  # lint Laravel preset
npm run build                      # asset production
php artisan route:list             # 92 routes
php artisan queue:work             # untuk job (jika dipakai)
```

## Catatan Production

- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://...`
- DB: MySQL 8 (utf8mb4), set `SESSION_DRIVER=database` atau `redis` untuk skala besar.
- `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
- Symlink storage: `php artisan storage:link`.
- Pastikan `php artisan storage:link` agar gambar soal terakses.
- Cron untuk schedule: `* * * * * php artisan schedule:run >> /dev/null 2>&1`.
- Queue (untuk import Excel besar): `php artisan queue:work --tries=3`.

## Lisensi

MIT
