<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Teacher\ChapterController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController;
use App\Http\Controllers\Teacher\GradingController;
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect(Auth::user()->dashboardRoute()) : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect(Auth::user()->dashboardRoute());
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==============================
// ADMIN
// ==============================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

    Route::resource('jurusans', JurusanController::class)->except(['show']);
    Route::resource('mapels', MapelController::class)->except(['show']);
    Route::resource('classes', SchoolClassController::class)->except(['show'])->parameters(['classes' => 'class']);

    Route::resource('academic-years', AcademicYearController::class)->except(['show']);
    Route::post('semesters/{semester}/toggle', [AcademicYearController::class, 'toggleSemester'])->name('semesters.toggle');

    Route::resource('students', StudentController::class)->except(['show']);
    Route::post('students/{student}/reset-password', [StudentController::class, 'resetPassword'])->name('students.reset');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');

    Route::resource('teachers', TeacherController::class)->except(['show']);
    Route::post('teachers/{teacher}/reset-password', [TeacherController::class, 'resetPassword'])->name('teachers.reset');
});

// ==============================
// TEACHER
// ==============================
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', TeacherDashboardController::class)->name('dashboard');

    Route::resource('chapters', ChapterController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('questions', QuestionController::class)->except(['show']);

    Route::resource('exams', TeacherExamController::class)->except(['show']);
    Route::get('exams/{exam}/questions', [TeacherExamController::class, 'questions'])->name('exams.questions');
    Route::post('exams/{exam}/questions', [TeacherExamController::class, 'attachQuestions'])->name('exams.questions.attach');
    Route::delete('exams/{exam}/questions/{question}', [TeacherExamController::class, 'detachQuestion'])->name('exams.questions.detach');
    Route::post('exams/{exam}/publish', [TeacherExamController::class, 'publish'])->name('exams.publish');
    Route::post('exams/{exam}/close', [TeacherExamController::class, 'close'])->name('exams.close');

    Route::get('exams/{exam}/grading', [GradingController::class, 'index'])->name('grading.index');
    Route::get('exams/{exam}/grading/{participant}', [GradingController::class, 'show'])->name('grading.show');
    Route::post('exams/{exam}/grading/{participant}/answers/{answer}', [GradingController::class, 'gradeEssay'])->name('grading.essay');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{exam}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('reports/{exam}/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    Route::get('reports/{exam}/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
});

// ==============================
// STUDENT
// ==============================
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', StudentDashboardController::class)->name('dashboard');

    Route::get('exams', [StudentExamController::class, 'index'])->name('exams.index');
    Route::get('exams/{exam}', [StudentExamController::class, 'show'])->name('exams.show');
    Route::post('exams/{exam}/start', [StudentExamController::class, 'start'])->name('exams.start');
    Route::get('exams/{exam}/take/{number}', [StudentExamController::class, 'take'])->name('exams.take');
    Route::post('exams/{exam}/answers/{question}', [StudentExamController::class, 'saveAnswer'])->name('exams.answer');
    Route::post('exams/{exam}/submit', [StudentExamController::class, 'submit'])->name('exams.submit');
    Route::get('exams/{exam}/result', [StudentExamController::class, 'result'])->name('exams.result');
});

require __DIR__.'/auth.php';
