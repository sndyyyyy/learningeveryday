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
    // Halaman list Bank Soal Utama
    public function index()
    {
        // Mengambil seluruh bank soal beserta jumlah part di dalamnya
        $banks = QuestionBank::withCount('parts')->latest()->get();
        return view('admin.bank.index', compact('banks'));
    }

    // Menyimpan Label Bank Soal Baru (Misal: Reading Test)
    public function storeBank(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        QuestionBank::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Wadah Bank Soal berhasil dibuat!');
    }

    public function updateBank(Request $request, QuestionBank $bank)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $bank->update([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Nama Kategori Bank Soal berhasil diperbarui!');
    }

    // Menghapus Bank Soal permanen beserta isinya (cascade)
    public function destroyBank(QuestionBank $bank)
    {
        $bank->delete();
        return redirect()->back()->with('success', 'Bank Soal berhasil dihapus!');
    }

    // Halaman Detail Part di dalam salah satu Bank Soal
    public function showParts(QuestionBank $bank)
    {
        // Mengambil part yang terikat ke Bank Soal ini beserta kalkulasi jumlah soalnya
        $parts = BankPart::where('question_bank_id', $bank->id)->withCount('questions')->get();
        return view('admin.bank.parts', compact('bank', 'parts'));
    }

    // Membuat Part baru di dalam Bank Soal (Misal: Part 1, Part 2)
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
        $request->validate([
            'part_name' => 'required|string|max:255'
        ]);

        $part->update([
            'part_name' => $request->part_name
        ]);

        return redirect()->back()->with('success', 'Nama Part Bank Soal berhasil diperbarui!');
    }

    // Menghapus Part
    public function destroyPart(BankPart $part)
    {
        $part->delete();
        return redirect()->back()->with('success', 'Part berhasil dihapus!');
    }

    public function showBankQuestions(BankPart $part)
    {
        // Mengambil data wadah bank soal utamanya untuk info di breadcrumb UI
        $bank = $part->questionBank;
        
        // Mengambil seluruh soal yang terikat ke part ini
        $questions = BankQuestion::where('bank_part_id', $part->id)->latest()->get();

        return view('admin.bank.questions', compact('bank', 'part', 'questions'));
    }

    // 2. Menyimpan Soal ke Bank Soal secara Manual 1-per-1
    public function storeBankQuestion(Request $request, BankPart $part)
    {
        // Validasi dibikin dinamis tergantung tipe soal
        $request->validate([
            'type' => 'required|in:multiple_choice,essay',
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'audio' => 'nullable|extensions:mp3,wav,ogg,aac,m4a,opus|max:10240',
            'explanation' => 'nullable|string',
            
            // Pilihan ganda hanya wajib kalau tipenya multiple_choice
            'option_a' => 'required_if:type,multiple_choice',
            'option_b' => 'required_if:type,multiple_choice',
            'option_c' => 'required_if:type,multiple_choice',
            'option_d' => 'required_if:type,multiple_choice',
            'correct_answer_mc' => 'required_if:type,multiple_choice|in:A,B,C,D|nullable',
            
            // Kunci jawaban essay hanya wajib kalau tipenya essay
            'correct_answer_essay' => 'required_if:type,essay|string|nullable',
        ]);

        $imagePath = $request->hasFile('image') ? $request->file('image')->store('bank/images', 'public') : null;
        $audioPath = $request->hasFile('audio') ? $request->file('audio')->store('bank/audios', 'public') : null;

        // Persiapan variabel data
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
            // Mode Essay Multi-Blank & Multi-Alias:
            // 1. Pecah berdasarkan pemisah antar-blank ('|' atau ';')
            $rawBlanks = array_map('trim', preg_split('/[|;]/', $request->correct_answer_essay));
            
            $parsedAnswers = [];
            foreach ($rawBlanks as $blank) {
                if (!empty($blank)) {
                    // 2. Pecah variasi alias dalam 1 blank berdasarkan ('/' atau ',')
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
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new BankQuestionImport($part->id), $request->file('excel_file'));

            return redirect()->back()->with('success', 'Berhasil mengimpor koleksi soal secara massal dari file Excel!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor file Excel. Pastikan struktur kolom sesuai dengan template.');
        }
    }

    // ===================================================
    // FUNGSI UPDATE DATA SOAL DI BANK SOAL
    // ===================================================
    public function updateBankQuestion(Request $request, BankQuestion $question)
    {
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

        // Manajemen File Gambar
        if ($request->hasFile('image')) {
            if ($question->image) {
                Storage::disk('public')->delete($question->image);
            }
            $question->image = $request->file('image')->store('bank/images', 'public');
        }

        // Manajemen File Audio
        if ($request->hasFile('audio')) {
            if ($question->audio) {
                Storage::disk('public')->delete($question->audio);
            }
            $question->audio = $request->file('audio')->store('bank/audios', 'public');
        }

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

    // ===================================================
    // FUNGSI HAPUS SOAL PERMANEN DARI BANK SOAL
    // ===================================================
    public function destroyBankQuestion(BankQuestion $question)
    {
        if ($question->image) {
            Storage::disk('public')->delete($question->image);
        }

        if ($question->audio) {
            Storage::disk('public')->delete($question->audio);
        }

        $question->delete();

        return redirect()->back()->with('success', 'Soal berhasil dihapus dari Bank Soal!');
    }
}