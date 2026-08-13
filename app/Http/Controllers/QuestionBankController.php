<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use App\Models\BankPart;
use App\Models\BankQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BankQuestionImport;

class QuestionBankController extends Controller
{
    public function index()
    {
        $banks = QuestionBank::withCount('parts')->latest()->get();
        return view('admin.bank.index', compact('banks'));
    }

    public function storeBank(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        QuestionBank::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Wadah Bank Soal berhasil dibuat!');
    }

    public function updateBank(Request $request, QuestionBank $bank)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $bank->update(['name' => $request->name]);
        return redirect()->back()->with('success', 'Nama Kategori Bank Soal berhasil diperbarui!');
    }

    public function destroyBank(QuestionBank $bank)
    {
        $bank->delete();
        return redirect()->back()->with('success', 'Bank Soal berhasil dihapus!');
    }

    public function showParts(QuestionBank $bank)
    {
        $parts = BankPart::where('question_bank_id', $bank->id)->withCount('questions')->get();
        return view('admin.bank.parts', compact('bank', 'parts'));
    }

    public function storePart(Request $request, QuestionBank $bank)
    {
        $request->validate(['part_name' => 'required|string|max:255']);
        
        BankPart::create([
            'question_bank_id' => $bank->id,
            'part_name' => $request->part_name
        ]);

        return redirect()->back()->with('success', 'Part baru berhasil ditambahkan!');
    }

    public function updatePart(Request $request, BankPart $part)
    {
        $request->validate(['part_name' => 'required|string|max:255']);
        $part->update(['part_name' => $request->part_name]);
        return redirect()->back()->with('success', 'Nama Part Bank Soal berhasil diperbarui!');
    }

    public function destroyPart(BankPart $part)
    {
        $part->delete();
        return redirect()->back()->with('success', 'Part berhasil dihapus!');
    }

    public function showBankQuestions(BankPart $part)
    {
        $bank = $part->questionBank;
        $questions = BankQuestion::where('bank_part_id', $part->id)->latest()->get();

        return view('admin.bank.questions', compact('bank', 'part', 'questions'));
    }

    public function storeBankQuestion(Request $request, BankPart $part)
    {
        $request->validate([
            'type' => 'required|in:multiple_choice,essay',
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'audio' => 'nullable|extensions:mp3,wav,ogg,aac,m4a,opus|max:10240',
            'selected_image_path' => 'nullable|string',
            'selected_audio_path' => 'nullable|string',
            'explanation' => 'nullable|string',
            'correct_answer_mc' => 'required_if:type,multiple_choice|in:A,B,C,D|nullable',
            'correct_answer_essay' => 'required_if:type,essay|string|nullable',
            'option_a_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_b_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_c_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_d_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Prioritas 1: File Upload Manual Baru. Fallback: Path dari Galeri
        $imagePath = $request->hasFile('image') 
            ? $request->file('image')->store('bank/images', 'public') 
            : $request->input('selected_image_path');

        $audioPath = $request->hasFile('audio') 
            ? $request->file('audio')->store('bank/audios', 'public') 
            : $request->input('selected_audio_path');

        $options = [];
        $correctAnswer = null;

        if ($request->type === 'multiple_choice') {
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

        BankQuestion::create([
            'bank_part_id' => $part->id,
            'type' => $request->type,
            'question_text' => $request->question_text,
            'image' => $imagePath,
            'audio' => $audioPath,
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'explanation' => $request->explanation,
        ]);

        return redirect()->back()->with('success', 'Soal baru berhasil ditambahkan ke Bank Soal!');
    }

    public function importExcelPlaceholder(Request $request, BankPart $part)
    {
        $request->validate(['excel_file' => 'required|file|mimes:xlsx,xls,csv|max:2048']);

        try {
            Excel::import(new BankQuestionImport($part->id), $request->file('excel_file'));
            return redirect()->back()->with('success', 'Berhasil mengimpor koleksi soal secara massal dari file Excel!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor file Excel. Pastikan struktur kolom sesuai dengan template.');
        }
    }

    public function updateBankQuestion(Request $request, BankQuestion $question)
    {
        $request->validate([
            'type' => 'required|in:multiple_choice,essay',
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'audio' => 'nullable|extensions:mp3,wav,ogg,aac,m4a,opus|max:10240',
            'selected_image_path' => 'nullable|string',
            'selected_audio_path' => 'nullable|string',
            'explanation' => 'nullable|string',
            'correct_answer_mc' => 'required_if:type,multiple_choice|in:A,B,C,D|nullable',
            'correct_answer_essay' => 'required_if:type,essay|string|nullable',
            'option_a_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_b_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_c_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_d_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update Gambar Utama
        if ($request->hasFile('image')) {
            if ($question->image && Storage::disk('public')->exists($question->image)) {
                Storage::disk('public')->delete($question->image);
            }
            $question->image = $request->file('image')->store('bank/images', 'public');
        } elseif ($request->filled('selected_image_path')) {
            $question->image = $request->input('selected_image_path');
        }

        // Update Audio Utama
        if ($request->hasFile('audio')) {
            if ($question->audio && Storage::disk('public')->exists($question->audio)) {
                Storage::disk('public')->delete($question->audio);
            }
            $question->audio = $request->file('audio')->store('bank/audios', 'public');
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

        $question->update([
            'type' => $request->type,
            'question_text' => $request->question_text,
            'image' => $question->image,
            'audio' => $question->audio,
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'explanation' => $request->explanation,
        ]);

        return redirect()->back()->with('success', 'Soal di Bank Soal berhasil diperbarui!');
    }

    public function destroyBankQuestion(BankQuestion $question)
    {
        if ($question->image && Storage::disk('public')->exists($question->image)) {
            Storage::disk('public')->delete($question->image);
        }

        if ($question->audio && Storage::disk('public')->exists($question->audio)) {
            Storage::disk('public')->delete($question->audio);
        }

        $question->delete();

        return redirect()->back()->with('success', 'Soal berhasil dihapus dari Bank Soal!');
    }
}