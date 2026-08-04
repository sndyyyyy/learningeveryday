<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;

class PesertaController extends Controller
{
    // 1. Dashboard Utama Peserta (Dibatasi maksimal 3 data kuis terbaru)
    public function index()
    {
        $user = auth()->user();
        
        // Asumsi ID Super Admin Pusat adalah 1. Sesuaikan jika berbeda di DB.
        $superAdminId = 1; 

        // 1. FILTER QUIS (Logika Tahap 6 - Isolasi Baru)
if ($user->instansi_id !== null) {
            // 🏫 SISWA INSTANSI: Tampilkan kuis buatan sekolahnya yang kelasnya COCOK dengan siswa,
            // ATAU kuis yang class_group-nya NULL (diperuntukkan bagi semua kelas di sekolah tersebut)
            $quizzes = Quiz::where('created_by', $user->instansi_id)
                           ->where(function($query) use ($user) {
                               $query->where('class_group', $user->class_group)
                                     ->orWhereNull('class_group');
                           })
                           ->latest()
                           ->get();
        } elseif ($user->subscription === 'siswa_khusus') {
            // ⚓ TES KHUSUS (Marlins): Terisolasi, hanya melihat kuis 'khusus'
            $quizzes = Quiz::where('created_by', $superAdminId)
                           ->where('tier_access', 'khusus')
                           ->latest()
                           ->get();
        } elseif ($user->subscription === 'siswa_premium') {
            // 👑 PREMIUM: Melihat Premium & Basic
            $quizzes = Quiz::where('created_by', $superAdminId)
                           ->whereIn('tier_access', ['premium', 'basic'])
                           ->latest()
                           ->get();
        } else {
            // 🟢 BASIC: Hanya melihat Basic
            $quizzes = Quiz::where('created_by', $superAdminId)
                           ->where('tier_access', 'basic')
                           ->latest()
                           ->get();
        }

        // 2. AMBIL DATA RIWAYAT (Untuk mengatasi Error Undefined Variable $history)
        $history = \App\Models\QuizResult::where('user_id', $user->id)
                                         ->with('quiz') // Eager load relasi kuisnya agar title kuis terbaca
                                         ->latest()
                                         ->get();

        // Kirim kedua variabel ke view kuis
        return view('peserta.dashboard', compact('quizzes', 'history'));
    }

    // 2. Halaman Baru: Menampilkan SELURUH Kuis Sesuai Paket
    public function allQuizzes()
    {
        $user = auth()->user();
        
        // Asumsi ID Super Admin Pusat adalah 1.
        $superAdminId = 1; 

        // Terapkan isolasi query yang sama persis seperti di dashboard utama
if ($user->instansi_id !== null) {
            // 🏫 SISWA INSTANSI: Tampilkan kuis buatan sekolahnya yang kelasnya COCOK dengan siswa,
            // ATAU kuis yang class_group-nya NULL (diperuntukkan bagi semua kelas di sekolah tersebut)
            $quizzes = Quiz::where('created_by', $user->instansi_id)
                           ->where(function($query) use ($user) {
                               $query->where('class_group', $user->class_group)
                                     ->orWhereNull('class_group');
                           })
                           ->latest()
                           ->get();
        } elseif ($user->subscription === 'siswa_khusus') {
            // ⚓ TES KHUSUS
            $quizzes = Quiz::where('created_by', $superAdminId)
                           ->where('tier_access', 'khusus')
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
        
        return view('peserta.quiz-all', compact('quizzes'));
    }

    public function showQuiz(Quiz $quiz)
    {
        $user = auth()->user();
        $superAdminId = 1;

        // PROTEKSI SAAS: Cegah loncat URL
        if ($user->instansi_id !== null) {
            if ($quiz->created_by !== $user->instansi_id) {
                abort(403, 'Anda tidak memiliki hak akses untuk mengerjakan kuis instansi lain.');
            }

            // ⛔ PENTING: Cegah Amin mengerjakan kuis Nima via direct URL
            if ($quiz->class_group !== null && $quiz->class_group !== $user->class_group) {
                abort(403, 'Kuis ini khusus ditujukan untuk kelas: ' . $quiz->class_group);
            }
        } else {
            if ($quiz->created_by !== $superAdminId) {
                abort(403, 'Siswa mandiri tidak dapat mengakses kuis milik instansi.');
            }
            
            // Aturan Isolasi Tes Khusus (Marlins)
            if ($user->subscription === 'siswa_khusus' && $quiz->tier_access !== 'khusus') {
                abort(403, 'Akun Anda hanya diizinkan untuk mengakses Tes Khusus.');
            }
            if ($user->subscription !== 'siswa_khusus' && $quiz->tier_access === 'khusus') {
                abort(403, 'Kuis ini khusus untuk pendaftar Tes Khusus.');
            }
            
            // Aturan Hierarki Premium > Basic
            if ($user->subscription === 'siswa_basic' && $quiz->tier_access === 'premium') {
                abort(403, 'Konten ini hanya tersedia untuk paket Siswa Premium.');
            }
        }

        // Mengambil semua soal yang ada di kuis ini
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

    // 2. Memproses jawaban dan menghitung skor
    public function submitQuiz(Request $request, Quiz $quiz)
    {
        // Tangkap data kiriman dari JavaScript form di view tadi
        $score = $request->input('final_score');
        $answers = json_decode($request->input('peserta_answers'), true);

        // Simpan data bersihnya langsung ke tabel quiz_results
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

    // 3. Halaman Baru: Membedah Lembar Jawaban Detail Benar/Salah (Poin 2)
    public function detailHistory(QuizResult $result)
    {
        // Proteksi keamanan: Peserta dilarang mengintip hasil riwayat milik orang lain!
        if ($result->user_id !== Auth::id()) {
            abort(403, 'Kamu tidak memiliki akses untuk melihat lembar jawaban ini.');
        }

        // Ambil data kuis dan seluruh pertanyaan yang ada di kuis tersebut
        $quiz = $result->quiz;
        $questions = Question::where('quiz_id', $quiz->id)->get();

        return view('peserta.riwayat-detail', compact('result', 'quiz', 'questions'));
    }
}