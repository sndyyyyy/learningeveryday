<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Soal - {{ $part->part_name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    @include('layouts.navbar')

    <div class="bg-white border-t border-gray-100 py-3 px-4 md:px-8 flex items-center shadow-xs text-xs md:text-sm text-gray-500 font-medium">
        <a href="{{ route('admin.bank.index') }}" class="hover:text-indigo-600 transition">Bank Soal Utama</a>
        <span class="mx-2">&rarr;</span>
        <a href="{{ route('admin.bank.parts', $bank->id) }}" class="hover:text-indigo-600 transition">{{ $bank->name }}</a>
        <span class="mx-2">&rarr;</span>
        <span class="text-gray-800 font-bold">Kelola Soal: {{ $part->part_name }}</span>
    </div>

    <div class="max-w-6xl mx-auto mt-6 px-4 pb-16 grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">

        <div class="space-y-6">

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded-xl text-xs font-bold shadow-xs">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-amber-100 text-amber-700 p-3 rounded-xl text-xs font-bold shadow-xs">{{ session('error') }}</div>
            @endif

            <!-- METODE 1: INPUT MANUAL SOAL -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center space-x-1">
                    <span>✏️</span> <span>Metode 1: Input Manual Soal</span>
                </h3>

                <form action="{{ route('admin.bank.questions.store', $part->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                    @csrf

                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Tipe Soal</label>
                        <select name="type" id="in-type" onchange="toggleQuestionType()" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 bg-indigo-50 font-bold text-indigo-700">
                            <option value="multiple_choice">🔘 Pilihan Ganda (A, B, C, D)</option>
                            <option value="essay">📝 Essay</option>
                            <option value="sorting">🔀 Sentence Sorting (Susun Kalimat)</option>
                            <option value="grouping">📑 Grouping (Pengelompokan Kategori)</option>
                            <option value="labeling">📍 Image Labeling (Tunjuk Titik Gambar)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Pertanyaan / Instruksi Soal</label>
                        <p id="hint-essay" class="hidden text-[10px] text-amber-600 mb-1 font-medium">💡 Gunakan kata <b>[blank]</b> untuk menandai area yang harus diisi peserta.</p>
                        <p id="hint-sorting" class="hidden text-[10px] text-purple-600 mb-1 font-medium">💡 Tuliskan instruksi susun kalimat.</p>
                        <p id="hint-grouping" class="hidden text-[10px] text-teal-600 mb-1 font-medium">💡 Tuliskan instruksi grouping.</p>
                        <p id="hint-labeling" class="hidden text-[10px] text-rose-600 mb-1 font-medium">💡 Tuliskan instruksi penempatan label gambar.</p>
                        <textarea name="question_text" rows="2" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500"></textarea>
                    </div>

                    <!-- INPUT GAMBAR SOAL UTAMA -->
                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Gambar Soal (Wajib untuk Labeling)</label>
                        <div class="flex gap-1.5 items-center mb-1.5">
                            <input type="text" name="selected_image_path" id="in-image-path" onchange="previewLabelImage(this.value, 'picker-preview-img', 'interactive-picker-box')" readonly placeholder="Atau pilih dari galeri..." class="flex-1 px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs bg-gray-50 text-gray-600 focus:outline-none">
                            <button type="button" onclick="openMediaPickerModal('in-image-path')" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-2.5 py-1.5 rounded-lg text-xs border border-indigo-200 transition cursor-pointer whitespace-nowrap">
                                📁 Galeri
                            </button>
                        </div>
                        <input type="file" name="image" id="in-image-file" accept="image/*" onchange="previewUploadedImage(this, 'picker-preview-img', 'interactive-picker-box')" class="w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    <!-- INPUT AUDIO SOAL UTAMA -->
                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Audio Listening (Optional)</label>
                        <div class="flex gap-1.5 items-center mb-1.5">
                            <input type="text" name="selected_audio_path" id="in-audio-path" readonly placeholder="Atau pilih dari galeri..." class="flex-1 px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs bg-gray-50 text-gray-600 focus:outline-none">
                            <button type="button" onclick="openMediaPickerModal('in-audio-path')" class="bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold px-2.5 py-1.5 rounded-lg text-xs border border-amber-200 transition cursor-pointer whitespace-nowrap">
                                📁 Galeri
                            </button>
                        </div>
                        <input type="file" name="audio" accept="audio/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    </div>

                    <!-- OPSI PILIHAN GANDA -->
                    <div id="container-multiple-choice" class="space-y-3.5 transition-all">
                        <div class="grid grid-cols-1 gap-2.5">
                            @foreach(['a', 'b', 'c', 'd'] as $opt)
                                <div class="border border-gray-100 p-2.5 rounded-xl bg-gray-50/60">
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-gray-700 text-[11px] font-bold uppercase">Pilihan {{ strtoupper($opt) }}</label>
                                        <button type="button" onclick="openMediaPickerModal('in-opt-{{$opt}}-path')" class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 hover:bg-indigo-100 transition cursor-pointer">
                                            📁 Galeri
                                        </button>
                                    </div>
                                    <input type="text" name="option_{{$opt}}" id="in-opt-{{$opt}}-path" placeholder="Ketik teks opsi atau pilih gambar..." class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs bg-white focus:outline-none mb-1 font-medium">
                                    <input type="hidden" name="selected_option_{{$opt}}_path" id="in-opt-{{$opt}}-path-hidden">

                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[9px] text-gray-400 font-semibold">Atau upload gambar:</span>
                                        <input type="file" name="option_{{$opt}}_file" accept="image/*" class="text-[9px] text-gray-500 file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-[9px] file:font-bold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div>
                            <label class="block text-gray-600 text-[11px] font-semibold mb-1">Jawaban Benar (Pilihan Ganda)</label>
                            <select name="correct_answer_mc" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none font-bold text-indigo-700 bg-white">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>

                    <!-- CONTAINER INPUT ESSAY -->
                    <div id="container-essay" class="hidden mt-2 bg-amber-50 p-3 rounded-lg border border-amber-200 space-y-2">
                        <label class="block text-amber-800 text-[11px] font-bold mb-1">Kunci Jawaban Essay</label>
                        <input type="text" name="correct_answer_essay" class="w-full px-3 py-2 border border-amber-300 rounded-lg text-xs focus:outline-none focus:border-amber-500 bg-white font-medium" placeholder="Contoh: Bandung / Bandoeng | Jawa Barat / Jabar">
                    </div>

                    <!-- CONTAINER INPUT SORTING -->
                    <div id="container-sorting" class="hidden mt-2 bg-purple-50 p-3 rounded-lg border border-purple-200 space-y-2">
                        <label class="block text-purple-800 text-[11px] font-bold mb-1">Target Susunan Kalimat Benar</label>
                        <input type="text" name="correct_answer_sorting" class="w-full px-3 py-2 border border-purple-300 rounded-lg text-xs focus:outline-none focus:border-purple-500 bg-white font-semibold text-gray-800" placeholder="Contoh: What precautions should be taken when loading cargo?">
                    </div>

                    <!-- CONTAINER INPUT GROUPING -->
                    <div id="container-grouping" class="hidden mt-2 bg-teal-50 p-3 rounded-lg border border-teal-200 space-y-2">
                        <label class="block text-teal-800 text-[11px] font-bold mb-1">Kunci Pengelompokan Kategori</label>
                        <textarea name="correct_answer_grouping" rows="2" class="w-full px-3 py-2 border border-teal-300 rounded-lg text-xs focus:outline-none focus:border-teal-500 bg-white font-medium text-gray-800" placeholder="action: disembark, embark | description: dangerous, safe | object: ship, lifejacket"></textarea>
                    </div>

                    <!-- 🌟 CONTAINER INPUT LABELING: VISUAL CLICK-TO-POINT 🌟 -->
                    <div id="container-labeling" class="hidden mt-2 bg-rose-50 p-3.5 rounded-xl border border-rose-200 space-y-3">
                        <div>
                            <span class="block text-rose-900 text-xs font-bold mb-0.5">🎯 Interactive Pinpoint Picker</span>
                            <p class="text-[10px] text-rose-700 leading-normal">
                                1. Ketik nama label di bawah.<br>
                                2. Klik pada area gambar untuk menaruh titik target.<br>
                                3. Koordinat akan terhitung otomatis!
                            </p>
                        </div>

                        <!-- Baris Input Nama Label Aktif -->
                        <div class="flex gap-1.5 items-center">
                            <input type="text" id="active-label-input" placeholder="Ketik label (contoh: Eye, Cockpit)..." class="flex-1 px-2.5 py-1.5 border border-rose-300 rounded-lg text-xs bg-white focus:outline-none focus:border-rose-500 font-bold text-gray-800">
                            <span class="text-[10px] bg-rose-200 text-rose-800 font-bold px-2 py-1.5 rounded-lg whitespace-nowrap">Siap Klik 📍</span>
                        </div>

                        <!-- Canvas Gambar Interaktif -->
                        <div id="interactive-picker-box" class="hidden relative border-2 border-dashed border-rose-300 rounded-xl overflow-hidden cursor-crosshair bg-black">
                            <img id="picker-preview-img" src="" alt="Preview" class="w-full h-auto object-contain block pointer-events-none" />
                            <div id="pins-overlay-container" class="absolute inset-0 w-full h-full pointer-events-auto" onclick="handleCanvasPointClick(event, 'in-correct-labeling', 'pins-overlay-container', 'active-label-input', 'tags-badge-list')">
                            </div>
                        </div>

                        <!-- Daftar Pin Aktif -->
                        <div>
                            <label class="block text-gray-600 text-[10px] font-bold mb-1">Daftar Titik Label Terpasang:</label>
                            <div id="tags-badge-list" class="flex flex-wrap gap-1.5 min-h-[25px]">
                                <span class="text-[10px] text-gray-400 italic">Belum ada titik yang ditandai.</span>
                            </div>
                        </div>

                        <!-- String Data Koordinat (Otomatis Terisi) -->
                        <div>
                            <label class="block text-gray-500 text-[9px] font-semibold mb-0.5">Data String (Tersimpan ke Database):</label>
                            <input type="text" name="correct_answer_labeling" id="in-correct-labeling" readonly class="w-full px-2 py-1 border border-gray-200 rounded text-[10px] bg-white text-gray-500 font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Pembahasan (Optional)</label>
                        <textarea name="explanation" rows="2" placeholder="Tuliskan alasan/materi pembahasan..." class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg text-xs transition cursor-pointer">
                        Simpan ke Bank Soal
                    </button>
                </form>
            </div>

            <!-- METODE 2: IMPORT DARI EXCEL STANDARD (.XLSX) -->
            <div id="container-import-excel" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-800 mb-1 flex items-center space-x-1">
                    <span>📊</span> <span>Metode 2: Import dari Excel (.XLSX)</span>
                </h3>

                <form action="{{ route('admin.bank.questions.import', $part->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-3 text-center bg-white hover:bg-gray-50 transition cursor-pointer">
                        <input type="file" name="excel_file" required accept=".xlsx, .xls, .csv" class="w-full text-xs text-gray-500 file:text-[11px] file:font-bold file:bg-indigo-50 file:text-indigo-700 file:border-0 file:rounded-md file:px-3 file:py-1.5 cursor-pointer">
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-lg text-xs transition shadow-sm cursor-pointer">
                        🚀 Import Soal dari Excel
                    </button>
                </form>
            </div>

            <!-- METODE 3: IMPORT PAKET SOAL (.ZIP) -->
            <div id="container-import-zip" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-800 mb-1 flex items-center space-x-1">
                    <span>📦</span> <span>Metode 3: Import Paket Soal (.ZIP)</span>
                </h3>

                <form action="{{ route('admin.bank.questions.import_zip', $part->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-3 text-center bg-white hover:bg-gray-50 transition cursor-pointer">
                        <input type="file" name="zip_file" required accept=".zip" class="w-full text-xs text-gray-500 file:text-[11px] file:font-bold file:bg-indigo-50 file:text-indigo-700 file:border-0 file:rounded-md file:px-3 file:py-1.5 cursor-pointer">
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-lg text-xs transition shadow-sm cursor-pointer flex items-center justify-center gap-1.5">
                        <span>🚀 Ekstrak & Import Paket ZIP</span>
                    </button>
                </form>
            </div>

        </div>

        <div class="md:col-span-2 bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
            <h2 class="text-base font-bold text-gray-800 mb-1">Koleksi Konten Soal</h2>
            <p class="text-xs text-gray-400 mb-5">Daftar item soal aktif di kategori <span class="text-indigo-600 font-bold">{{ $part->part_name }}</span>.</p>

            <div class="space-y-6">
                @forelse($questions as $index => $q)
                    <div class="border-b border-gray-100 pb-5 last:border-b-0 animate-fade-in">

                        <div class="flex justify-between items-start gap-4 mb-2">
                            <div class="flex-1">
                                <span class="text-[9px] px-2 py-0.5 rounded border mb-1.5 inline-block font-bold uppercase tracking-wider 
                                    {{ $q->type === 'essay' ? 'bg-amber-50 text-amber-600 border-amber-200' : ($q->type === 'sorting' ? 'bg-purple-50 text-purple-600 border-purple-200' : ($q->type === 'grouping' ? 'bg-teal-50 text-teal-600 border-teal-200' : ($q->type === 'labeling' ? 'bg-rose-50 text-rose-600 border-rose-200' : 'bg-blue-50 text-blue-600 border-blue-200'))) }}">
                                    {{ $q->type === 'essay' ? '📝 Essay' : ($q->type === 'sorting' ? '🔀 Sorting' : ($q->type === 'grouping' ? '📑 Grouping' : ($q->type === 'labeling' ? '📍 Labeling' : '🔘 Pilihan Ganda'))) }}
                                </span>
                                <p class="font-semibold text-gray-800 text-sm leading-relaxed">
                                    <span class="text-indigo-600 font-bold">{{ $index + 1 }}.</span> {{ $q->question_text }}
                                </p>
                            </div>

                            <div class="flex flex-row gap-1.5 shrink-0 items-center">
                                @php
                                    $essayAnswerString = '';
                                    $groupingAnswerString = '';
                                    $labelingAnswerString = '';

                                    if ($q->type === 'essay' && $q->correct_answer) {
                                        $decoded = json_decode($q->correct_answer, true);
                                        if (is_array($decoded)) {
                                            $blankParts = [];
                                            foreach ($decoded as $bIdx => $aliases) {
                                                $blankParts[] = "Blank " . ($bIdx + 1) . ": " . implode(' / ', array_map('ucwords', (array)$aliases));
                                            }
                                            $essayAnswerString = implode(' | ', $blankParts);
                                        } else {
                                            $essayAnswerString = $q->correct_answer;
                                        }
                                    } elseif ($q->type === 'grouping' && $q->correct_answer) {
                                        $decoded = json_decode($q->correct_answer, true);
                                        if (is_array($decoded)) {
                                            $gArr = [];
                                            foreach ($decoded as $cat => $wList) {
                                                $gArr[] = "{$cat}: " . implode(', ', (array)$wList);
                                            }
                                            $groupingAnswerString = implode(' | ', $gArr);
                                        } else {
                                            $groupingAnswerString = $q->correct_answer;
                                        }
                                    } elseif ($q->type === 'labeling' && $q->correct_answer) {
                                        $decoded = json_decode($q->correct_answer, true);
                                        if (is_array($decoded)) {
                                            $lArr = [];
                                            foreach ($decoded as $lbl => $coords) {
                                                $lArr[] = "{$lbl}: " . ($coords['x'] ?? 50) . ", " . ($coords['y'] ?? 50);
                                            }
                                            $labelingAnswerString = implode(' | ', $lArr);
                                        } else {
                                            $labelingAnswerString = $q->correct_answer;
                                        }
                                    }
                                @endphp

                                <button type="button"
                                        data-id="{{ $q->id }}"
                                        data-type="{{ $q->type ?? 'multiple_choice' }}"
                                        data-text="{{ $q->question_text }}"
                                        data-opta="{{ $q->options['A'] ?? '' }}"
                                        data-optb="{{ $q->options['B'] ?? '' }}"
                                        data-optc="{{ $q->options['C'] ?? '' }}"
                                        data-optd="{{ $q->options['D'] ?? '' }}"
                                        data-correct-mc="{{ $q->type === 'multiple_choice' ? $q->correct_answer : 'A' }}"
                                        data-correct-essay="{{ $essayAnswerString }}"
                                        data-correct-sorting="{{ $q->correct_answer }}"
                                        data-correct-grouping="{{ $groupingAnswerString }}"
                                        data-correct-labeling="{{ $labelingAnswerString }}"
                                        data-image="{{ $q->image }}"
                                        data-explanation="{{ $q->explanation }}"
                                        data-hasimage="{{ $q->image ? '1' : '0' }}"
                                        data-hasaudio="{{ $q->audio ? '1' : '0' }}"
                                        onclick="openEditModal(this)" 
                                        class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2 py-1 rounded-md text-xs font-bold transition cursor-pointer">
                                    Edit
                                </button>

                                <form action="{{ route('admin.bank.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus soal ini dari database bank?')" class="inline-block">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 px-2 py-1 rounded-md text-xs font-bold transition cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if($q->image)
                            <div class="my-3 pl-4 relative inline-block">
                                <img src="/storage/{{ $q->image }}" alt="Media Soal" class="max-h-48 object-contain rounded-lg border border-gray-100 shadow-2xs">
                                @if($q->type === 'labeling')
                                    @php
                                        $lblPoints = json_decode($q->correct_answer, true) ?? [];
                                    @endphp
                                    @foreach($lblPoints as $lbl => $coords)
                                        <div class="absolute w-3.5 h-3.5 bg-rose-500 border-2 border-white rounded-full transform -translate-x-1/2 -translate-y-1/2 shadow-xs flex items-center justify-center text-[7px] font-bold text-white" 
                                             style="left: {{ $coords['x'] ?? 50 }}%; top: {{ $coords['y'] ?? 50 }}%;" 
                                             title="{{ $lbl }} ({{ $coords['x'] }}%, {{ $coords['y'] }}%)">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif

                        @if($q->audio)
                            <div class="my-3 pl-4 max-w-sm">
                                <audio src="{{ route('audio.stream', ['path' => $q->audio]) }}" controls class="w-full scale-90 origin-left outline-none"></audio>
                            </div>
                        @endif

                        @if($q->type === 'essay')
                            <div class="mt-2 pl-4 text-xs">
                                <p class="bg-amber-50/70 text-amber-800 p-2.5 rounded-lg border border-amber-200/60 font-semibold inline-block">
                                    <span class="text-amber-500 mr-1">🔑</span> Kunci Jawaban: <span class="font-bold underline decoration-amber-300">{{ $essayAnswerString }}</span>
                                </p>
                            </div>
                        @elseif($q->type === 'sorting')
                            <div class="mt-2 pl-4 text-xs">
                                <div class="bg-purple-50/70 text-purple-800 p-2.5 rounded-lg border border-purple-200/60 font-semibold">
                                    <span class="text-purple-500 mr-1">🔑</span> Susunan Kalimat Benar: <span class="font-bold underline decoration-purple-300">{{ $q->correct_answer }}</span>
                                </div>
                            </div>
                        @elseif($q->type === 'grouping')
                            <div class="mt-2 pl-4 text-xs">
                                <div class="bg-teal-50/70 text-teal-800 p-2.5 rounded-lg border border-teal-200/60 font-semibold">
                                    <span class="text-teal-600 mr-1">🔑</span> Kunci Pengelompokan Kategori:
                                    <div class="mt-1.5 grid grid-cols-1 sm:grid-cols-3 gap-2">
                                        @php
                                            $parsedG = json_decode($q->correct_answer, true) ?? [];
                                        @endphp
                                        @foreach($parsedG as $catName => $wordsList)
                                            <div class="bg-white p-2 rounded-lg border border-teal-200 shadow-2xs">
                                                <span class="font-bold text-teal-700 block uppercase text-[10px] mb-1 pb-1 border-b border-teal-100">{{ $catName }}</span>
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach((array)$wordsList as $wl)
                                                        <span class="bg-teal-50 text-teal-700 px-1.5 py-0.5 rounded text-[10px] font-medium">{{ $wl }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @elseif($q->type === 'labeling')
                            <div class="mt-2 pl-4 text-xs">
                                <div class="bg-rose-50/70 text-rose-800 p-2.5 rounded-lg border border-rose-200/60 font-semibold">
                                    <span class="text-rose-600 mr-1">📍</span> Kunci Koordinat Label Titik:
                                    <div class="mt-1 flex flex-wrap gap-1.5">
                                        @php
                                            $parsedL = json_decode($q->correct_answer, true) ?? [];
                                        @endphp
                                        @foreach($parsedL as $lbl => $c)
                                            <span class="bg-white border border-rose-200 text-rose-800 px-2 py-0.5 rounded-full text-[10px] font-bold">
                                                {{ $lbl }} &rarr; X: {{ $c['x'] ?? 50 }}%, Y: {{ $c['y'] ?? 50 }}%
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2.5 pl-4 text-xs text-gray-600">
                                @foreach(['A', 'B', 'C', 'D'] as $optKey)
                                    @php
                                        $optVal = $q->options[$optKey] ?? '';
                                        $isOptImage = preg_match('/\.(jpeg|jpg|gif|png|webp)$/i', $optVal) || str_starts_with($optVal, 'options/') || str_starts_with($optVal, 'media/');
                                        $isCorrect = ($q->correct_answer == $optKey);
                                    @endphp
                                    <div class="p-2 rounded-md {{ $isCorrect ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200' : 'bg-gray-50' }}">
                                        <strong>{{ $optKey }}.</strong> 
                                        @if($isOptImage)
                                            <img src="/storage/{{ $optVal }}" class="max-h-16 object-contain inline-block ml-1 rounded border border-gray-200 bg-white p-0.5" />
                                        @else
                                            {{ $optVal }}
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($q->explanation)
                            <p class="text-xs text-gray-500 mt-2.5 bg-indigo-50/50 p-2 rounded-lg border-l-2 border-indigo-500 pl-3">
                                <span class="font-bold text-indigo-700">Materi Pembahasan:</span> {{ $q->explanation }}
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-400 text-center py-10 text-xs">Kategori part ini belum memiliki koleksi soal.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- MODAL POPUP EDIT SOAL BANK -->
    <div id="edit-modal" class="fixed inset-0 bg-black/60 hidden justify-center items-center z-50 backdrop-blur-sm transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-lg w-[90%] shadow-2xl overflow-y-auto max-h-[90vh] animate-fade-in">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800">Edit Soal Bank</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-xl cursor-pointer">&times;</button>
            </div>

            <form id="edit-form" method="POST" enctype="multipart/form-data" class="space-y-4 text-left">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-gray-600 text-[11px] font-semibold mb-1">Tipe Soal</label>
                    <select name="type" id="edit_type" onchange="toggleEditQuestionType()" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 bg-indigo-50 font-bold text-indigo-700">
                        <option value="multiple_choice">🔘 Pilihan Ganda (A, B, C, D)</option>
                        <option value="essay">📝 Essay</option>
                        <option value="sorting">🔀 Sentence Sorting (Susun Kalimat)</option>
                        <option value="grouping">📑 Grouping (Pengelompokan Kategori)</option>
                        <option value="labeling">📍 Image Labeling (Tunjuk Titik Gambar)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-600 text-[11px] font-semibold mb-1">Pertanyaan / Instruksi Soal</label>
                    <textarea name="question_text" id="edit_question_text" rows="2" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none"></textarea>
                </div>

                <div class="border border-gray-100 p-3 rounded-xl bg-gray-50/50">
                    <label class="block text-gray-600 text-[11px] font-semibold mb-1">Ganti Gambar (Optional)</label>
                    <div class="flex gap-1.5 items-center mb-1.5">
                        <input type="text" name="selected_image_path" id="edit-image-path" onchange="previewLabelImage(this.value, 'edit-picker-preview-img', 'edit-interactive-picker-box')" readonly placeholder="Atau pilih dari galeri..." class="flex-1 px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs bg-white text-gray-600 focus:outline-none">
                        <button type="button" onclick="openMediaPickerModal('edit-image-path')" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-2.5 py-1.5 rounded-lg text-xs border border-indigo-200 transition cursor-pointer whitespace-nowrap">
                            📁 Galeri
                        </button>
                    </div>
                    <input type="file" name="image" accept="image/*" onchange="previewUploadedImage(this, 'edit-picker-preview-img', 'edit-interactive-picker-box')" class="w-full text-xs text-gray-500 mb-2">
                    <div id="edit_image_delete_container" class="hidden flex-row items-center space-x-2 mt-1">
                        <input type="checkbox" name="delete_image" id="delete_image_checkbox" value="1" class="w-3.5 h-3.5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="delete_image_checkbox" class="text-[11px] text-red-500 font-medium cursor-pointer">🗑️ Hapus file gambar lama</label>
                    </div>
                </div>

                <div id="edit_container_multiple_choice" class="space-y-4">
                    <div class="grid grid-cols-1 gap-2.5">
                        @foreach(['a', 'b', 'c', 'd'] as $opt)
                            <div class="border border-gray-100 p-2.5 rounded-xl bg-gray-50/60">
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-gray-700 text-[11px] font-bold uppercase">Pilihan {{ strtoupper($opt) }}</label>
                                    <button type="button" onclick="openMediaPickerModal('edit_option_{{$opt}}')" class="text-[10px] text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 hover:bg-indigo-100 transition cursor-pointer">
                                        📁 Galeri
                                    </button>
                                </div>
                                <input type="text" name="option_{{$opt}}" id="edit_option_{{$opt}}" placeholder="Ketik teks opsi atau pilih gambar..." class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs bg-white focus:outline-none mb-1 font-medium">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[9px] text-gray-400 font-semibold">Ganti gambar:</span>
                                    <input type="file" name="option_{{$opt}}_file" accept="image/*" class="text-[9px] text-gray-500 file:mr-2 file:py-0.5 file:px-2 file:rounded file:border-0 file:text-[9px] file:font-bold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Jawaban Benar</label>
                        <select name="correct_answer_mc" id="edit_correct_answer_mc" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none font-bold text-indigo-700 bg-white">
                            <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                        </select>
                    </div>
                </div>

                <div id="edit_container_essay" class="hidden bg-amber-50 p-3 rounded-lg border border-amber-200 space-y-2">
                    <label class="block text-amber-800 text-[11px] font-bold mb-1">Kunci Jawaban Essay</label>
                    <input type="text" name="correct_answer_essay" id="edit_correct_answer_essay" class="w-full px-3 py-2 border border-amber-300 rounded-lg text-xs focus:outline-none focus:border-amber-500 bg-white font-medium">
                </div>

                <div id="edit_container_sorting" class="hidden bg-purple-50 p-3 rounded-lg border border-purple-200 space-y-2">
                    <label class="block text-purple-800 text-[11px] font-bold mb-1">Target Susunan Kalimat Benar</label>
                    <input type="text" name="correct_answer_sorting" id="edit_correct_answer_sorting" class="w-full px-3 py-2 border border-purple-300 rounded-lg text-xs focus:outline-none focus:border-purple-500 bg-white font-semibold text-gray-800">
                </div>

                <div id="edit_container_grouping" class="hidden bg-teal-50 p-3 rounded-lg border border-teal-200 space-y-2">
                    <label class="block text-teal-800 text-[11px] font-bold mb-1">Kunci Pengelompokan Kategori</label>
                    <textarea name="correct_answer_grouping" id="edit_correct_answer_grouping" rows="2" class="w-full px-3 py-2 border border-teal-300 rounded-lg text-xs focus:outline-none focus:border-teal-500 bg-white font-medium text-gray-800"></textarea>
                </div>

                <!-- 🌟 EDIT CONTAINER LABELING 🌟 -->
                <div id="edit_container_labeling" class="hidden bg-rose-50 p-3.5 rounded-xl border border-rose-200 space-y-3">
                    <div>
                        <span class="block text-rose-900 text-xs font-bold mb-0.5">🎯 Interactive Pinpoint Picker (Edit)</span>
                        <p class="text-[10px] text-rose-700 leading-normal">
                            Ketik label di bawah, lalu klik di gambar untuk menambah titik target baru.
                        </p>
                    </div>

                    <div class="flex gap-1.5 items-center">
                        <input type="text" id="edit-active-label-input" placeholder="Ketik label..." class="flex-1 px-2.5 py-1.5 border border-rose-300 rounded-lg text-xs bg-white focus:outline-none font-bold text-gray-800">
                        <span class="text-[10px] bg-rose-200 text-rose-800 font-bold px-2 py-1.5 rounded-lg whitespace-nowrap">Siap Klik 📍</span>
                    </div>

                    <div id="edit-interactive-picker-box" class="relative border-2 border-dashed border-rose-300 rounded-xl overflow-hidden cursor-crosshair bg-black">
                        <img id="edit-picker-preview-img" src="" alt="Preview" class="w-full h-auto object-contain block pointer-events-none" />
                        <div id="edit-pins-overlay-container" class="absolute inset-0 w-full h-full pointer-events-auto" onclick="handleCanvasPointClick(event, 'edit_correct_answer_labeling', 'edit-pins-overlay-container', 'edit-active-label-input', 'edit-tags-badge-list')">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-[10px] font-bold mb-1">Daftar Titik Label Terpasang:</label>
                        <div id="edit-tags-badge-list" class="flex flex-wrap gap-1.5 min-h-[25px]"></div>
                    </div>

                    <input type="text" name="correct_answer_labeling" id="edit_correct_answer_labeling" readonly class="w-full px-2 py-1 border border-gray-200 rounded text-[10px] bg-white text-gray-500 font-mono">
                </div>

                <div>
                    <label class="block text-gray-600 text-[11px] font-semibold mb-1">Pembahasan</label>
                    <textarea name="explanation" id="edit_explanation" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none"></textarea>
                </div>

                <div class="flex space-x-2 pt-2">
                    <button type="button" onclick="closeEditModal()" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 rounded-lg text-xs transition cursor-pointer">Batal</button>
                    <button type="submit" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg text-xs transition cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- KOMPONEN MODAL POP-UP GALERI MEDIA INTERNAL -->
    @include('components.media-picker-modal')

    <script>
        // Objek state penampung pin
        let labelingPins = {};
        let editLabelingPins = {};

        function toggleQuestionType() {
            const type = document.getElementById("in-type").value;
            const containerMC = document.getElementById("container-multiple-choice");
            const containerEssay = document.getElementById("container-essay");
            const containerSorting = document.getElementById("container-sorting");
            const containerGrouping = document.getElementById("container-grouping");
            const containerLabeling = document.getElementById("container-labeling");

            const hintEssay = document.getElementById("hint-essay");
            const hintSorting = document.getElementById("hint-sorting");
            const hintGrouping = document.getElementById("hint-grouping");
            const hintLabeling = document.getElementById("hint-labeling");

            containerMC.classList.add('hidden');
            containerEssay.classList.add('hidden');
            containerSorting.classList.add('hidden');
            containerGrouping.classList.add('hidden');
            containerLabeling.classList.add('hidden');
            
            hintEssay.classList.add('hidden');
            hintSorting.classList.add('hidden');
            hintGrouping.classList.add('hidden');
            hintLabeling.classList.add('hidden');

            if (type === 'essay') {
                containerEssay.classList.remove('hidden');
                hintEssay.classList.remove('hidden');
            } else if (type === 'sorting') {
                containerSorting.classList.remove('hidden');
                hintSorting.classList.remove('hidden');
            } else if (type === 'grouping') {
                containerGrouping.classList.remove('hidden');
                hintGrouping.classList.remove('hidden');
            } else if (type === 'labeling') {
                containerLabeling.classList.remove('hidden');
                hintLabeling.classList.remove('hidden');
            } else {
                containerMC.classList.remove('hidden');
            }
        }

        function toggleEditQuestionType() {
            const type = document.getElementById("edit_type").value;
            const containerMC = document.getElementById("edit_container_multiple_choice");
            const containerEssay = document.getElementById("edit_container_essay");
            const containerSorting = document.getElementById("edit_container_sorting");
            const containerGrouping = document.getElementById("edit_container_grouping");
            const containerLabeling = document.getElementById("edit_container_labeling");

            containerMC.classList.add('hidden');
            containerEssay.classList.add('hidden');
            containerSorting.classList.add('hidden');
            containerGrouping.classList.add('hidden');
            containerLabeling.classList.add('hidden');

            if (type === 'essay') {
                containerEssay.classList.remove('hidden');
            } else if (type === 'sorting') {
                containerSorting.classList.remove('hidden');
            } else if (type === 'grouping') {
                containerGrouping.classList.remove('hidden');
            } else if (type === 'labeling') {
                containerLabeling.classList.remove('hidden');
            } else {
                containerMC.classList.remove('hidden');
            }
        }

        // Preview gambar saat dipilih dari galeri atau upload lokal
        function previewLabelImage(path, imgElementId, boxElementId) {
            if (!path) return;
            const imgEl = document.getElementById(imgElementId);
            const boxEl = document.getElementById(boxElementId);
            imgEl.src = path.startsWith('http') || path.startsWith('/') ? path : `/storage/${path}`;
            boxEl.classList.remove('hidden');
        }

        function previewUploadedImage(input, imgElementId, boxElementId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgEl = document.getElementById(imgElementId);
                    const boxEl = document.getElementById(boxElementId);
                    imgEl.src = e.target.result;
                    boxEl.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // 🎯 HANDLER KLIK GAMBAR UNTUK TARUH PIN OTOMATIS
        function handleCanvasPointClick(e, inputTargetId, overlayId, labelInputId, badgeListId) {
            const labelInput = document.getElementById(labelInputId);
            const labelName = labelInput.value.trim();

            if (!labelName) {
                alert("Ketikkan Nama Label terlebih dahulu di kotak atas sebelum menandai titik gambar!");
                labelInput.focus();
                return;
            }

            const overlay = document.getElementById(overlayId);
            const rect = overlay.getBoundingClientRect();

            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const pctX = Math.round((x / rect.width) * 100);
            const pctY = Math.round((y / rect.height) * 100);

            const isEdit = inputTargetId.includes('edit');
            let pinStorage = isEdit ? editLabelingPins : labelingPins;

            pinStorage[labelName] = { x: pctX, y: pctY };
            labelInput.value = ""; // Reset input label agar siap untuk label berikutnya

            renderPins(inputTargetId, overlayId, badgeListId, isEdit);
        }

        // Render Pin Visual & Update Nilai String Input
        function renderPins(inputTargetId, overlayId, badgeListId, isEdit) {
            const overlay = document.getElementById(overlayId);
            const badgeContainer = document.getElementById(badgeListId);
            const hiddenInput = document.getElementById(inputTargetId);

            let pinStorage = isEdit ? editLabelingPins : labelingPins;
            overlay.innerHTML = "";
            badgeContainer.innerHTML = "";

            let stringArr = [];

            const keys = Object.keys(pinStorage);
            if (keys.length === 0) {
                badgeContainer.innerHTML = `<span class="text-[10px] text-gray-400 italic">Belum ada titik yang ditandai.</span>`;
                hiddenInput.value = "";
                return;
            }

            keys.forEach(lbl => {
                const coord = pinStorage[lbl];
                stringArr.push(`${lbl}: ${coord.x}, ${coord.y}`);

                // 1. Render Titik Merah di Atas Gambar
                const pinEl = document.createElement("div");
                pinEl.className = "absolute flex items-center gap-1 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none";
                pinEl.style.left = `${coord.x}%`;
                pinEl.style.top = `${coord.y}%`;
                pinEl.innerHTML = `
                    <div class="w-3.5 h-3.5 bg-rose-600 border-2 border-white rounded-full shadow-md animate-pulse"></div>
                    <span class="bg-rose-900/90 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow whitespace-nowrap">${lbl}</span>
                `;
                overlay.appendChild(pinEl);

                // 2. Render Badge di Bawah Gambar (Bisa Dihapus)
                const badge = document.createElement("div");
                badge.className = "bg-rose-100 text-rose-800 border border-rose-200 text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1 shadow-2xs";
                badge.innerHTML = `
                    <span>📍 ${lbl} (${coord.x}%, ${coord.y}%)</span>
                    <button type="button" onclick="deletePin('${lbl}', '${inputTargetId}', '${overlayId}', '${badgeListId}', ${isEdit})" class="text-rose-500 hover:text-rose-700 font-black ml-1 cursor-pointer">&times;</button>
                `;
                badgeContainer.appendChild(badge);
            });

            hiddenInput.value = stringArr.join(" | ");
        }

        // Hapus Pin Jika Salah Klik
        function deletePin(labelName, inputTargetId, overlayId, badgeListId, isEdit) {
            let pinStorage = isEdit ? editLabelingPins : labelingPins;
            delete pinStorage[labelName];
            renderPins(inputTargetId, overlayId, badgeListId, isEdit);
        }

        function openEditModal(btnElement) {
            const id = btnElement.getAttribute('data-id');
            const type = btnElement.getAttribute('data-type');
            const text = btnElement.getAttribute('data-text');
            const optA = btnElement.getAttribute('data-opta');
            const optB = btnElement.getAttribute('data-optb');
            const optC = btnElement.getAttribute('data-optc');
            const optD = btnElement.getAttribute('data-optd');
            const correctMC = btnElement.getAttribute('data-correct-mc');
            const correctEssay = btnElement.getAttribute('data-correct-essay');
            const correctSorting = btnElement.getAttribute('data-correct-sorting');
            const correctGrouping = btnElement.getAttribute('data-correct-grouping');
            const correctLabeling = btnElement.getAttribute('data-correct-labeling');
            const imagePath = btnElement.getAttribute('data-image');
            const explanation = btnElement.getAttribute('data-explanation');

            const modal = document.getElementById('edit-modal');
            const form = document.getElementById('edit-form');
            form.action = `/admin/bank/questions/${id}`;

            document.getElementById('edit_type').value = type;
            document.getElementById('edit_question_text').value = text;
            document.getElementById('edit_option_a').value = optA;
            document.getElementById('edit_option_b').value = optB;
            document.getElementById('edit_option_c').value = optC;
            document.getElementById('edit_option_d').value = optD;
            document.getElementById('edit_correct_answer_mc').value = correctMC;
            document.getElementById('edit_correct_answer_essay').value = correctEssay;
            document.getElementById('edit_correct_answer_sorting').value = correctSorting || '';
            document.getElementById('edit_correct_answer_grouping').value = correctGrouping || '';
            document.getElementById('edit_correct_answer_labeling').value = correctLabeling || '';
            document.getElementById('edit_explanation').value = explanation || '';

            toggleEditQuestionType();

            // Setup Preview Titik untuk Edit Modal
            editLabelingPins = {};
            if (type === 'labeling' && correctLabeling) {
                previewLabelImage(imagePath, 'edit-picker-preview-img', 'edit-interactive-picker-box');
                const pairs = correctLabeling.split('|');
                pairs.forEach(p => {
                    if (p.includes(':')) {
                        const [lbl, coords] = p.split(':');
                        const [x, y] = coords.split(',');
                        if (lbl && x && y) {
                            editLabelingPins[lbl.trim()] = { x: parseFloat(x.trim()), y: parseFloat(y.trim()) };
                        }
                    }
                });
                renderPins('edit_correct_answer_labeling', 'edit-pins-overlay-container', 'edit-tags-badge-list', true);
            }

            modal.classList.replace('hidden', 'flex');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.replace('flex', 'hidden');
        }
    </script>
</body>
</html>