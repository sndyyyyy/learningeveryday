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
    public function index()
    {
        $user = auth()->user();

        // JIKA SUPER ADMIN: Tampilkan semua kuis yang ada di sistem
        if ($user->role === 'super_admin') {
            $quizzes = Quiz::latest()->get();
        } else {
            // JIKA ADMIN INSTANSI: Hanya tampilkan kuis yang dibuat oleh akun dia sendiri
            $quizzes = Quiz::where('created_by', $user->id)->latest()->get();
        }

        return view('admin.quiz.index', compact('quizzes'));
    }

    // Menyimpan Kuis Baru dengan Label Akses Tier
    public function storeQuiz(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tier_access' => 'required_if:auth_role,super_admin|in:basic,premium,khusus',
        ]);

        $tierAccess = ($user->role === 'super_admin') ? $request->tier_access : 'basic';

        Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => $user->id, 
            'tier_access' => $tierAccess, 
        ]);

        return redirect()->back()->with('success', 'Kuis baru berhasil dibuat!');
    }

    // =========================================================================
    // FITUR BARU: MEMPROSES UPDATE DATA KUIS (SUPER ADMIN & ADMIN INSTANSI)
    // =========================================================================
    public function updateQuiz(Request $request, Quiz $quiz)
    {
        $user = auth()->user();

        // Proteksi: Admin instansi dilarang mengedit kuis milik instansi lain
        if ($user->role !== 'super_admin' && $quiz->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah kuis ini.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tier_access' => 'required_if:auth_role,super_admin|in:basic,premium,khusus',
        ]);

        // Jika super admin, tier_access bisa diubah. Jika admin instansi, biarkan tetap data lama
        $tierAccess = ($user->role === 'super_admin') ? $request->tier_access : $quiz->tier_access;

        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'tier_access' => $tierAccess,
        ]);

        return redirect()->back()->with('success', 'Data kuis berhasil diperbarui!');
    }

    public function showQuestions(Quiz $quiz)
    {
        $questions = Question::where('quiz_id', $quiz->id)->oldest()->get();
        $bankSoalList = QuestionBank::with('parts')->get();

        return view('admin.quiz.questions', compact('quiz', 'questions', 'bankSoalList'));
    }

    public function pullFromBankSoal(Request $request, Quiz $quiz)
    {
        $request->validate([
            'bank_part_id' => 'required|exists:bank_parts,id'
        ]);

        $bankQuestions = BankQuestion::where('bank_part_id', $request->bank_part_id)->get();

        if ($bankQuestions->isEmpty()) {
            return redirect()->back()->with('error', 'Part Bank Soal yang dipilih ternyata masih kosong, tidak ada soal yang bisa ditarik!');
        }

        $copiedCount = 0;
        foreach ($bankQuestions as $bq) {
            $exists = Question::where('quiz_id', $quiz->id)
                ->where('question_text', $bq->question_text)
                ->exists();

            if (!$exists) {
                Question::create([
                    'quiz_id' => $quiz->id,
                    'type' => $bq->type, 
                    'bank_part_id' => $bq->bank_part_id,
                    'question_text' => $bq->question_text,
                    'image' => $bq->image,
                    'audio' => $bq->audio,
                    'options' => $bq->options, 
                    'correct_answer' => $bq->correct_answer,
                    'explanation' => $bq->explanation,
                    'explanation_link' => $bq->explanation_link,
                    'is_show_explanation' => $bq->is_show_explanation ?? true
                ]);
                $copiedCount++;
            }
        }

        return redirect()->back()->with('success', "Berhasil menarik {$copiedCount} soal dari Bank Soal ke dalam kuis ini!");
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $request->validate([
            'type' => 'required|in:multiple_choice,essay',
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'audio' => 'nullable|extensions:mp3,wav,ogg,aac,m4a,opus|max:10240',
            'explanation' => 'nullable|string',
            'explanation_link' => 'nullable|url',
            'is_show_explanation' => 'required|boolean',
            'option_a' => 'required_if:type,multiple_choice',
            'option_b' => 'required_if:type,multiple_choice',
            'option_c' => 'required_if:type,multiple_choice',
            'option_d' => 'required_if:type,multiple_choice',
            'correct_answer_mc' => 'required_if:type,multiple_choice|in:A,B,C,D|nullable',
            'correct_answer_essay' => 'required_if:type,essay|string|nullable',
        ]);

        $imagePath = $request->hasFile('image') ? $request->file('image')->store('questions/images', 'public') : null;
        $audioPath = $request->hasFile('audio') ? $request->file('audio')->store('questions/audios', 'public') : null;

        $options = [];
        $correctAnswer = null;

        if ($request->type === 'multiple_choice') {
            $options = [
                'A' => $request->option_a, 'B' => $request->option_b, 'C' => $request->option_c, 'D' => $request->option_d,
            ];
            $correctAnswer = $request->correct_answer_mc;
        } else {
            $answersArray = array_map('trim', explode(',', $request->correct_answer_essay));
            $correctAnswer = json_encode($answersArray);
        }

        Question::create([
            'quiz_id' => $quiz->id,
            'type' => $request->type, 
            'question_text' => $request->question_text,
            'image' => $imagePath,
            'audio' => $audioPath,
            'options' => $options, 
            'correct_answer' => $correctAnswer,
            'explanation' => $request->explanation,
            'explanation_link' => $request->explanation_link,
            'is_show_explanation' => $request->is_show_explanation,
        ]);

        return redirect()->back()->with('success', 'Soal bermedia berhasil ditambahkan!');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        $request->validate([
            'type' => 'required|in:multiple_choice,essay',
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'audio' => 'nullable|extensions:mp3,wav,ogg,aac,m4a,opus|max:10240',
            'explanation' => 'nullable|string',
            'explanation_link' => 'nullable|url',
            'is_show_explanation' => 'required|boolean',
            'option_a' => 'required_if:type,multiple_choice',
            'option_b' => 'required_if:type,multiple_choice',
            'option_c' => 'required_if:type,multiple_choice',
            'option_d' => 'required_if:type,multiple_choice',
            'correct_answer_mc' => 'required_if:type,multiple_choice|in:A,B,C,D|nullable',
            'correct_answer_essay' => 'required_if:type,essay|string|nullable',
        ]);

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

        $options = [];
        $correctAnswer = null;

        if ($request->type === 'multiple_choice') {
            $options = [
                'A' => $request->option_a, 'B' => $request->option_b, 'C' => $request->option_c, 'D' => $request->option_d,
            ];
            $correctAnswer = $request->correct_answer_mc;
        } else {
            $answersArray = array_map('trim', explode(',', $request->correct_answer_essay));
            $correctAnswer = json_encode($answersArray);
        }

        $question->type = $request->type;
        $question->question_text = $request->question_text;
        $question->options = $options;
        $question->correct_answer = $correctAnswer;
        $question->explanation = $request->explanation;
        $question->explanation_link = $request->explanation_link;
        $question->is_show_explanation = $request->is_show_explanation;
        
        $question->save();

        return redirect()->back()->with('success', 'Soal berhasil diperbarui!');
    }

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

    public function destroyQuiz(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->back()->with('success', 'Kuis beserta seluruh soal di dalamnya berhasil dihapus!');
    }
}