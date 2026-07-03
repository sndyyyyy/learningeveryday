<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use App\Models\BankPart;
use App\Models\BankQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        // Mode Essay: Pecah jawaban berdasarkan koma lalu simpan ke format JSON array
        // Misal input: "Soekarno, Hatta" -> tersimpan: ["Soekarno", "Hatta"]
        $answersArray = array_map('trim', explode(',', $request->correct_answer_essay));
        $correctAnswer = json_encode($answersArray);
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

// 3. Placeholder Sementara untuk Import Excel (Biar rute tidak error pas di-klik)
// Ganti fungsi importExcelPlaceholder lama dengan kode ini:
public function importExcelPlaceholder(Request $request, BankPart $part)
{
    $request->validate([
        'excel_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048'
    ]);

    $file = $request->file('excel_file');
    $extension = $file->getClientOriginalExtension();

    if (in_array($extension, ['csv', 'txt'])) {
        $path = $file->getRealPath();
        
        if (($handle = fopen($path, "r")) !== FALSE) {
            // Lewati baris pertama (Header Excel)
            fgetcsv($handle, 1000, ","); 

            $rowCount = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Pastikan minimal ada 6 kolom terisi/terdeteksi
                if (count($data) >= 6) {
                    
                    $questionText = trim($data[0]);
                    $optA = trim($data[1]);
                    $optB = trim($data[2]);
                    $optC = trim($data[3]);
                    $optD = trim($data[4]);
                    $rawAnswer = trim($data[5]);
                    $explanation = isset($data[6]) ? trim($data[6]) : null;

                    // === 🧠 LOGIKA SMART DETECT: Pilihan Ganda vs Essay ===
                    // Jika Opsi A kosong atau isinya cuma tanda strip (-), jadikan Mode Essay
                    $isEssay = empty($optA) || $optA === '-';

                    if ($isEssay) {
                        $type = 'essay';
                        $options = []; // Kosongkan array options
                        
                        // Pecah jawaban (yang dipisah koma) menjadi JSON Array
                        $answersArray = array_map('trim', explode(',', $rawAnswer));
                        $correctAnswer = json_encode($answersArray);
                    } else {
                        $type = 'multiple_choice';
                        $options = [
                            'A' => $optA,
                            'B' => $optB,
                            'C' => $optC,
                            'D' => $optD,
                        ];
                        // Pastikan format kunci jawaban selalu huruf besar (A/B/C/D)
                        $correctAnswer = strtoupper($rawAnswer); 
                    }

                    // Eksekusi Simpan ke Database
                    BankQuestion::create([
                        'bank_part_id' => $part->id,
                        'type' => $type,
                        'question_text' => $questionText,
                        'options' => $options,
                        'correct_answer' => $correctAnswer,
                        'explanation' => $explanation,
                        'image' => null, 
                        'audio' => null 
                    ]);
                    $rowCount++;
                }
            }
            fclose($handle);
            
            return redirect()->back()->with('success', "Berhasil mengimpor {$rowCount} soal secara massal dari file CSV!");
        }
    } 
    
    if (in_array($extension, ['xlsx', 'xls'])) {
        return redirect()->back()->with('error', 'Untuk import yang optimal, silakan "Save As" file Excel Anda ke format .CSV (Comma Delimited) terlebih dahulu lalu upload kembali!');
    }

    return redirect()->back()->with('error', 'Format file tidak didukung.');
}

// ===================================================
// FUNGSI BARU 1: UPDATE DATA SOAL DI BANK SOAL
// ===================================================
public function updateBankQuestion(Request $request, BankQuestion $question)
{
    $request->validate([
        'question_text' => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        // Tetap gunakan extensions agar format audio modern aman dari bug mime-type OS
        'audio' => 'nullable|extensions:mp3,wav,ogg,aac,m4a,opus|max:10240',
        'option_a' => 'required|string',
        'option_b' => 'required|string',
        'option_c' => 'required|string',
        'option_d' => 'required|string',
        'correct_answer' => 'required|in:A,B,C,D',
        'explanation' => 'nullable|string',
    ]);

    // Manajemen File Gambar Lama & Baru
    if ($request->hasFile('image')) {
        if ($question->image) {
            Storage::disk('public')->delete($question->image);
        }
        $question->image = $request->file('image')->store('bank/images', 'public');
    }

    // Manajemen File Audio Lama & Baru
    if ($request->hasFile('audio')) {
        if ($question->audio) {
            Storage::disk('public')->delete($question->audio);
        }
        $question->audio = $request->file('audio')->store('bank/audios', 'public');
    }

    // Update data teks dasar & Array Pilihan Ganda (options)
    $question->update([
        'question_text' => $request->question_text,
        'image' => $question->image,
        'audio' => $question->audio,
        'options' => [
            'A' => $request->option_a,
            'B' => $request->option_b,
            'C' => $request->option_c,
            'D' => $request->option_d,
        ],
        'correct_answer' => $request->correct_answer,
        'explanation' => $request->explanation,
    ]);

    return redirect()->back()->with('success', 'Soal di Bank Soal berhasil diperbarui!');
}

// ===================================================
// FUNGSI BARU 2: HAPUS SOAL PERMANEN DARI BANK SOAL
// ===================================================
public function destroyBankQuestion(BankQuestion $question)
{
    // Hapus file fisik gambar di storage server agar tidak jadi sampah penyimpanan
    if ($question->image) {
        Storage::disk('public')->delete($question->image);
    }

    // Hapus file fisik audio di storage server
    if ($question->audio) {
        Storage::disk('public')->delete($question->audio);
    }

    // Hapus baris data dari database
    $question->delete();

    return redirect()->back()->with('success', 'Soal berhasil dihapus dari Bank Soal!');
}
}