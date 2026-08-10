<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizResult;
use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesertaController extends Controller
{
    // 1. Dashboard Utama Peserta
    public function index()
    {
        $user = auth()->user();
        
        $superAdmin = User::where('role', 'super_admin')->first();
        $superAdminId = $superAdmin ? $superAdmin->id : 1; 

        if ($user->instansi_id !== null) {
            // 🏫 SISWA INSTANSI
            $quizzes = Quiz::where('created_by', $user->instansi_id)
                           ->where(function($query) use ($user) {
                               $query->where('class_group', $user->class_group)
                                     ->orWhereNull('class_group');
                           })
                           ->latest()
                           ->get();
        } elseif ($user->subscription === 'siswa_khusus') {
            // ⚓ TES KHUSUS: Filter kuis khusus berdasarkan special_test_id peserta
            $quizzes = Quiz::where('created_by', $superAdminId) // 👈 PASTIKAN BUATAN SUPER ADMIN
                           ->where('tier_access', 'khusus')
                           ->where('special_test_id', $user->special_test_id) // 👈 KUNCI ISOLASI KHUSUS
                           ->latest()
                           ->get();
        } elseif ($user->subscription === 'siswa_premium') {
            // 👑 PREMIUM
            $quizzes = Quiz::where('created_by', $superAdminId)
                           ->whereIn('tier_access', ['premium', 'basic'])
                           ->latest()
                           ->get();
        } else {
            // 🟢 BASIC
            $quizzes = Quiz::where('created_by', $superAdminId)
                           ->where('tier_access', 'basic')
                           ->latest()
                           ->get();
        }

        $history = QuizResult::where('user_id', $user->id)
                             ->with('quiz')
                             ->latest()
                             ->get();

        return view('peserta.dashboard', compact('quizzes', 'history'));
    }

    // 2. Halaman Seluruh Kuis
    public function allQuizzes()
    {
        $user = auth()->user();
        
        $superAdmin = User::where('role', 'super_admin')->first();
        $superAdminId = $superAdmin ? $superAdmin->id : 1; 

        if ($user->instansi_id !== null) {
            $quizzes = Quiz::where('created_by', $user->instansi_id)
                           ->where(function($query) use ($user) {
                               $query->where('class_group', $user->class_group)
                                     ->orWhereNull('class_group');
                           })
                           ->latest()
                           ->get();
        } elseif ($user->subscription === 'siswa_khusus') {
            // ⚓ TES KHUSUS
            $quizzes = Quiz::where('created_by', $superAdminId) // 👈 PASTIKAN BUATAN SUPER ADMIN
                           ->where('tier_access', 'khusus')
                           ->where('special_test_id', $user->special_test_id) // 👈 KUNCI ISOLASI KHUSUS
                           ->latest()
                           ->get();
        } elseif ($user->subscription === 'siswa_premium') {
            $quizzes = Quiz::where('created_by', $superAdminId)
                           ->whereIn('tier_access', ['premium', 'basic'])
                           ->latest()
                           ->get();
        } else {
            $quizzes = Quiz::where('created_by', $superAdminId)
                           ->where('tier_access', 'basic')
                           ->latest()
                           ->get();
        }
        
        return view('peserta.quiz-all', compact('quizzes'));
    }

    // 3. Proteksi & Tampilan Pengerjaan Kuis
    public function showQuiz(Quiz $quiz)
    {
        $user = auth()->user();
        
        $superAdmin = User::where('role', 'super_admin')->first();
        $superAdminId = $superAdmin ? $superAdmin->id : 1; 

        // PROTEKSI SAAS URL
        if ($user->instansi_id !== null) {
            if ($quiz->created_by !== $user->instansi_id) {
                abort(403, 'Anda tidak memiliki hak akses untuk mengerjakan kuis instansi lain.');
            }

            if ($quiz->class_group !== null && $quiz->class_group !== $user->class_group) {
                abort(403, 'Kuis ini khusus ditujukan untuk kelas: ' . $quiz->class_group);
            }
        } else {
            if ($quiz->created_by !== $superAdminId) {
                abort(403, 'Siswa mandiri tidak dapat mengakses kuis milik instansi.');
            }
            
            // Aturan Isolasi Ketat Tes Khusus
            if ($user->subscription === 'siswa_khusus') {
                if ($quiz->tier_access !== 'khusus') {
                    abort(403, 'Akun Anda hanya diizinkan untuk mengakses Tes Khusus.');
                }
                if ($quiz->special_test_id !== $user->special_test_id) {
                    abort(403, 'Kuis ini ditujukan untuk jenis tes khusus yang berbeda dengan akun Anda.');
                }
            }

            if ($user->subscription !== 'siswa_khusus' && $quiz->tier_access === 'khusus') {
                abort(403, 'Kuis ini khusus untuk pendaftar Tes Khusus.');
            }
            
            if ($user->subscription === 'siswa_basic' && $quiz->tier_access === 'premium') {
                abort(403, 'Konten ini hanya tersedia untuk paket Siswa Premium.');
            }
        }

        $questions = Question::with('bankPart')
            ->where('quiz_id', $quiz->id)
            ->orderBy('bank_part_id', 'asc') 
            ->inRandomOrder() 
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->back()->with('error', 'Kuis ini belum memiliki soal. Silakan pilih kuis lain.');
        }

        return view('peserta.quiz-play', compact('quiz', 'questions'));
    }

    public function submitQuiz(Request $request, Quiz $quiz)
    {
        $score = $request->input('final_score');
        $answers = json_decode($request->input('peserta_answers'), true);

        QuizResult::create([
            'user_id' => Auth::id(),
            'quiz_id' => $quiz->id,
            'score' => $score,
            'answers' => $answers,
        ]);

        return redirect()->route('peserta.dashboard')->with('success', 'Kuis telah selesai dikerjakan! Nilai kamu: ' . $score);
    }

    public function allHistory()
    {
        $history = QuizResult::where('user_id', Auth::id())
            ->with('quiz')
            ->latest()
            ->get();

        return view('peserta.riwayat-all', compact('history'));
    }

    public function detailHistory(QuizResult $result)
    {
        if ($result->user_id !== Auth::id()) {
            abort(403, 'Kamu tidak memiliki akses untuk melihat lembar jawaban ini.');
        }

        $quiz = $result->quiz;
        $questions = Question::where('quiz_id', $quiz->id)->get();

        return view('peserta.riwayat-detail', compact('result', 'quiz', 'questions'));
    }
}