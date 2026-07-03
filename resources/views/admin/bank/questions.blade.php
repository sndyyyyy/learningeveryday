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
                            <option value="essay">📝 Isian Rumpang / Essay</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Pertanyaan / Teks Soal</label>
                        <p id="hint-essay" class="hidden text-[10px] text-amber-600 mb-1 font-medium">💡 Gunakan kata <b>[blank]</b> untuk menandai area yang harus diisi peserta. Contoh: <i>"Ibukota Jepang adalah [blank]."</i></p>
                        <textarea name="question_text" rows="3" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Upload Gambar (Optional)</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Upload Audio Listening (Optional)</label>
                        <input type="file" name="audio" accept="audio/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    </div>
                    
                    <div id="container-multiple-choice" class="space-y-3.5 transition-all">
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-gray-600 text-[10px] font-semibold mb-0.5">Pilihan A</label>
                                <input type="text" name="option_a" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-gray-600 text-[10px] font-semibold mb-0.5">Pilihan B</label>
                                <input type="text" name="option_b" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-gray-600 text-[10px] font-semibold mb-0.5">Pilihan C</label>
                                <input type="text" name="option_c" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-gray-600 text-[10px] font-semibold mb-0.5">Pilihan D</label>
                                <input type="text" name="option_d" class="w-full px-2.5 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-600 text-[11px] font-semibold mb-1">Jawaban Benar (Pilihan Ganda)</label>
                            <select name="correct_answer_mc" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>

                    <div id="container-essay" class="hidden mt-2 bg-amber-50 p-3 rounded-lg border border-amber-200">
                        <label class="block text-amber-800 text-[11px] font-bold mb-1">Kunci Jawaban Isian Rumpang</label>
                        <p class="text-[10px] text-amber-600 mb-2 leading-relaxed">Masukkan jawaban berurutan sesuai jumlah kata <b>[blank]</b>. Pisahkan dengan koma (,).<br>Contoh: <b>Jakarta, 1945</b></p>
                        <input type="text" name="correct_answer_essay" class="w-full px-3 py-2 border border-amber-300 rounded-lg text-xs focus:outline-none focus:border-amber-500 bg-white" placeholder="Jawaban 1, Jawaban 2...">
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

<div id="container-import-excel" class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
    <h3 class="text-sm font-bold text-gray-800 mb-1 flex items-center space-x-1">
        <span>📊</span> <span>Metode 2: Import dari Excel (.CSV)</span>
    </h3>
    
    <!-- PANDUAN PENGISIAN YANG LEBIH JELAS -->
    <div class="text-[10px] text-gray-500 mb-3 leading-relaxed bg-gray-50 p-2 rounded-md border border-gray-200">
        <p class="font-bold text-gray-700 mb-1">Format kolom berurutan: <span class="text-indigo-600">Soal, Opsi A, Opsi B, Opsi C, Opsi D, Jawaban Benar, Pembahasan.</span></p>
        <ul class="list-disc pl-4 text-gray-500 space-y-0.5">
            <li><b>Pilihan Ganda:</b> Isi lengkap opsi A-D. Kolom Jawaban Benar isi dengan huruf A/B/C/D.</li>
            <li><b>Essay / Rumpang:</b> Kosongkan kolom Opsi A-D (atau isi tanda strip <code class="bg-gray-200 px-1 rounded">-</code>). Kolom Jawaban Benar isi dengan teks jawaban dipisah koma (contoh: <i>Jakarta, 1945</i>). Sisipkan <b>[blank]</b> pada teks soal.</li>
        </ul>
    </div>
    
    <form action="{{ route('admin.bank.questions.import', $part->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <div class="border-2 border-dashed border-gray-200 rounded-xl p-3 text-center bg-white hover:bg-gray-50 transition cursor-pointer">
            <input type="file" name="excel_file" required accept=".csv" class="w-full text-xs text-gray-500 file:text-[11px] file:font-bold file:bg-indigo-50 file:text-indigo-700 file:border-0 file:rounded-md file:px-3 file:py-1.5 cursor-pointer">
        </div>
        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-lg text-xs transition shadow-sm">
            🚀 Jalankan Import File CSV
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
                                <span class="text-[9px] px-2 py-0.5 rounded border mb-1.5 inline-block font-bold uppercase tracking-wider {{ $q->type === 'essay' ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-blue-50 text-blue-600 border-blue-200' }}">
                                    {{ $q->type === 'essay' ? '📝 Isian Rumpang' : '🔘 Pilihan Ganda' }}
                                </span>
                                <p class="font-semibold text-gray-800 text-sm leading-relaxed">
                                    <span class="text-indigo-600 font-bold">{{ $index + 1 }}.</span> {{ $q->question_text }}
                                </p>
                            </div>
                            
                            <div class="flex flex-row gap-1.5 shrink-0 items-center">
                                @php
                                    // Ekstrak string jawaban essay jika berupa JSON array
                                    $essayAnswerString = '';
                                    if($q->type === 'essay' && $q->correct_answer) {
                                        $decoded = json_decode($q->correct_answer, true);
                                        $essayAnswerString = is_array($decoded) ? implode(', ', $decoded) : $q->correct_answer;
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
                            <div class="my-3 pl-4">
                                <img src="/storage/{{ $q->image }}" alt="Media Soal" class="max-h-32 object-contain rounded-lg border border-gray-100 shadow-2xs">
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
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2.5 pl-4 text-xs text-gray-600">
                                <p class="p-2 rounded-md {{ $q->correct_answer == 'A' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-100' : 'bg-gray-50' }}">A. {{ $q->options['A'] ?? '' }}</p>
                                <p class="p-2 rounded-md {{ $q->correct_answer == 'B' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-100' : 'bg-gray-50' }}">B. {{ $q->options['B'] ?? '' }}</p>
                                <p class="p-2 rounded-md {{ $q->correct_answer == 'C' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-100' : 'bg-gray-50' }}">C. {{ $q->options['C'] ?? '' }}</p>
                                <p class="p-2 rounded-md {{ $q->correct_answer == 'D' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-100' : 'bg-gray-50' }}">D. {{ $q->options['D'] ?? '' }}</p>
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
                        <option value="essay">📝 Isian Rumpang / Essay</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-600 text-[11px] font-semibold mb-1">Pertanyaan / Soal</label>
                    <p id="edit_hint_essay" class="hidden text-[10px] text-amber-600 mb-1 font-medium">💡 Gunakan kata <b>[blank]</b> untuk menandai rumpang.</p>
                    <textarea name="question_text" id="edit_question_text" rows="3" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none"></textarea>
                </div>
                
                <div class="border border-gray-100 p-3 rounded-xl bg-gray-50/50">
                    <label class="block text-gray-600 text-[11px] font-semibold mb-1">Ganti Gambar (Optional)</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-gray-500 mb-2">
                    <div id="edit_image_delete_container" class="hidden flex-row items-center space-x-2 mt-1">
                        <input type="checkbox" name="delete_image" id="delete_image_checkbox" value="1" class="w-3.5 h-3.5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="delete_image_checkbox" class="text-[11px] text-red-500 font-medium cursor-pointer">🗑️ Hapus file gambar lama</label>
                    </div>
                </div>

                <div class="border border-gray-100 p-3 rounded-xl bg-gray-50/50">
                    <label class="block text-gray-600 text-[11px] font-semibold mb-1">Ganti Audio Listening (Optional)</label>
                    <input type="file" name="audio" accept="audio/*" class="w-full text-xs text-gray-500 mb-2">
                    <div id="edit_audio_delete_container" class="hidden flex-row items-center space-x-2 mt-1">
                        <input type="checkbox" name="delete_audio" id="delete_audio_checkbox" value="1" class="w-3.5 h-3.5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="delete_audio_checkbox" class="text-[11px] text-red-500 font-medium cursor-pointer">🗑️ Hapus file audio lama</label>
                    </div>
                </div>
                
                <div id="edit_container_multiple_choice" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-600 text-[11px] font-semibold mb-1">Pilihan A</label>
                            <input type="text" name="option_a" id="edit_option_a" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-[11px] font-semibold mb-1">Pilihan B</label>
                            <input type="text" name="option_b" id="edit_option_b" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-[11px] font-semibold mb-1">Pilihan C</label>
                            <input type="text" name="option_c" id="edit_option_c" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-[11px] font-semibold mb-1">Pilihan D</label>
                            <input type="text" name="option_d" id="edit_option_d" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-[11px] font-semibold mb-1">Jawaban Benar</label>
                        <select name="correct_answer_mc" id="edit_correct_answer_mc" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                </div>

                <div id="edit_container_essay" class="hidden bg-amber-50 p-3 rounded-lg border border-amber-200">
                    <label class="block text-amber-800 text-[11px] font-bold mb-1">Kunci Jawaban Isian Rumpang</label>
                    <p class="text-[10px] text-amber-600 mb-2 leading-relaxed">Pisahkan jawaban dengan koma (,). Contoh: <b>Jakarta, 1945</b></p>
                    <input type="text" name="correct_answer_essay" id="edit_correct_answer_essay" class="w-full px-3 py-2 border border-amber-300 rounded-lg text-xs focus:outline-none focus:border-amber-500 bg-white">
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

    <script>
        // Logika Form Kiri (Input Baru)
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

        // Logika Form Kanan (Modal Edit)
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
            const hasImage = btnElement.getAttribute('data-hasimage') === '1';
            const hasAudio = btnElement.getAttribute('data-hasaudio') === '1';

            const modal = document.getElementById('edit-modal');
            const form = document.getElementById('edit-form');
            
            form.action = `/admin/bank/questions/${id}`;
            
            // Populate Data
            document.getElementById('edit_type').value = type;
            document.getElementById('edit_question_text').value = text;
            document.getElementById('edit_option_a').value = optA;
            document.getElementById('edit_option_b').value = optB;
            document.getElementById('edit_option_c').value = optC;
            document.getElementById('edit_option_d').value = optD;
            document.getElementById('edit_correct_answer_mc').value = correctMC;
            document.getElementById('edit_correct_answer_essay').value = correctEssay;
            document.getElementById('edit_explanation').value = explanation || '';

            // Panggil fungsi toggle agar form yang muncul sesuai dengan tipe soal
            toggleEditQuestionType();

            // Kontrol Media Delete Checkbox
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