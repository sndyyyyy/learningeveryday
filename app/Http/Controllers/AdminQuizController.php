<?php

namespace App\Http\Controllers;

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
        // Mengambil semua soal yang berelasi dengan kuis ini
        $questions = Question::where('quiz_id', $quiz->id)->get();
        return view('admin.quiz.questions', compact('quiz', 'questions'));
    }

    // 4. Menyimpan Soal Baru ke dalam Kuis
public function storeQuestion(Request $request, Quiz $quiz)
{
    $request->validate([
        'question_text' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        'audio' => 'nullable|mimes:mp3,wav,ogg|max:5120', // Max 5MB
        'option_a' => 'required|string',
        'option_b' => 'required|string',
        'option_c' => 'required|string',
        'option_d' => 'required|string',
        'correct_answer' => 'required|in:A,B,C,D',
        'explanation' => 'nullable|string',
    ]);

    $imagePath = null;
    $audioPath = null;

    // Proses upload gambar jika ada
    if ($request->hasFile('image')) {
        // Menyimpan ke folder storage/app/public/questions/images
        $imagePath = $request->file('image')->store('questions/images', 'public');
    }

    // Proses upload audio jika ada
    if ($request->hasFile('audio')) {
        // Menyimpan ke folder storage/app/public/questions/audios
        $audioPath = $request->file('audio')->store('questions/audios', 'public');
    }

    Question::create([
        'quiz_id' => $quiz->id,
        'question_text' => $request->question_text,
        'image' => $imagePath, // Menyimpan path file gambar
        'audio' => $audioPath, // Menyimpan path file audio
        'options' => [
            'A' => $request->option_a,
            'B' => $request->option_b,
            'C' => $request->option_c,
            'D' => $request->option_d,
        ],
        'correct_answer' => $request->correct_answer,
        'explanation' => $request->explanation,
    ]);

    return redirect()->back()->with('success', 'Soal bermedia berhasil ditambahkan!');
}

public function updateQuestion(Request $request, Question $question)
{
    $request->validate([
        'question_text' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'audio' => 'nullable|mimes:mp3,wav,ogg|max:5120',
        'option_a' => 'required|string',
        'option_b' => 'required|string',
        'option_c' => 'required|string',
        'option_d' => 'required|string',
        'correct_answer' => 'required|in:A,B,C,D',
        'explanation' => 'nullable|string',
    ]);

    // Update data teks dasar
    $question->question_text = $question_text = $request->question_text;
    $question->correct_answer = $request->correct_answer;
    $question->explanation = $request->explanation;
    $question->options = [
        'A' => $request->option_a,
        'B' => $request->option_b,
        'C' => $request->option_c,
        'D' => $request->option_d,
    ];

    // === LOGIKA BARU: HAPUS MEDIA VIA CHECKBOX ===

    // 1. Cek apakah centang "Hapus Gambar" aktif (dan tidak ada upload gambar baru)
    if ($request->has('delete_image') && !$request->hasFile('image')) {
        if ($question->image) {
            Storage::disk('public')->delete($question->image);
            $question->image = null; // Kosongkan database
        }
    }

    // 2. Cek apakah centang "Hapus Audio" aktif (dan tidak ada upload audio baru)
    if ($request->has('delete_audio') && !$request->hasFile('audio')) {
        if ($question->audio) {
            Storage::disk('public')->delete($question->audio);
            $question->audio = null; // Kosongkan database
        }
    }

    // =============================================

    // Jika admin malah mengunggah GAMBAR BARU, otomatis gantikan yang lama
    if ($request->hasFile('image')) {
        if ($question->image) {
            Storage::disk('public')->delete($question->image);
        }
        $question->image = $request->file('image')->store('questions/images', 'public');
    }

    // Jika admin malah mengunggah AUDIO BARU, otomatis gantikan yang lama
    if ($request->hasFile('audio')) {
        if ($question->audio) {
            Storage::disk('public')->delete($question->audio);
        }
        $question->audio = $request->file('audio')->store('questions/audios', 'public');
    }

    $question->save();

    return redirect()->back()->with('success', 'Soal berhasil diperbarui!');
}

// 2. Menghapus Soal Beserta File Medianya dari Hardisk
public function destroyQuestion(Question $question)
{
    // Hapus file fisik dari storage agar tidak memenuhi hosting/laptop
    if ($question->image) {
        Storage::disk('public')->delete($question->image);
    }
    if ($question->audio) {
        Storage::disk('public')->delete($question->audio);
    }

    $question->delete();

    return redirect()->back()->with('success', 'Soal berhasil dihapus dari kuis!');
}

    public function destroyQuiz(Quiz $quiz)
{
    $quiz->delete();
    return redirect()->back()->with('success', 'Kuis beserta seluruh soal di dalamnya berhasil dihapus!');
}
}