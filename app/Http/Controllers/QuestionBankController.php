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
            'type' => 'required|in:multiple_choice,essay,sorting,grouping,labeling',
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'audio' => 'nullable|extensions:mp3,wav,ogg,aac,m4a,opus|max:10240',
            'selected_image_path' => 'nullable|string',
            'selected_audio_path' => 'nullable|string',
            'explanation' => 'nullable|string',
            'correct_answer_mc' => 'required_if:type,multiple_choice|in:A,B,C,D|nullable',
            'correct_answer_essay' => 'required_if:type,essay|string|nullable',
            'correct_answer_sorting' => 'required_if:type,sorting|string|nullable',
            'correct_answer_grouping' => 'required_if:type,grouping|string|nullable',
            'correct_answer_labeling' => 'required_if:type,labeling|string|nullable',
            'option_a_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_b_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_c_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_d_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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
        } elseif ($request->type === 'essay') {
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
        } elseif ($request->type === 'sorting') {
            $cleanSentence = trim(preg_replace('/\s+/', ' ', $request->correct_answer_sorting));
            $correctAnswer = $cleanSentence;
            $words = explode(' ', $cleanSentence);
            $shuffledWords = $words;
            shuffle($shuffledWords);
            $options = $shuffledWords;
        } elseif ($request->type === 'grouping') {
            $rawGroups = array_map('trim', explode('|', $request->correct_answer_grouping));
            $parsedGroupAnswers = [];
            $allWords = [];
            $categories = [];

            foreach ($rawGroups as $groupStr) {
                if (str_contains($groupStr, ':')) {
                    [$catName, $wordsStr] = array_map('trim', explode(':', $groupStr, 2));
                    $words = array_values(array_filter(array_map('trim', explode(',', $wordsStr))));
                    $parsedGroupAnswers[$catName] = $words;
                    $categories[] = $catName;
                    $allWords = array_merge($allWords, $words);
                }
            }

            shuffle($allWords);
            $options = ['categories' => $categories, 'words' => $allWords];
            $correctAnswer = json_encode($parsedGroupAnswers);
        } elseif ($request->type === 'labeling') {
            // Format: "rotor blade: 50, 30 | cockpit: 60, 48 | landing pad: 50, 75"
            $rawLabels = array_map('trim', explode('|', $request->correct_answer_labeling));
            $parsedLabels = [];
            $labelList = [];

            foreach ($rawLabels as $lblStr) {
                if (str_contains($lblStr, ':')) {
                    [$name, $coordStr] = array_map('trim', explode(':', $lblStr, 2));
                    $coords = array_map('floatval', array_map('trim', explode(',', $coordStr)));
                    $parsedLabels[$name] = [
                        'x' => $coords[0] ?? 50,
                        'y' => $coords[1] ?? 50
                    ];
                    $labelList[] = $name;
                }
            }

            shuffle($labelList);
            $options = $labelList;
            $correctAnswer = json_encode($parsedLabels);
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

    public function updateBankQuestion(Request $request, BankQuestion $question)
    {
        $request->validate([
            'type' => 'required|in:multiple_choice,essay,sorting,grouping,labeling',
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'audio' => 'nullable|extensions:mp3,wav,ogg,aac,m4a,opus|max:10240',
            'selected_image_path' => 'nullable|string',
            'selected_audio_path' => 'nullable|string',
            'explanation' => 'nullable|string',
            'correct_answer_mc' => 'required_if:type,multiple_choice|in:A,B,C,D|nullable',
            'correct_answer_essay' => 'required_if:type,essay|string|nullable',
            'correct_answer_sorting' => 'required_if:type,sorting|string|nullable',
            'correct_answer_grouping' => 'required_if:type,grouping|string|nullable',
            'correct_answer_labeling' => 'required_if:type,labeling|string|nullable',
            'option_a_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_b_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_c_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'option_d_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->has('delete_image') && !$request->hasFile('image')) {
            $question->image = null;
        } elseif ($request->hasFile('image')) {
            $question->image = $request->file('image')->store('bank/images', 'public');
        } elseif ($request->filled('selected_image_path')) {
            $question->image = $request->input('selected_image_path');
        }

        if ($request->has('delete_audio') && !$request->hasFile('audio')) {
            $question->audio = null;
        } elseif ($request->hasFile('audio')) {
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
        } elseif ($request->type === 'essay') {
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
        } elseif ($request->type === 'sorting') {
            $cleanSentence = trim(preg_replace('/\s+/', ' ', $request->correct_answer_sorting));
            $correctAnswer = $cleanSentence;
            $words = explode(' ', $cleanSentence);
            $shuffledWords = $words;
            shuffle($shuffledWords);
            $options = $shuffledWords;
        } elseif ($request->type === 'grouping') {
            $rawGroups = array_map('trim', explode('|', $request->correct_answer_grouping));
            $parsedGroupAnswers = [];
            $allWords = [];
            $categories = [];

            foreach ($rawGroups as $groupStr) {
                if (str_contains($groupStr, ':')) {
                    [$catName, $wordsStr] = array_map('trim', explode(':', $groupStr, 2));
                    $words = array_values(array_filter(array_map('trim', explode(',', $wordsStr))));
                    $parsedGroupAnswers[$catName] = $words;
                    $categories[] = $catName;
                    $allWords = array_merge($allWords, $words);
                }
            }

            shuffle($allWords);
            $options = ['categories' => $categories, 'words' => $allWords];
            $correctAnswer = json_encode($parsedGroupAnswers);
        } elseif ($request->type === 'labeling') {
            $rawLabels = array_map('trim', explode('|', $request->correct_answer_labeling));
            $parsedLabels = [];
            $labelList = [];

            foreach ($rawLabels as $lblStr) {
                if (str_contains($lblStr, ':')) {
                    [$name, $coordStr] = array_map('trim', explode(':', $lblStr, 2));
                    $coords = array_map('floatval', array_map('trim', explode(',', $coordStr)));
                    $parsedLabels[$name] = [
                        'x' => $coords[0] ?? 50,
                        'y' => $coords[1] ?? 50
                    ];
                    $labelList[] = $name;
                }
            }

            shuffle($labelList);
            $options = $labelList;
            $correctAnswer = json_encode($parsedLabels);
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
        $question->delete();
        return redirect()->back()->with('success', 'Soal berhasil dihapus dari Bank Soal!');
    }

    public function importZipPackage(Request $request, BankPart $part)
    {
        $request->validate([
            'zip_file' => 'required|file|mimes:zip|max:51200',
        ]);

        $zipFile = $request->file('zip_file');
        $zip = new ZipArchive();

        if ($zip->open($zipFile->getRealPath()) !== true) {
            return redirect()->back()->with('error', 'Gagal membuka file ZIP. Pastikan format file adalah .zip standar.');
        }

        $tempDirName = 'temp_zip_' . time() . '_' . auth()->id();
        $tempPath = storage_path('app/' . $tempDirName);
        File::makeDirectory($tempPath, 0755, true);

        $zip->extractTo($tempPath);
        $zip->close();

        $excelFiles = File::glob($tempPath . '/*.{xlsx,xls,csv}', GLOB_BRACE);
        if (empty($excelFiles)) {
            $excelFiles = File::glob($tempPath . '/*/*.{xlsx,xls,csv}', GLOB_BRACE);
        }

        if (empty($excelFiles)) {
            File::deleteDirectory($tempPath);
            return redirect()->back()->with('error', 'File Excel (.xlsx) tidak ditemukan di dalam paket ZIP!');
        }

        $excelFilePath = $excelFiles[0];

        $mediaDirs = File::glob($tempPath . '/media', GLOB_ONLYDIR);
        if (empty($mediaDirs)) {
            $mediaDirs = File::glob($tempPath . '/*/media', GLOB_ONLYDIR);
        }

        $mediaMap = []; 
        $folderMap = [];

        if (!empty($mediaDirs)) {
            $mediaPath = $mediaDirs[0];
            $allMediaFiles = File::allFiles($mediaPath);

            $bankName = $part->questionBank ? $part->questionBank->name : 'Bank Soal';
            $folderName = $bankName . ' - ' . $part->part_name . ' (' . date('d M Y, H:i') . ')';

            $mainGalleryFolder = MediaFolder::create([
                'user_id'   => auth()->id(),
                'parent_id' => null,
                'name'      => $folderName
            ]);

            foreach ($allMediaFiles as $mFile) {
                $fileName = $mFile->getFilename();
                $ext = strtolower($mFile->getExtension());

                $isAudio = in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'opus', 'aac']);
                $type = $isAudio ? 'audio' : 'image';
                $targetFolder = $isAudio ? 'audios' : 'images';

                $relativePath = $mFile->getRelativePath();
                $targetFolderId = $mainGalleryFolder->id;

                if (!empty($relativePath)) {
                    $subFolderName = trim($relativePath, '/\\');
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

                $storagePath = "media/{$targetFolder}/" . time() . '_' . $fileName;
                Storage::disk('public')->put($storagePath, File::get($mFile->getRealPath()));

                MediaFile::create([
                    'user_id'   => auth()->id(),
                    'folder_id' => $targetFolderId,
                    'file_name' => $fileName,
                    'file_path' => $storagePath,
                    'file_type' => $type,
                    'file_size' => $mFile->getSize(),
                ]);

                $mediaMap[strtolower($fileName)] = $storagePath;
            }
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFilePath);
            $worksheet   = $spreadsheet->getActiveSheet();
            $rows        = $worksheet->toArray();

            $countSuccess = 0;

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

                $options = [];
                $isNonMC = empty($rawOptA) || $rawOptA === '-';

                if ($isNonMC) {
                    // Deteksi Labeling vs Grouping vs Sorting vs Essay
                    if (preg_match('/:\s*\d+(\.\d+)?\s*,\s*\d+(\.\d+)?/', $rawAnswer)) {
                        $type = 'labeling';
                        $rawLabels = array_map('trim', explode('|', $rawAnswer));
                        $parsedLabels = [];
                        $labelList = [];
                        foreach ($rawLabels as $lblStr) {
                            if (str_contains($lblStr, ':')) {
                                [$name, $coordStr] = array_map('trim', explode(':', $lblStr, 2));
                                $coords = array_map('floatval', array_map('trim', explode(',', $coordStr)));
                                $parsedLabels[$name] = ['x' => $coords[0] ?? 50, 'y' => $coords[1] ?? 50];
                                $labelList[] = $name;
                            }
                        }
                        shuffle($labelList);
                        $options = $labelList;
                        $correctAnswer = json_encode($parsedLabels);

                    } elseif (str_contains($rawAnswer, ':') && str_contains($rawAnswer, ',')) {
                        $type = 'grouping';
                        $rawGroups = array_map('trim', explode('|', $rawAnswer));
                        $parsedGroupAnswers = [];
                        $allWords = [];
                        $categories = [];
                        foreach ($rawGroups as $groupStr) {
                            if (str_contains($groupStr, ':')) {
                                [$catName, $wordsStr] = array_map('trim', explode(':', $groupStr, 2));
                                $words = array_values(array_filter(array_map('trim', explode(',', $wordsStr))));
                                $parsedGroupAnswers[$catName] = $words;
                                $categories[] = $catName;
                                $allWords = array_merge($allWords, $words);
                            }
                        }
                        shuffle($allWords);
                        $options = ['categories' => $categories, 'words' => $allWords];
                        $correctAnswer = json_encode($parsedGroupAnswers);

                    } elseif (str_contains(strtolower($rawSoal), 'rearrange') || str_contains(strtolower($rawSoal), 'susun') || str_contains(strtolower($rawSoal), '[sorting]')) {
                        $type = 'sorting';
                        $cleanSentence = trim(preg_replace('/\s+/', ' ', str_replace(['[sorting]', '|'], ' ', $rawAnswer)));
                        $correctAnswer = $cleanSentence;
                        $words = explode(' ', $cleanSentence);
                        $shuffledWords = $words;
                        shuffle($shuffledWords);
                        $options = $shuffledWords;
                    } else {
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
                    }
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

            File::deleteDirectory($tempPath);

            return redirect()->back()->with('success', "Berhasil mengimpor paket ZIP! {$countSuccess} soal & seluruh folder media otomatis dibuatkan di Galeri.");

        } catch (\Exception $e) {
            File::deleteDirectory($tempPath);
            return redirect()->back()->with('error', 'Gagal memproses file Excel ZIP: ' . $e->getMessage());
        }
    }
}