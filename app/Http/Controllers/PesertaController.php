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
        // Ambil kuis yang tersedia, urutkan dari yang terbaru, lalu batasi hanya 3 data saja
        $quizzes = Quiz::latest()->take(3)->get();

        // Ambil riwayat kuis (sementara biarkan ambil semua, nanti kita urus di tahap berikutnya)
        $history = QuizResult::where('user_id', Auth::id())
            ->with('quiz')
            ->latest()
            ->take(3)
            ->get();

        return view('peserta.dashboard', compact('quizzes', 'history'));
    }

    // 2. Halaman Baru: Menampilkan SELURUH Kuis Tanpa Batasan (Poin 1)
    public function allQuizzes()
    {
        // Ambil semua data kuis dari database tanpa batasan jumlah
        $quizzes = Quiz::latest()->get();

        return view('peserta.quiz-all', compact('quizzes'));
    }

    public function showQuiz(Quiz $quiz)
    {
        // Mengambil semua soal yang ada di kuis ini
        $questions = Question::with('bankPart') // Tarik data nama part-nya sekalian
            ->where('quiz_id', $quiz->id)
            ->orderBy('bank_part_id', 'asc') // Kelompokkan soal berdasarkan part/section agar tidak tercampur
            ->inRandomOrder() // Soal di dalam part yang sama tetap teracak urutannya bagi tiap peserta
            ->get();
        // Jika kuis belum ada soalnya, balikin ke dashboard dengan pesan peringatan
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
