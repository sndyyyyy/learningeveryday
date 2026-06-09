<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminPesertaController;
use App\Http\Controllers\AdminQuizController;
use App\Http\Controllers\PesertaController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Kelompok Route Khusus Admin (Harus login & role-nya admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminPesertaController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/peserta', [AdminPesertaController::class, 'store'])->name('admin.peserta.store');

    // Route Tambahan untuk Manajemen Kuis & Soal
    Route::get('/admin/quiz', [AdminQuizController::class, 'index'])->name('admin.quiz.index');
    Route::post('/admin/quiz', [AdminQuizController::class, 'storeQuiz'])->name('admin.quiz.store');
    Route::get('/admin/quiz/{quiz}/questions', [AdminQuizController::class, 'showQuestions'])->name('admin.quiz.questions');
    Route::post('/admin/quiz/{quiz}/questions', [AdminQuizController::class, 'storeQuestion'])->name('admin.quiz.questions.store');
Route::put('/admin/peserta/{user}', [AdminPesertaController::class, 'update'])->name('admin.peserta.update');
    Route::delete('/admin/peserta/{user}', [AdminPesertaController::class, 'destroy'])->name('admin.peserta.destroy');
    Route::delete('/admin/quiz/{quiz}', [AdminQuizController::class, 'destroyQuiz'])->name('admin.quiz.destroy');
Route::put('/admin/question/{question}', [AdminQuizController::class, 'updateQuestion'])->name('admin.quiz.questions.update');
    Route::delete('/admin/question/{question}', [AdminQuizController::class, 'destroyQuestion'])->name('admin.quiz.questions.destroy');

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