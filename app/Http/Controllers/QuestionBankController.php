<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use App\Models\BankPart;
use App\Models\BankQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BankQuestionImport;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use Illuminate\Support\Facades\File;
use ZipArchive;


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
        // if ($question->image && Storage::disk('public')->exists($question->image)) {
        //     Storage::disk('public')->delete($question->image);
        // }

        // if ($question->audio && Storage::disk('public')->exists($question->audio)) {
        //     Storage::disk('public')->delete($question->audio);
        // }

        $question->delete();

        return redirect()->back()->with('success', 'Soal berhasil dihapus dari Bank Soal!');
    }


// ===================================================
// FUNGSI IMPORT PAKET ZIP (EXCEL + FOLDER MEDIA)
// ===================================================
public function importZipPackage(Request $request, BankPart $part)
{
    $request->validate([
        'zip_file' => 'required|file|mimes:zip|max:51200', // Max 50MB
    ]);

    $zipFile = $request->file('zip_file');
    $zip = new ZipArchive();

    if ($zip->open($zipFile->getRealPath()) !== true) {
        return redirect()->back()->with('error', 'Gagal membuka file ZIP. Pastikan format file adalah .zip standar.');
    }

    // 1. Buat folder ekstrak sementara di storage
    $tempDirName = 'temp_zip_' . time() . '_' . auth()->id();
    $tempPath = storage_path('app/' . $tempDirName);
    File::makeDirectory($tempPath, 0755, true);

    // Ekstrak seluruh isi ZIP
    $zip->extractTo($tempPath);
    $zip->close();

    // 2. Cari File Excel (.xlsx / .csv)
    $excelFiles = File::glob($tempPath . '/*.{xlsx,xls,csv}', GLOB_BRACE);
    if (empty($excelFiles)) {
        $excelFiles = File::glob($tempPath . '/*/*.{xlsx,xls,csv}', GLOB_BRACE);
    }

    if (empty($excelFiles)) {
        File::deleteDirectory($tempPath);
        return redirect()->back()->with('error', 'File Excel (.xlsx) tidak ditemukan di dalam paket ZIP!');
    }

    $excelFilePath = $excelFiles[0];

    // 3. Cari Folder 'media'
    $mediaDirs = File::glob($tempPath . '/media', GLOB_ONLYDIR);
    if (empty($mediaDirs)) {
        $mediaDirs = File::glob($tempPath . '/*/media', GLOB_ONLYDIR);
    }

    $mediaMap = []; 
    $folderMap = []; // Peta penampung ID folder galeri: [nama_subfolder => id_media_folder]

    if (!empty($mediaDirs)) {
        $mediaPath = $mediaDirs[0];
        $allMediaFiles = File::allFiles($mediaPath);

        // A. Buat Folder Utama untuk Paket ZIP ini di Galeri (agar tidak berantakan di root)
        $bankName = $part->questionBank ? $part->questionBank->name : 'Bank Soal';

        // 🌟 Format nama folder: [Nama Bank] - [Nama Part] (opsional: tambah timestamp)
        $folderName = $bankName . ' - ' . $part->part_name . ' (' . date('d M Y, H:i') . ')';

        // A. Buat Folder Utama untuk Paket ZIP ini di Galeri
        $mainGalleryFolder = MediaFolder::create([
            'user_id'   => auth()->id(),
            'parent_id' => null, // Masuk ke Root Galeri
            'name'      => $folderName
        ]);

        foreach ($allMediaFiles as $mFile) {
            $fileName = $mFile->getFilename();
            $ext = strtolower($mFile->getExtension());

            // Deteksi jenis file (Audio vs Gambar)
            $isAudio = in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'opus', 'aac']);
            $type = $isAudio ? 'audio' : 'image';
            $targetFolder = $isAudio ? 'audios' : 'images';

            // 🌟 DETEKSI APAKAH FILE BERADA DI DALAM SUB-FOLDER (misal: media/khususgambar/gambar.jpeg)
            $relativePath = $mFile->getRelativePath(); // Mengambil struktur folder relatif di dalam 'media/'
            $targetFolderId = $mainGalleryFolder->id;

            if (!empty($relativePath)) {
                $subFolderName = trim($relativePath, '/\\');
                
                // Jika sub-folder galeri ini belum pernah dibuat, buatkan baru!
                if (!isset($folderMap[$subFolderName])) {
                    $newSubFolder = MediaFolder::create([
                        'user_id' => auth()->id(),
                        'parent_id' => $mainGalleryFolder->id,
                        'name' => $subFolderName
                    ]);
                    $folderMap[$subFolderName] = $newSubFolder->id;
                }
                
                $targetFolderId = $folderMap[$subFolderName];
            }

            // Pindahkan berkas dari temp ke Storage Public
            $storagePath = "media/{$targetFolder}/" . time() . '_' . $fileName;
            Storage::disk('public')->put($storagePath, File::get($mFile->getRealPath()));

            // Simpan record ke Galeri Media Internal dengan ID FOLDER YANG SESUAI
            MediaFile::create([
                'user_id'   => auth()->id(),
                'folder_id' => $targetFolderId, // 👈 Masuk ke Sub-folder Galeri yang sesuai struktur ZIP
                'file_name' => $fileName,
                'file_path' => $storagePath,
                'file_type' => $type,
                'file_size' => $mFile->getSize(),
            ]);

            // Catat ke pemetaan nama file
            $mediaMap[strtolower($fileName)] = $storagePath;
        }
    }

    // 4. Proses Pembacaan Data Excel
    try {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFilePath);
        $worksheet   = $spreadsheet->getActiveSheet();
        $rows        = $worksheet->toArray();

        $countSuccess = 0;

        // Loop data baris kuis (mulai baris ke-2 / Index 1)
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            $rawSoal     = trim($row[0] ?? '');
            $rawOptA     = trim($row[1] ?? '');
            $rawOptB     = trim($row[2] ?? '');
            $rawOptC     = trim($row[3] ?? '');
            $rawOptD     = trim($row[4] ?? '');
            $rawAnswer   = trim($row[5] ?? '');
            $explanation = trim($row[6] ?? '');

            if (empty($rawSoal) || empty($rawAnswer)) {
                continue;
            }

            // A. Deteksi Gambar & Audio pada Teks Soal
            $questionImage = null;
            $questionAudio = null;

            foreach ($mediaMap as $origFileName => $storagePath) {
                if (str_contains(strtolower($rawSoal), $origFileName)) {
                    if (str_contains($storagePath, '/audios/')) {
                        $questionAudio = $storagePath;
                    } else {
                        $questionImage = $storagePath;
                    }

                    $rawSoal = str_ireplace(
                        [$origFileName, '[image:'.$origFileName.']', '[audio:'.$origFileName.']'],
                        '',
                        $rawSoal
                    );
                }
            }

            // B. Deteksi Opsi A, B, C, D (Gambar vs Teks / Essay)
            $options = [];
            $isEssay = empty($rawOptA) || $rawOptA === '-';

            if ($isEssay) {
                $type = 'essay';
                $rawBlanks = array_map('trim', preg_split('/[|;]/', $rawAnswer));
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
            } else {
                $type = 'multiple_choice';

                $rawOptions = ['A' => $rawOptA, 'B' => $rawOptB, 'C' => $rawOptC, 'D' => $rawOptD];

                foreach ($rawOptions as $key => $val) {
                    $valLower = strtolower($val);
                    if (isset($mediaMap[$valLower])) {
                        $options[$key] = $mediaMap[$valLower];
                    } else {
                        $options[$key] = $val;
                    }
                }

                $correctAnswer = strtoupper($rawAnswer);
            }

            // C. Simpan ke Database Soal
            BankQuestion::create([
                'bank_part_id'  => $part->id,
                'type'          => $type,
                'question_text' => trim($rawSoal),
                'image'         => $questionImage,
                'audio'         => $questionAudio,
                'options'       => $options,
                'correct_answer'=> $correctAnswer,
                'explanation'   => !empty($explanation) ? $explanation : null,
            ]);

            $countSuccess++;
        }

        // Hapus folder temporary
        File::deleteDirectory($tempPath);

        return redirect()->back()->with('success', "Berhasil mengimpor paket ZIP! {$countSuccess} soal & seluruh folder media otomatis dibuatkan di Galeri.");

    } catch (\Exception $e) {
        File::deleteDirectory($tempPath);
        return redirect()->back()->with('error', 'Gagal memproses file Excel ZIP: ' . $e->getMessage());
    }
}
}