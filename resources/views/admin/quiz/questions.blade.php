<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Soal - {{ $quiz->title }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans">

    @include('layouts.navbar')

    <div class="max-w-6xl mx-auto mt-6 md:mt-8 px-4 pb-16 grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
        
        <!-- SISI KIRI: INPUT SOAL & TARIK BANK -->
        <div class="md:col-span-1 flex flex-col space-y-6">
            
            <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm h-fit border border-gray-100">
                <h2 class="text-base font-bold text-gray-800 mb-4">Tambah Pertanyaan</h2>
                
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-xs font-semibold">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-xs font-semibold">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('admin.quiz.questions.store', $quiz->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Tipe Soal</label>
                        <select name="type" id="in-type" onchange="toggleQuestionType()" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 bg-indigo-50 font-bold text-indigo-700">
                            <option value="multiple_choice">🔘 Pilihan Ganda</option>
                            <option value="essay">📝 Essay</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Pertanyaan / Soal</label>
                        <p id="hint-essay" class="hidden text-[10px] text-amber-600 mb-1 font-medium">💡 Gunakan kata <b>[blank]</b> untuk menandai area kosong.</p>
                        <textarea name="question_text" rows="2" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Upload Gambar (Optional)</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Upload Audio Listening (Optional)</label>
                        <input type="file" name="audio" accept="audio/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    </div>
                    
                    <div id="container-multiple-choice" class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan A</label><input type="text" name="option_a" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none"></div>
                            <div><label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan B</label><input type="text" name="option_b" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none"></div>
                            <div><label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan C</label><input type="text" name="option_c" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none"></div>
                            <div><label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan D</label><input type="text" name="option_d" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none"></div>
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-semibold mb-1">Jawaban Benar</label>
                            <select name="correct_answer_mc" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                                <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                            </select>
                        </div>
                    </div>

                    <div id="container-essay" class="hidden bg-amber-50 p-3 rounded-lg border border-amber-200 space-y-2">
                        <label class="block text-amber-800 text-xs font-bold">Kunci Jawaban Isian</label>
                        <p class="text-[10px] text-amber-700 leading-normal">
                            • Pemisah antar-blank gunakan <b>|</b><br>
                            • Variasi sinonim jawaban gunakan <b>/</b><br>
                            <i>Contoh: <b>Bandung / Bandoeng | Jawa Barat / Jabar</b></i>
                        </p>
                        <input type="text" name="correct_answer_essay" class="w-full px-3 py-2 border border-amber-300 rounded-lg text-xs focus:outline-none bg-white font-medium">
                    </div>

                    <div class="border border-indigo-100 bg-indigo-50/30 p-3 rounded-xl space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <label class="block text-indigo-800 text-[11px] font-bold">Tampilkan Pembahasan ke Peserta?</label>
                            <select name="is_show_explanation" class="px-2 py-1.5 border border-indigo-200 rounded-md text-[11px] font-bold focus:outline-none bg-white text-indigo-700">
                                <option value="1">Ya, Tampilkan</option>
                                <option value="0">Tidak, Sembunyikan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600 text-[10px] font-semibold mb-1">Teks Pembahasan (Optional)</label>
                            <textarea name="explanation" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-gray-600 text-[10px] font-semibold mb-1">Link Video/Referensi (Optional)</label>
                            <input type="url" name="explanation_link" placeholder="https://youtube.com/..." class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg text-xs transition cursor-pointer">Simpan Soal</button>
                </form>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-800 mb-1 flex items-center space-x-1">
                    <span>Metode 2: Tarik dari Bank Soal</span>
                </h3>
                <p class="text-[10px] text-gray-400 mb-4 leading-relaxed">Pilih salah satu materi part di Bank Soal untuk dimasukkan langsung secara massal ke kuis ini.</p>
                
                <form action="{{ route('admin.quiz.pull_bank', $quiz->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Pilih Materi / Part Bank</label>
                        <select name="bank_part_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 bg-white">
                            <option value="">-- Pilih Part Bank Soal --</option>
                            @foreach($bankSoalList as $bank)
                                <optgroup label="🏢 Kategori: {{ $bank->name }}">
                                    @foreach($bank->parts as $part)
                                        <option value="{{ $part->id }}">📄 {{ $part->part_name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg text-xs transition cursor-pointer shadow-xs flex items-center justify-center space-x-1">
                        <span>Masukkan Soal ke Kuis</span>
                    </button>
                </form>
            </div>
            
        </div>

        <!-- SISI KANAN: DAFTAR SOAL KUIS & AKSI GLOBAL -->
        <div class="md:col-span-2 bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100 h-fit space-y-6">
            
            <!-- HEADER DAFTAR SOAL + BARIS TOMBOL PENGATURAN MASSAL (BULK TOGGLE) -->
            <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-base md:text-lg font-bold text-gray-800">Daftar Soal Kuis: <span class="text-indigo-600">{{ $quiz->title }}</span></h2>
                    <p class="text-xs text-gray-500 mt-0.5">Total {{ $questions->count() }} Pertanyaan Aktif</p>
                </div>

                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.quiz.toggle_all_explanations', $quiz->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="1">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg transition shadow-xs flex items-center gap-1 cursor-pointer">
                            <span>Tampilkan Semua</span>
                        </button>
                    </form>

                    <form action="{{ route('admin.quiz.toggle_all_explanations', $quiz->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="0">
                        <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-[11px] font-bold px-3 py-1.5 rounded-lg transition shadow-xs flex items-center gap-1 cursor-pointer">
                            <span>Sembunyikan Semua</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- DAFTAR ITEM SOAL -->
            <div class="space-y-6">
                @forelse($questions as $index => $q)
                    <div class="border-b border-gray-100 pb-6 relative group">
                        
                        <div class="absolute top-0 right-0">
                            @if($q->is_show_explanation)
                                <span class="bg-emerald-100 text-emerald-700 border border-emerald-200 text-[9px] font-bold px-2 py-0.5 rounded-full">Bahas: Tampil</span>
                            @else
                                <span class="bg-gray-100 text-gray-500 border border-gray-200 text-[9px] font-bold px-2 py-0.5 rounded-full">Bahas: Sembunyi</span>
                            @endif
                        </div>

                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1 pr-16">
                                <span class="text-[9px] px-2 py-0.5 rounded border mb-1.5 inline-block font-bold uppercase tracking-wider {{ $q->type === 'essay' ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-blue-50 text-blue-600 border-blue-200' }}">
                                    {{ $q->type === 'essay' ? '📝 Essay' : '🔘 Pilihan Ganda' }}
                                </span>
                                <p class="font-semibold text-gray-800 text-sm leading-relaxed max-w-[90%]">
                                    <span class="text-indigo-600 font-bold">{{ $index + 1 }}.</span> {{ $q->question_text }}
                                </p>
                            </div>
                            
                            <div class="flex flex-col gap-1.5 shrink-0 mt-6">
                                @php
                                    // PERBAIKAN PARSING KUNCI JAWABAN ESSAY ARRAY 2 DIMENSI
                                    $essayAnswerString = '';
                                    if ($q->type === 'essay' && $q->correct_answer) {
                                        $decoded = json_decode($q->correct_answer, true);
                                        if (is_array($decoded)) {
                                            $blankParts = [];
                                            foreach ($decoded as $bIdx => $aliases) {
                                                if (is_array($aliases)) {
                                                    $blankParts[] = "Blank " . ($bIdx + 1) . ": " . implode(' / ', array_map('ucwords', $aliases));
                                                } else {
                                                    $blankParts[] = $aliases;
                                                }
                                            }
                                            $essayAnswerString = implode(' | ', $blankParts);
                                        } else {
                                            $essayAnswerString = $q->correct_answer;
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
                                        data-explanation="{{ $q->explanation }}"
                                        data-isshow="{{ $q->is_show_explanation ? '1' : '0' }}"
                                        data-link="{{ $q->explanation_link }}"
                                        data-hasimage="{{ $q->image ? '1' : '0' }}"
                                        data-hasaudio="{{ $q->audio ? '1' : '0' }}"
                                        onclick="openEditModal(this)" 
                                        class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1 rounded text-[11px] font-bold transition cursor-pointer text-center">
                                    Edit
                                </button>
                                <form action="{{ route('admin.quiz.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin menghapus soal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full text-red-500 hover:text-red-700 bg-red-50 px-3 py-1 rounded text-[11px] font-bold transition cursor-pointer text-center">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if($q->image)
                            <div class="my-3 pl-4"><img src="/storage/{{ $q->image }}" alt="Gambar Soal" class="max-h-32 object-contain rounded-lg border border-gray-100 shadow-xs"></div>
                        @endif
                        @if($q->audio)
                            <div class="my-3 pl-4 max-w-sm"><audio src="{{ route('audio.stream', ['path' => $q->audio]) }}" controls class="w-full max-w-sm outline-none scale-90 origin-left"></audio></div>
                        @endif
                        
                        @if($q->type === 'essay')
                            <div class="mt-2 pl-4 text-xs">
                                <p class="bg-amber-50/70 text-amber-800 p-2.5 rounded-lg border border-amber-200/60 font-semibold inline-block">
                                    <span class="text-amber-500 mr-1">🔑</span> Kunci Jawaban: <span class="font-bold underline decoration-amber-300">{{ $essayAnswerString }}</span>
                                </p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2 pl-4 text-xs text-gray-600">
                                <p class="p-2 rounded-md {{ $q->correct_answer == 'A' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200' : 'bg-gray-50' }}">A. {{ $q->options['A'] ?? '' }}</p>
                                <p class="p-2 rounded-md {{ $q->correct_answer == 'B' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200' : 'bg-gray-50' }}">B. {{ $q->options['B'] ?? '' }}</p>
                                <p class="p-2 rounded-md {{ $q->correct_answer == 'C' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200' : 'bg-gray-50' }}">C. {{ $q->options['C'] ?? '' }}</p>
                                <p class="p-2 rounded-md {{ $q->correct_answer == 'D' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200' : 'bg-gray-50' }}">D. {{ $q->options['D'] ?? '' }}</p>
                            </div>
                        @endif
                        
                        @if($q->explanation || $q->explanation_link)
                            <div class="text-xs mt-3 bg-indigo-50/50 p-3 rounded-lg border-l-2 border-indigo-500 pl-4 space-y-1">
                                @if($q->explanation)
                                    <p class="text-gray-600"><span class="font-bold text-indigo-700">Pembahasan:</span> {{ $q->explanation }}</p>
                                @endif
                                @if($q->explanation_link)
                                    <p class="text-indigo-600 font-medium break-all">🔗 <a href="{{ $q->explanation_link }}" target="_blank" class="hover:underline">{{ $q->explanation_link }}</a></p>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-400 text-center py-8 text-sm">Kuis ini belum memiliki soal.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- MODAL POPUP EDIT SOAL KUIS -->
    <div id="edit-modal" class="fixed inset-0 bg-black/50 hidden justify-center items-center z-50 backdrop-blur-xs">
        <div class="bg-white p-6 rounded-2xl max-w-lg w-[90%] shadow-2xl overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800">Edit Soal Kuis</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-xl cursor-pointer">&times;</button>
            </div>
            
            <form id="edit-form" method="POST" enctype="multipart/form-data" class="space-y-4 text-left">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-gray-600 text-[11px] font-semibold mb-1">Tipe Soal</label>
                    <select name="type" id="edit_type" onchange="toggleEditQuestionType()" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 bg-indigo-50 font-bold text-indigo-700">
                        <option value="multiple_choice">🔘 Pilihan Ganda</option>
                        <option value="essay">📝 Essay</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Pertanyaan / Soal</label>
                    <p id="edit_hint_essay" class="hidden text-[10px] text-amber-600 mb-1 font-medium">💡 Gunakan kata <b>[blank]</b> untuk rumpang.</p>
                    <textarea name="question_text" id="edit_question_text" rows="2" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none"></textarea>
                </div>
                
                <div class="border border-gray-100 p-3 rounded-xl bg-gray-50/50">
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Ganti Gambar (Optional)</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-gray-500 mb-2">
                    <div id="edit_image_delete_container" class="hidden flex items-center space-x-2 mt-1">
                        <input type="checkbox" name="delete_image" id="delete_image_checkbox" value="1" class="w-3.5 h-3.5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="delete_image_checkbox" class="text-xs text-red-500 font-medium cursor-pointer">🗑️ Hapus gambar lama</label>
                    </div>
                </div>

                <div class="border border-gray-100 p-3 rounded-xl bg-gray-50/50">
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Ganti Audio Listening (Optional)</label>
                    <input type="file" name="audio" accept="audio/*" class="w-full text-xs text-gray-500 mb-2">
                    <div id="edit_audio_delete_container" class="hidden flex items-center space-x-2 mt-1">
                        <input type="checkbox" name="delete_audio" id="delete_audio_checkbox" value="1" class="w-3.5 h-3.5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="delete_audio_checkbox" class="text-xs text-red-500 font-medium cursor-pointer">🗑️ Hapus audio lama</label>
                    </div>
                </div>
                
                <div id="edit_container_multiple_choice" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan A</label><input type="text" name="option_a" id="edit_option_a" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none"></div>
                        <div><label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan B</label><input type="text" name="option_b" id="edit_option_b" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none"></div>
                        <div><label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan C</label><input type="text" name="option_c" id="edit_option_c" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none"></div>
                        <div><label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan D</label><input type="text" name="option_d" id="edit_option_d" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none"></div>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Jawaban Benar</label>
                        <select name="correct_answer_mc" id="edit_correct_answer_mc" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                            <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                        </select>
                    </div>
                </div>

                <div id="edit_container_essay" class="hidden bg-amber-50 p-3 rounded-lg border border-amber-200 space-y-2">
                    <label class="block text-amber-800 text-[11px] font-bold">Kunci Jawaban Isian</label>
                    <p class="text-[10px] text-amber-700 leading-normal">
                        • Pemisah antar-blank gunakan <b>|</b><br>
                        • Variasi sinonim jawaban gunakan <b>/</b><br>
                        <i>Contoh: <b>Bandung / Bandoeng | Jawa Barat / Jabar</b></i>
                    </p>
                    <input type="text" name="correct_answer_essay" id="edit_correct_answer_essay" class="w-full px-3 py-2 border border-amber-300 rounded-lg text-xs focus:outline-none focus:border-amber-500 bg-white font-medium">
                </div>

                <div class="border border-indigo-100 bg-indigo-50/30 p-3 rounded-xl space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <label class="block text-indigo-800 text-[11px] font-bold">Tampilkan Pembahasan?</label>
                        <select name="is_show_explanation" id="edit_is_show_explanation" class="px-2 py-1.5 border border-indigo-200 rounded-md text-[11px] font-bold focus:outline-none bg-white text-indigo-700">
                            <option value="1">Ya, Tampilkan</option>
                            <option value="0">Tidak, Sembunyikan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-[10px] font-semibold mb-1">Teks Pembahasan</label>
                        <textarea name="explanation" id="edit_explanation" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-[10px] font-semibold mb-1">Link Video/Referensi</label>
                        <input type="url" name="explanation_link" id="edit_explanation_link" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                    </div>
                </div>
                
                <div class="flex space-x-2 pt-2">
                    <button type="button" onclick="closeEditModal()" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 rounded-lg text-xs transition cursor-pointer">Batal</button>
                    <button type="submit" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg text-xs transition cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleQuestionType() {
            const type = document.getElementById("in-type").value;
            const containerMC = document.getElementById("container-multiple-choice");
            const containerEssay = document.getElementById("container-essay");
            const hintEssay = document.getElementById("hint-essay");
            if (type === 'essay') {
                containerMC.classList.add('hidden');
                containerEssay.classList.remove('hidden');
                hintEssay.classList.remove('hidden');
            } else {
                containerMC.classList.remove('hidden');
                containerEssay.classList.add('hidden');
                hintEssay.classList.add('hidden');
            }
        }

        function toggleEditQuestionType() {
            const type = document.getElementById("edit_type").value;
            const containerMC = document.getElementById("edit_container_multiple_choice");
            const containerEssay = document.getElementById("edit_container_essay");
            const hintEssay = document.getElementById("edit_hint_essay");
            if (type === 'essay') {
                containerMC.classList.add('hidden');
                containerEssay.classList.remove('hidden');
                hintEssay.classList.remove('hidden');
            } else {
                containerMC.classList.remove('hidden');
                containerEssay.classList.add('hidden');
                hintEssay.classList.add('hidden');
            }
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
            const explanation = btnElement.getAttribute('data-explanation');
            
            const isShow = btnElement.getAttribute('data-isshow');
            const link = btnElement.getAttribute('data-link');
            
            const hasImage = btnElement.getAttribute('data-hasimage') === '1';
            const hasAudio = btnElement.getAttribute('data-hasaudio') === '1';

            const modal = document.getElementById('edit-modal');
            const form = document.getElementById('edit-form');
            form.action = `/admin/question/${id}`;
            
            document.getElementById('edit_type').value = type;
            document.getElementById('edit_question_text').value = text;
            document.getElementById('edit_option_a').value = optA;
            document.getElementById('edit_option_b').value = optB;
            document.getElementById('edit_option_c').value = optC;
            document.getElementById('edit_option_d').value = optD;
            document.getElementById('edit_correct_answer_mc').value = correctMC;
            document.getElementById('edit_correct_answer_essay').value = correctEssay;
            
            document.getElementById('edit_explanation').value = explanation || '';
            document.getElementById('edit_explanation_link').value = link || '';
            document.getElementById('edit_is_show_explanation').value = isShow;

            toggleEditQuestionType();

            const imgDeleteBox = document.getElementById('edit_image_delete_container');
            const audioDeleteBox = document.getElementById('edit_audio_delete_container');
            document.getElementById('delete_image_checkbox').checked = false;
            document.getElementById('delete_audio_checkbox').checked = false;
            hasImage ? imgDeleteBox.classList.replace('hidden', 'flex') : imgDeleteBox.classList.replace('flex', 'hidden');
            hasAudio ? audioDeleteBox.classList.replace('hidden', 'flex') : audioDeleteBox.classList.replace('flex', 'hidden');

            modal.classList.replace('hidden', 'flex');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.replace('flex', 'hidden');
        }
    </script>
</body>
</html>