<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use App\Models\BankPart;
use App\Models\BankQuestion;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminQuizController extends Controller
{
    // 1. Menampilkan semua kuis yang ada
    public function index()
    {
        $quizzes = Quiz::latest()->get();
        return view('admin.quiz.index', compact('quizzes'));
    }

    // 2. Menyimpan Kuis Baru
    public function storeQuiz(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => Auth::id(), // ID Admin yang sedang login
        ]);

        return redirect()->back()->with('success', 'Kuis baru berhasil dibuat!');
    }

    // 3. Menampilkan Halaman Kelola Soal berdasarkan Kuis tertentu
    public function showQuestions(Quiz $quiz)
    {
        $questions = Question::where('quiz_id', $quiz->id)->oldest()->get();
        $bankSoalList = QuestionBank::with('parts')->get();

        return view('admin.quiz.questions', compact('quiz', 'questions', 'bankSoalList'));
    }

    // 4. Logika Menyalin Soal dari Bank Soal ke Tabel Questions Kuis
    public function pullFromBankSoal(Request $request, Quiz $quiz)
    {
        $request->validate([
            'bank_part_id' => 'required|exists:bank_parts,id'
        ]);

        // Ambil semua soal yang ada di dalam Part Bank Soal yang dipilih admin
        $bankQuestions = BankQuestion::where('bank_part_id', $request->bank_part_id)->get();

        if ($bankQuestions->isEmpty()) {
            return redirect()->back()->with('error', 'Part Bank Soal yang dipilih ternyata masih kosong, tidak ada soal yang bisa ditarik!');
        }

        $copiedCount = 0;
        foreach ($bankQuestions as $bq) {
            // Cek duplikasi: Agar soal yang sama dari part ini tidak masuk dua kali ke kuis ini
            $exists = Question::where('quiz_id', $quiz->id)
                ->where('question_text', $bq->question_text)
                ->exists();

            if (!$exists) {
                // Salin record dari tabel bank_questions ke tabel questions milik kuis
                Question::create([
                    'quiz_id' => $quiz->id,
                    'type' => $bq->type, // ✔️ PASTIKAN TIPE SOAL IKUT TERSALIN
                    'bank_part_id' => $bq->bank_part_id,
                    'question_text' => $bq->question_text,
                    'image' => $bq->image,
                    'audio' => $bq->audio,
                    'options' => $bq->options, // Data array akan aman karena cast otomatis
                    'correct_answer' => $bq->correct_answer,
                    'explanation' => $bq->explanation
                ]);
                $copiedCount++;
            }
        }

        return redirect()->back()->with('success', "Berhasil menarik {$copiedCount} soal dari Bank Soal ke dalam kuis ini!");
    }

    // 5. Menyimpan Soal Baru ke dalam Kuis (Input Manual)
    public function storeQuestion(Request $request, Quiz $quiz)
    {
        // ✔️ VALIDASI DINAMIS BERDASARKAN TIPE SOAL
        $request->validate([
            'type' => 'required|in:multiple_choice,essay',
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'audio' => 'nullable|extensions:mp3,wav,ogg,aac,m4a,opus|max:10240',
            'explanation' => 'nullable|string',
            
            // Aturan khusus Pilihan Ganda
            'option_a' => 'required_if:type,multiple_choice',
            'option_b' => 'required_if:type,multiple_choice',
            'option_c' => 'required_if:type,multiple_choice',
            'option_d' => 'required_if:type,multiple_choice',
            'correct_answer_mc' => 'required_if:type,multiple_choice|in:A,B,C,D|nullable',
            
            // Aturan khusus Essay
            'correct_answer_essay' => 'required_if:type,essay|string|nullable',
        ]);

        $imagePath = $request->hasFile('image') ? $request->file('image')->store('questions/images', 'public') : null;
        $audioPath = $request->hasFile('audio') ? $request->file('audio')->store('questions/audios', 'public') : null;

        // ✔️ LOGIKA PEMISAHAN DATA OPTIONS & KUNCI JAWABAN
        $options = [];
        $correctAnswer = null;

        if ($request->type === 'multiple_choice') {
            $options = [
                'A' => $request->option_a,
                'B' => $request->option_b,
                'C' => $request->option_c,
                'D' => $request->option_d,
            ];
            $correctAnswer = $request->correct_answer_mc;
        } else {
            // Mode Essay: pecah dengan koma lalu jadikan JSON array
            $answersArray = array_map('trim', explode(',', $request->correct_answer_essay));
            $correctAnswer = json_encode($answersArray);
        }

        Question::create([
            'quiz_id' => $quiz->id,
            'type' => $request->type, // Simpan tipe soal
            'question_text' => $request->question_text,
            'image' => $imagePath,
            'audio' => $audioPath,
            'options' => $options, // Jika essay, ini akan terisi array kosong []
            'correct_answer' => $correctAnswer,
            'explanation' => $request->explanation,
        ]);

        return redirect()->back()->with('success', 'Soal bermedia berhasil ditambahkan!');
    }

    // 6. Update Soal Kuis
    public function updateQuestion(Request $request, Question $question)
    {
        // ✔️ VALIDASI DINAMIS BERDASARKAN TIPE SOAL (Sama seperti Store)
        $request->validate([
            'type' => 'required|in:multiple_choice,essay',
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'audio' => 'nullable|extensions:mp3,wav,ogg,aac,m4a,opus|max:10240',
            'explanation' => 'nullable|string',
            
            'option_a' => 'required_if:type,multiple_choice',
            'option_b' => 'required_if:type,multiple_choice',
            'option_c' => 'required_if:type,multiple_choice',
            'option_d' => 'required_if:type,multiple_choice',
            'correct_answer_mc' => 'required_if:type,multiple_choice|in:A,B,C,D|nullable',
            
            'correct_answer_essay' => 'required_if:type,essay|string|nullable',
        ]);

        // Proses Logika Media (Hapus via Checkbox)
        if ($request->has('delete_image') && !$request->hasFile('image')) {
            if ($question->image) {
                Storage::disk('public')->delete($question->image);
                $question->image = null;
            }
        }
        if ($request->has('delete_audio') && !$request->hasFile('audio')) {
            if ($question->audio) {
                Storage::disk('public')->delete($question->audio);
                $question->audio = null;
            }
        }

        // Proses Logika Media (Ganti Baru)
        if ($request->hasFile('image')) {
            if ($question->image) {
                Storage::disk('public')->delete($question->image);
            }
            $question->image = $request->file('image')->store('questions/images', 'public');
        }
        if ($request->hasFile('audio')) {
            if ($question->audio) {
                Storage::disk('public')->delete($question->audio);
            }
            $question->audio = $request->file('audio')->store('questions/audios', 'public');
        }

        // ✔️ LOGIKA PEMISAHAN DATA OPTIONS & KUNCI JAWABAN
        $options = [];
        $correctAnswer = null;

        if ($request->type === 'multiple_choice') {
            $options = [
                'A' => $request->option_a,
                'B' => $request->option_b,
                'C' => $request->option_c,
                'D' => $request->option_d,
            ];
            $correctAnswer = $request->correct_answer_mc;
        } else {
            $answersArray = array_map('trim', explode(',', $request->correct_answer_essay));
            $correctAnswer = json_encode($answersArray);
        }

        // Update Text dan Array
        $question->type = $request->type;
        $question->question_text = $request->question_text;
        $question->options = $options;
        $question->correct_answer = $correctAnswer;
        $question->explanation = $request->explanation;
        
        $question->save();

        return redirect()->back()->with('success', 'Soal berhasil diperbarui!');
    }

    // 7. Menghapus Soal Beserta File Medianya dari Hardisk
    public function destroyQuestion(Question $question)
    {
        if ($question->image) {
            Storage::disk('public')->delete($question->image);
        }
        if ($question->audio) {
            Storage::disk('public')->delete($question->audio);
        }

        $question->delete();

        return redirect()->back()->with('success', 'Soal berhasil dihapus dari kuis!');
    }

    // 8. Menghapus Kuis Keseluruhan
    public function destroyQuiz(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->back()->with('success', 'Kuis beserta seluruh soal di dalamnya berhasil dihapus!');
    }
}