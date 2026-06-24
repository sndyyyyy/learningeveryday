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

    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('bank/images', 'public');
    }

    $audioPath = null;
    if ($request->hasFile('audio')) {
        $audioPath = $request->file('audio')->store('bank/audios', 'public');
    }

    BankQuestion::create([
        'bank_part_id' => $part->id,
        'question_text' => $request->question_text,
        'image' => $imagePath,
        'audio' => $audioPath,
        'options' => [
            'A' => $request->option_a,
            'B' => $request->option_b,
            'C' => $request->option_c,
            'D' => $request->option_d,
        ],
        'correct_answer' => $request->correct_answer,
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
    
    // Ambil ekstensi file
    $extension = $file->getClientOriginalExtension();

    // KONDISI 1: JIKA USER MENGUPLOAD CSV
    if (in_array($extension, ['csv', 'txt'])) {
        $path = $file->getRealPath();
        
        // Buka file CSV
        if (($handle = fopen($path, "r")) !== FALSE) {
            // Lewati baris pertama (Header kolom: soal, opsi_a, dll)
            fgetcsv($handle, 1000, ","); 

            $rowCount = 0;
            // Looping membaca baris demi baris data CSV
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Pastikan kolom minimal terisi (soal sampai jawaban_benar)
                if (count($data) >= 6) {
                    BankQuestion::create([
                        'bank_part_id' => $part->id,
                        'question_text' => $data[0], // Kolom 1: Soal
                        'options' => [
                            'A' => $data[1], // Kolom 2: Opsi A
                            'B' => $data[2], // Kolom 3: Opsi B
                            'C' => $data[3], // Kolom 4: Opsi C
                            'D' => $data[4], // Kolom 5: Opsi D
                        ],
                        'correct_answer' => strtoupper(trim($data[5])), // Kolom 6: Kunci (A/B/C/D)
                        'explanation' => isset($data[6]) ? $data[6] : null, // Kolom 7: Pembahasan (Optional)
                        'image' => null, // Import massal default tanpa gambar dahulu
                        'audio' => null  // Import massal default tanpa audio dahulu
                    ]);
                    $rowCount++;
                }
            }
            fclose($handle);
            
            return redirect()->back()->with('success', "Berhasil mengimpor {$rowCount} soal secara massal dari file CSV!");
        }
    } 
    
    // KONDISI 2: JIKA USER MENGUPLOAD XLSX (Menggunakan Helper parser instan)
    if (in_array($extension, ['xlsx', 'xls'])) {
        // Sebagai fallback jika tidak pakai composer Maatwebsite, kita sarankan user save as .csv 
        // Agar ringan dan dijamin 100% langsung bekerja di server lokal tanpa setup tambahan.
        return redirect()->back()->with('error', 'Untuk performa native yang optimal tanpa library berat, silakan "Save As" file Excel Anda ke format .CSV (Comma Delimited) terlebih dahulu lalu upload kembali, bang!');
    }

    return redirect()->back()->with('error', 'Format file tidak didukung.');
}
}