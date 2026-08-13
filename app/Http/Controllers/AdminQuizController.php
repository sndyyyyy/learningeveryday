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

        if ($user->role === 'super_admin') {
            $quizzes = Quiz::latest()->get();
            $classGroups = collect(); 
            $specialTests = \App\Models\SpecialTest::oldest('name')->get();
        } else {
            $quizzes = Quiz::where('created_by', $user->id)->latest()->get();
            $classGroups = \App\Models\ClassGroup::where('instansi_id', $user->id)->oldest('name')->get();
            $specialTests = collect();
        }

        return view('admin.quiz.index', compact('quizzes', 'classGroups', 'specialTests'));
    }

    public function storeQuiz(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tier_access' => 'required_if:auth_role,super_admin|in:basic,premium,khusus',
            'class_group' => 'nullable|string|max:50',
        ]);

        $tierAccess = ($user->role === 'super_admin') ? $request->tier_access : 'basic';

        Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => $user->id,
            'tier_access' => $tierAccess,
            'class_group' => $request->class_group,
        ]);

        return redirect()->back()->with('success', 'Kuis baru berhasil dibuat!');
    }

    public function updateQuiz(Request $request, Quiz $quiz)
    {
        $user = auth()->user();

        if ($user->role !== 'super_admin' && $quiz->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah kuis ini.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tier_access' => 'required_if:auth_role,super_admin|in:basic,premium,khusus',
            'class_group' => 'nullable|string|max:50',
        ]);

        $tierAccess = ($user->role === 'super_admin') ? $request->tier_access : $quiz->tier_access;

        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'tier_access' => $tierAccess,
            'class_group' => $request->class_group,
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
            'selected_image_path' => 'nullable|string',
            'selected_audio_path' => 'nullable|string',
            'explanation' => 'nullable|string',
            'explanation_link' => 'nullable|url',
            'is_show_explanation' => 'required|boolean',
            'correct_answer_mc' => 'required_if:type,multiple_choice|in:A,B,C,D|nullable',
            'correct_answer_essay' => 'required_if:type,essay|string|nullable',
            'option_a_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_b_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_c_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_d_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Prioritas 1: File Upload Manual Baru. Fallback: Path dari Galeri
        $imagePath = $request->hasFile('image') 
            ? $request->file('image')->store('questions/images', 'public') 
            : $request->input('selected_image_path');

        $audioPath = $request->hasFile('audio') 
            ? $request->file('audio')->store('questions/audios', 'public') 
            : $request->input('selected_audio_path');

        $options = [];
        $correctAnswer = null;

        if ($request->type === 'multiple_choice') {
            // Logika Opsi A, B, C, D (Gambar File / Path Galeri / Teks)
            foreach (['a', 'b', 'c', 'd'] as $opt) {
                $upper = strtoupper($opt);
                if ($request->hasFile("option_{$opt}_file")) {
                    $options[$upper] = $request->file("option_{$opt}_file")->store('options/images', 'public');
                } elseif ($request->filled("selected_option_{$opt}_path")) {
                    $options[$upper] = $request->input("selected_option_{$opt}_path");
                } else {
                    $options[$upper] = $request->input("option_{$opt}");
                }
            }
            $correctAnswer = $request->correct_answer_mc;
        } else {
            $rawBlanks = array_map('trim', preg_split('/[|;]/', $request->correct_answer_essay));
            $parsedAnswers = [];
            foreach ($rawBlanks as $blank) {
                if (!empty($blank)) {
                    $aliases = array_map(function($item) {
                        return mb_strtolower(trim($item));
                    }, preg_split('/[\/,]/', $blank));
                    
                    $parsedAnswers[] = array_values(array_filter($aliases));
                }
            }
            $correctAnswer = json_encode($parsedAnswers);
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
            'selected_image_path' => 'nullable|string',
            'selected_audio_path' => 'nullable|string',
            'explanation' => 'nullable|string',
            'explanation_link' => 'nullable|url',
            'is_show_explanation' => 'required|boolean',
            'correct_answer_mc' => 'required_if:type,multiple_choice|in:A,B,C,D|nullable',
            'correct_answer_essay' => 'required_if:type,essay|string|nullable',
            'option_a_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_b_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_c_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_d_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->has('delete_image') && !$request->hasFile('image')) {
            if ($question->image && Storage::disk('public')->exists($question->image)) {
                Storage::disk('public')->delete($question->image);
            }
            $question->image = null;
        }
        if ($request->has('delete_audio') && !$request->hasFile('audio')) {
            if ($question->audio && Storage::disk('public')->exists($question->audio)) {
                Storage::disk('public')->delete($question->audio);
            }
            $question->audio = null;
        }

        // Update Gambar Utama
        if ($request->hasFile('image')) {
            if ($question->image && Storage::disk('public')->exists($question->image)) {
                Storage::disk('public')->delete($question->image);
            }
            $question->image = $request->file('image')->store('questions/images', 'public');
        } elseif ($request->filled('selected_image_path')) {
            $question->image = $request->input('selected_image_path');
        }

        // Update Audio Utama
        if ($request->hasFile('audio')) {
            if ($question->audio && Storage::disk('public')->exists($question->audio)) {
                Storage::disk('public')->delete($question->audio);
            }
            $question->audio = $request->file('audio')->store('questions/audios', 'public');
        } elseif ($request->filled('selected_audio_path')) {
            $question->audio = $request->input('selected_audio_path');
        }

        $options = [];
        $correctAnswer = null;

        if ($request->type === 'multiple_choice') {
            $existingOptions = $question->options ?? [];
            
            foreach (['a', 'b', 'c', 'd'] as $opt) {
                $upper = strtoupper($opt);
                if ($request->hasFile("option_{$opt}_file")) {
                    $options[$upper] = $request->file("option_{$opt}_file")->store('options/images', 'public');
                } elseif ($request->filled("selected_option_{$opt}_path")) {
                    $options[$upper] = $request->input("selected_option_{$opt}_path");
                } elseif ($request->filled("option_{$opt}")) {
                    $options[$upper] = $request->input("option_{$opt}");
                } else {
                    $options[$upper] = $existingOptions[$upper] ?? '';
                }
            }
            $correctAnswer = $request->correct_answer_mc;
        } else {
            $rawBlanks = array_map('trim', preg_split('/[|;]/', $request->correct_answer_essay));
            $parsedAnswers = [];
            foreach ($rawBlanks as $blank) {
                if (!empty($blank)) {
                    $aliases = array_map(function($item) {
                        return mb_strtolower(trim($item));
                    }, preg_split('/[\/,]/', $blank));
                    $parsedAnswers[] = array_values(array_filter($aliases));
                }
            }
            $correctAnswer = json_encode($parsedAnswers);
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
        if ($question->image && Storage::disk('public')->exists($question->image)) {
            Storage::disk('public')->delete($question->image);
        }
        if ($question->audio && Storage::disk('public')->exists($question->audio)) {
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

    public function toggleAllExplanations(Request $request, Quiz $quiz)
    {
        $request->validate(['status' => 'required|boolean']);

        $status = $request->status;
        Question::where('quiz_id', $quiz->id)->update(['is_show_explanation' => $status]);

        $pesan = $status ? 'Seluruh pembahasan soal berhasil DITAMPILKAN!' : 'Seluruh pembahasan soal berhasil DISEMBUNYIKAN!';

        return redirect()->back()->with('success', $pesan);
    }

    public function getQuizReportData(Quiz $quiz)
    {
        $user = auth()->user();

        if ($user->role !== 'super_admin' && $quiz->created_by !== $user->id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $results = \App\Models\QuizResult::where('quiz_id', $quiz->id)
                    ->with('user')
                    ->latest()
                    ->get()
                    ->map(function($res) {
                        return [
                            'student_name' => $res->user?->name ?? 'Siswa Terhapus',
                            'class_group'  => $res->user?->class_group ?? '-',
                            'score'        => $res->score,
                            'date'         => $res->created_at->format('d M Y, H:i') . ' WIB',
                        ];
                    });

        return response()->json([
            'quiz_title' => $quiz->title,
            'total_participants' => $results->count(),
            'average_score' => $results->count() > 0 ? round($results->avg('score'), 1) : 0,
            'results' => $results
        ]);
    }

    public function exportQuizPdf(Quiz $quiz)
    {
        $user = auth()->user();

        if ($user->role !== 'super_admin' && $quiz->created_by !== $user->id) {
            abort(403, 'Anda tidak berhak mencetak kuis ini.');
        }

        $instansi = \App\Models\User::find($quiz->created_by);

        $mcQuestions = \App\Models\Question::where('quiz_id', $quiz->id)->where('type', 'multiple_choice')->oldest()->get();
        $essayQuestions = \App\Models\Question::where('quiz_id', $quiz->id)->where('type', 'essay')->oldest()->get();

        return view('admin.quiz.print-pdf', compact('quiz', 'mcQuestions', 'essayQuestions', 'instansi'));
    } 

    public function exportQuizWord(Quiz $quiz)
    {
        $user = auth()->user();

        if ($user->role !== 'super_admin' && $quiz->created_by !== $user->id) {
            abort(403, 'Anda tidak berhak mengunduh kuis ini.');
        }

        $instansi = \App\Models\User::find($quiz->created_by);

        $mcQuestions = \App\Models\Question::where('quiz_id', $quiz->id)->where('type', 'multiple_choice')->oldest()->get();
        $essayQuestions = \App\Models\Question::where('quiz_id', $quiz->id)->where('type', 'essay')->oldest()->get();

        $filename = 'Naskah_Soal_' . \Illuminate\Support\Str::slug($quiz->title) . '.doc';

        return response()
            ->view('admin.quiz.print-word', compact('quiz', 'mcQuestions', 'essayQuestions', 'instansi'))
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}