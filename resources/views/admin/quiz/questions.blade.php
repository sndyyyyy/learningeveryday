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
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Pertanyaan / Soal</label>
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
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan A</label>
                            <input type="text" name="option_a" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan B</label>
                            <input type="text" name="option_b" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan C</label>
                            <input type="text" name="option_c" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan D</label>
                            <input type="text" name="option_d" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Jawaban Benar</label>
                        <select name="correct_answer" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Pembahasan (Optional)</label>
                        <textarea name="explanation" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500" placeholder="Masukkan materi/pembahasan soal..."></textarea>
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
                                        <option value="{{ $part->id }}">
                                            📄 {{ $part->part_name }}
                                        </option>
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

        <div class="md:col-span-2 bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
            <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4">Daftar Soal Kuis: <span class="text-indigo-600">{{ $quiz->title }}</span></h2>
            <div class="space-y-6">
                @forelse($questions as $index => $q)
                    <div class="border-b border-gray-100 pb-6 relative group">
                        <div class="flex justify-between items-start mb-2">
                            <p class="font-semibold text-gray-800 text-sm leading-relaxed max-w-[80%]">
                                <span class="text-indigo-600 font-bold">{{ $index + 1 }}.</span> {{ $q->question_text }}
                            </p>
                            
                            <div class="flex space-x-2">
                                <button onclick="openEditModal({{ json_encode($q) }})" class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2 py-1 rounded text-xs font-bold transition cursor-pointer">
                                    Edit
                                </button>
                                <form action="{{ route('admin.quiz.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin menghapus soal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 px-2 py-1 rounded text-xs font-bold transition cursor-pointer">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if($q->image)
                            <div class="my-3 pl-4">
                                <img src="/storage/{{ $q->image }}" alt="Gambar Soal" class="max-h-32 object-contain rounded-lg border border-gray-100 shadow-xs">
                            </div>
                        @endif

                        @if($q->audio)
                            <div class="my-3 pl-4 max-w-sm">
<audio src="{{ route('audio.stream', ['path' => $q->audio]) }}" controls class="w-full max-w-sm outline-none scale-90 origin-left"></audio>                            </div>
                        @endif
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2 pl-4 text-xs text-gray-600">
                            <p class="p-2 rounded-md {{ $q->correct_answer == 'A' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200' : 'bg-gray-50' }}">A. {{ $q->options['A'] }}</p>
                            <p class="p-2 rounded-md {{ $q->correct_answer == 'B' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200' : 'bg-gray-50' }}">B. {{ $q->options['B'] }}</p>
                            <p class="p-2 rounded-md {{ $q->correct_answer == 'C' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200' : 'bg-gray-50' }}">C. {{ $q->options['C'] }}</p>
                            <p class="p-2 rounded-md {{ $q->correct_answer == 'D' ? 'bg-emerald-50 text-emerald-700 font-bold border border-emerald-200' : 'bg-gray-50' }}">D. {{ $q->options['D'] }}</p>
                        </div>
                        
                        @if($q->explanation)
                            <p class="text-xs text-gray-500 mt-3 bg-indigo-50/50 p-2.5 rounded-lg border-l-2 border-indigo-500 pl-4">
                                <span class="font-bold text-indigo-700">Pembahasan:</span> {{ $q->explanation }}
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-400 text-center py-8 text-sm">Kuis ini belum memiliki soal.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div id="edit-modal" class="fixed inset-0 bg-black/50 hidden justify-center items-center z-50 backdrop-blur-xs">
        <div class="bg-white p-6 rounded-2xl max-w-lg w-[90%] shadow-2xl overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800">Edit Soal Pertanyaan</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-xl cursor-pointer">&times;</button>
            </div>
            
            <form id="edit-form" method="POST" enctype="multipart/form-data" class="space-y-4 text-left">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Pertanyaan / Soal</label>
                    <textarea name="question_text" id="edit_question_text" rows="2" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none"></textarea>
                </div>
                <div class="border border-gray-100 p-3 rounded-xl bg-gray-50/50">
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Ganti Gambar (Optional)</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-gray-500 mb-2">
                    
                    <div id="edit_image_delete_container" class="hidden flex items-center space-x-2 mt-1">
                        <input type="checkbox" name="delete_image" id="delete_image_checkbox" value="1" class="w-3.5 h-3.5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="delete_image_checkbox" class="text-xs text-red-500 font-medium cursor-pointer">🗑️ Hapus file gambar yang ada saat ini</label>
                    </div>
                </div>

                <div class="border border-gray-100 p-3 rounded-xl bg-gray-50/50">
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Ganti Audio Listening (Optional)</label>
                    <input type="file" name="audio" accept="audio/*" class="w-full text-xs text-gray-500 mb-2">
                    
                    <div id="edit_audio_delete_container" class="hidden flex items-center space-x-2 mt-1">
                        <input type="checkbox" name="delete_audio" id="delete_audio_checkbox" value="1" class="w-3.5 h-3.5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="delete_audio_checkbox" class="text-xs text-red-500 font-medium cursor-pointer">🗑️ Hapus file audio yang ada saat ini</label>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan A</label>
                        <input type="text" name="option_a" id="edit_option_a" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan B</label>
                        <input type="text" name="option_b" id="edit_option_b" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan C</label>
                        <input type="text" name="option_c" id="edit_option_c" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Pilihan D</label>
                        <input type="text" name="option_d" id="edit_option_d" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Jawaban Benar</label>
                    <select name="correct_answer" id="edit_correct_answer" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:outline-none">
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Pembahasan</label>
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
        function openEditModal(questionData) {
            const modal = document.getElementById('edit-modal');
            const form = document.getElementById('edit-form');
            
            form.action = `/admin/question/${questionData.id}`;
            
            document.getElementById('edit_question_text').value = questionData.question_text;
            document.getElementById('edit_option_a').value = questionData.options.A;
            document.getElementById('edit_option_b').value = questionData.options.B;
            document.getElementById('edit_option_c').value = questionData.options.C;
            document.getElementById('edit_option_d').value = questionData.options.D;
            document.getElementById('edit_correct_answer').value = questionData.correct_answer;
            document.getElementById('edit_explanation').value = questionData.explanation || '';

            // --- KONTROL DINAMIS TOMBOL HAPUS MEDIA ---
            const imgDeleteBox = document.getElementById('edit_image_delete_container');
            const audioDeleteBox = document.getElementById('edit_audio_delete_container');
            
            // Reset status centang ke false setiap kali modal dibuka
            document.getElementById('delete_image_checkbox').checked = false;
            document.getElementById('delete_audio_checkbox').checked = false;

            // Jika soal punya gambar, munculkan opsi hapus gambar
            if (questionData.image) {
                imgDeleteBox.classList.replace('hidden', 'flex');
            } else {
                imgDeleteBox.classList.replace('flex', 'hidden');
            }

            // Jika soal punya audio, munculkan opsi hapus audio
            if (questionData.audio) {
                audioDeleteBox.classList.replace('hidden', 'flex');
            } else {
                audioDeleteBox.classList.replace('flex', 'hidden');
            }
            // ------------------------------------------

            modal.classList.replace('hidden', 'flex');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.replace('flex', 'hidden');
        }
    </script>
</body>
</html>