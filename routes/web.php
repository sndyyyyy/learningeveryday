<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminPesertaController;
use App\Http\Controllers\AdminQuizController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\AudioStreamController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SubUserController;
use App\Http\Controllers\ClassGroupController;
use App\Http\Controllers\SpecialTestController;
use App\Http\Controllers\MediaManagerController;

// Route::get('/', function () {
//     return redirect('/login');
// });

Route::get('/', function () {
    return view('welcome');
});

// Rute untuk Kuis Percobaan (trial-quiz.blade.php)
Route::get('/trial-quiz', function () {
    return view('trial-quiz');
});

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::get('/stream-audio', [AudioStreamController::class, 'stream'])->name('audio.stream');

// Kelompok Route Khusus Admin (Harus login & role-nya admin)
// =========================================================================
// KELOMPOK 1: AKSES BERSAMA (Bisa Diakses oleh SUPER ADMIN & ADMIN INSTANSI)
// =========================================================================
Route::middleware(['auth', 'role:super_admin,admin'])->group(function () {
    // Halaman Manajemen Kuis Utama & Akses Bank Soal
    Route::get('/admin/dashboard', [AdminPesertaController::class, 'dashboardUtama'])->name('admin.dashboard.utama');
    Route::get('/admin/quiz', [AdminQuizController::class, 'index'])->name('admin.quiz.index');
    Route::put('/admin/quiz/{quiz}/update', [AdminQuizController::class, 'updateQuiz'])->name('admin.quiz.update');
    Route::post('/admin/quiz', [AdminQuizController::class, 'storeQuiz'])->name('admin.quiz.store');
    Route::get('/admin/quiz/{quiz}/questions', [AdminQuizController::class, 'showQuestions'])->name('admin.quiz.questions');
    Route::post('/admin/quiz/{quiz}/questions', [AdminQuizController::class, 'storeQuestion'])->name('admin.quiz.questions.store');
    Route::delete('/admin/quiz/{quiz}', [AdminQuizController::class, 'destroyQuiz'])->name('admin.quiz.destroy');
    Route::post('/admin/quiz/{quiz}/toggle-all-explanations', [AdminQuizController::class, 'toggleAllExplanations'])->name('admin.quiz.toggle_all_explanations');
    Route::get('/admin/quiz/{quiz}/report-data', [AdminQuizController::class, 'getQuizReportData'])->name('admin.quiz.report_data');
    Route::get('/admin/quiz/{quiz}/export-pdf', [AdminQuizController::class, 'exportQuizPdf'])->name('admin.quiz.export_pdf');
    Route::put('/admin/question/{question}', [AdminQuizController::class, 'updateQuestion'])->name('admin.quiz.questions.update');
    Route::delete('/admin/question/{question}', [AdminQuizController::class, 'destroyQuestion'])->name('admin.quiz.questions.destroy');
    Route::post('/admin/quiz/{quiz}/pull-from-bank', [AdminQuizController::class, 'pullFromBankSoal'])->name('admin.quiz.pull_bank');
    Route::get('/admin/quiz/{quiz}/export-word', [AdminQuizController::class, 'exportQuizWord'])->name('admin.quiz.export_word');

Route::get('/admin/media-gallery', [MediaManagerController::class, 'index'])->name('admin.media.index');
    Route::get('/media-manager/data', [MediaManagerController::class, 'getMedia'])->name('media.data');
    Route::post('/media-manager/folder', [MediaManagerController::class, 'storeFolder'])->name('media.folder.store');
    Route::put('/media-manager/folder/{folder}', [MediaManagerController::class, 'updateFolder'])->name('media.folder.update');
    Route::delete('/media-manager/folder/{folder}', [MediaManagerController::class, 'destroyFolder'])->name('media.folder.destroy');
    Route::post('/media-manager/upload', [MediaManagerController::class, 'uploadFile'])->name('media.upload');
    Route::delete('/media-manager/file/{file}', [MediaManagerController::class, 'destroyFile'])->name('media.file.destroy');

    // Pengelolaan Gudang Data Bank Soal
    Route::get('/admin/bank-soal', [QuestionBankController::class, 'index'])->name('admin.bank.index');
    Route::post('/admin/bank-soal', [QuestionBankController::class, 'storeBank'])->name('admin.bank.store');
    Route::delete('/admin/bank-soal/{bank}', [QuestionBankController::class, 'destroyBank'])->name('admin.bank.destroy');
    Route::put('/admin/bank/{bank}/update', [QuestionBankController::class, 'updateBank'])->name('admin.bank.update');
    Route::put('/admin/bank/parts/{part}/update', [QuestionBankController::class, 'updatePart'])->name('admin.bank.parts.update');
    Route::get('/admin/bank-soal/{bank}/parts', [QuestionBankController::class, 'showParts'])->name('admin.bank.parts');
    Route::post('/admin/bank-soal/{bank}/parts', [QuestionBankController::class, 'storePart'])->name('admin.bank.parts.store');
    Route::delete('/admin/bank-soal/parts/{part}', [QuestionBankController::class, 'destroyPart'])->name('admin.bank.parts.destroy');
    Route::get('/admin/bank-soal/parts/{part}/questions', [QuestionBankController::class, 'showBankQuestions'])->name('admin.bank.questions');
    Route::post('/admin/bank-soal/parts/{part}/questions', [QuestionBankController::class, 'storeBankQuestion'])->name('admin.bank.questions.store');
    Route::put('/admin/bank/questions/{question}', [QuestionBankController::class, 'updateBankQuestion'])->name('admin.bank.questions.update');
    Route::delete('/admin/bank/questions/{question}', [QuestionBankController::class, 'destroyBankQuestion'])->name('admin.bank.questions.destroy');
    Route::post('/admin/bank-soal/parts/{part}/import', [QuestionBankController::class, 'importExcelPlaceholder'])->name('admin.bank.questions.import');
Route::post('/admin/bank/part/{part}/import-zip', [QuestionBankController::class, 'importZipPackage'])->name('admin.bank.questions.import_zip');
});

// =========================================================================
// KELOMPOK 2: KHUSUS SUPER ADMIN PUSAT ONLY
// =========================================================================
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    // Menu Approval Pendaftar Langganan SaaS

    Route::get('/admin/special-tests', [SpecialTestController::class, 'index'])->name('admin.special_tests.index');
    Route::post('/admin/special-tests/store', [SpecialTestController::class, 'store'])->name('admin.special_tests.store');
    Route::delete('/admin/special-tests/{specialTest}', [SpecialTestController::class, 'destroy'])->name('admin.special_tests.destroy');
    Route::post('/admin/special-tests/{specialTest}/participants', [SpecialTestController::class, 'storeParticipant'])->name('admin.special_tests.participant.store');
    Route::get('/admin/approval', [SuperAdminController::class, 'approvalIndex'])->name('admin.approval.index');
    Route::put('/admin/approval/{user}/approve', [SuperAdminController::class, 'approve'])->name('admin.approval.approve');
    Route::put('/admin/approval/{user}/reject', [SuperAdminController::class, 'reject'])->name('admin.approval.reject');

    // Menu Manajemen Peserta Mandiri (Bawaan Lama Pusat)
    Route::get('/admin/peserta', [AdminPesertaController::class, 'index'])->name('admin.peserta.index');
    Route::post('/admin/peserta', [AdminPesertaController::class, 'store'])->name('admin.peserta.store');
    Route::put('/admin/peserta/{user}', [AdminPesertaController::class, 'update'])->name('admin.peserta.update');
    Route::delete('/admin/peserta/{user}', [AdminPesertaController::class, 'destroy'])->name('admin.peserta.destroy');
    Route::get('/admin/rekap/{result}', [AdminPesertaController::class, 'showResultDetail'])->name('admin.rekap.detail');
    Route::post('/admin/peserta/{id}/reset-password', [AdminPesertaController::class, 'resetPassword'])->name('admin.peserta.reset_password');
    Route::post('/admin/peserta/import', [AdminPesertaController::class, 'importPeserta'])->name('admin.peserta.import');
});

// =========================================================================
// KELOMPOK 3: KHUSUS ADMIN INSTANSI (SEKOLAH) ONLY
// =========================================================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Menu Eksklusif Pendaftaran & Limitasi Murid Binaan Sekolah (Maks 50)
    Route::get('/admin/students', [SubUserController::class, 'index'])->name('admin.students.index');
    Route::post('/admin/students', [SubUserController::class, 'store'])->name('admin.students.store');
    Route::delete('/admin/students/{student}', [SubUserController::class, 'destroy'])->name('admin.students.destroy');
    Route::post('/admin/profile/update', [SubUserController::class, 'updateProfile'])->name('admin.profile.update');
Route::post('/admin/classes/store', [ClassGroupController::class, 'store'])->name('admin.classes.store');
    Route::delete('/admin/classes/{classGroup}', [ClassGroupController::class, 'destroy'])->name('admin.classes.destroy');
});
// Kelompok Route Khusus Peserta (Harus login & role-nya peserta)
// Kelompok Route Khusus Peserta (Harus login & role-nya peserta)
// Kelompok Route Khusus Peserta (Harus login & role-nya peserta)
Route::middleware(['auth', 'role:peserta'])->group(function () {
    // 1. Dashboard Utama Peserta
    Route::get('/peserta/dashboard', [PesertaController::class, 'index'])->name('peserta.dashboard');
    
    // 2. Halaman Baru: Daftar Seluruh Kuis (Poin 1)
    Route::get('/peserta/quiz', [PesertaController::class, 'allQuizzes'])->name('peserta.quiz.index');
    
    // 3. Halaman Jalannya Kuis Interaktif
    Route::get('/peserta/quiz/{quiz}', [PesertaController::class, 'showQuiz'])->name('peserta.quiz.show');
    Route::post('/peserta/quiz/{quiz}/submit', [PesertaController::class, 'submitQuiz'])->name('peserta.quiz.submit');

    // 4. Halaman Baru: Riwayat Seluruh Pengerjaan (Poin 2)
    Route::get('/peserta/riwayat', [PesertaController::class, 'allHistory'])->name('peserta.riwayat.index');
Route::get('/peserta/riwayat/{result}', [PesertaController::class, 'detailHistory'])->name('peserta.riwayat.detail');

});