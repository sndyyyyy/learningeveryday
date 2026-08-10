<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Kuis</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.2s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-100 font-sans">

@include('layouts.navbar')

    <div class="bg-white border-t border-gray-100 py-3 px-4 md:px-8 flex items-center shadow-xs">
        <a href="{{ route('admin.dashboard.utama') }}" class="text-xs md:text-sm text-gray-500 hover:text-indigo-600 font-semibold transition">
            &larr; Kembali ke Beranda
        </a>
    </div>

    <div class="max-w-6xl mx-auto mt-8 px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- SISI KIRI: PEMBUATAN KUIS -->
        <div class="bg-white p-6 rounded-xl shadow-sm h-fit border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Buat Kuis Baru</h2>
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm font-medium">{{ session('success') }}</div>
            @endif
            <form action="{{ route('admin.quiz.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="auth_role" value="{{ auth()->user()->role }}">

                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Judul Kuis</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Deskripsi</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <!-- OPSI KHUSUS SUPER ADMIN -->
                @if(auth()->user()->role === 'super_admin')
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Akses Paket Kuis (Tier)</label>
                        <select name="tier_access" id="select-tier-access" onchange="toggleSpecialTestDropdown('create')" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none bg-indigo-50 font-bold text-indigo-700">
                            <option value="basic">Kuis Basic (Diakses Basic & Premium)</option>
                            <option value="premium">Khusus Premium Only</option>
                            <option value="khusus">Tes Khusus (Sertifikasi)</option>
                        </select>
                    </div>

                    <!-- DROPDOWN DINAMIS JENIS TES KHUSUS (HANYA MUNCUL JIKA TIER KHUSUS DIPILIH) -->
                    <div id="container-special-test-create" class="hidden">
                        <label class="block text-sky-700 text-xs font-semibold mb-1">Pilih Jenis Tes Khusus</label>
                        <select name="special_test_id" class="w-full px-3 py-2 border border-sky-200 rounded-lg text-xs focus:outline-none bg-sky-50 font-bold text-sky-700">
                            <option value="">-- Pilih Jenis Tes Khusus --</option>
                            @foreach($specialTests as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- OPSI KHUSUS ADMIN INSTANSI (TARGET KELAS KUIS) -->
                @if(auth()->user()->role === 'admin')
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Target Kelas Kuis (Khusus Instansi)</label>
                        <select name="class_group" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 font-bold text-purple-700 bg-purple-50/30">
                            <option value="">-- Semua Kelas (Umum) --</option>
                            @foreach($classGroups as $cg)
                                <option value="{{ $cg->name }}">{{ $cg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-lg text-xs transition cursor-pointer">
                    Simpan Kuis Baru
                </button>
            </form>
        </div>

        <!-- SISI KANAN: DAFTAR KUIS -->
        <div class="md:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Daftar Kuis</h2>
            <div class="grid grid-cols-1 gap-4">
                @forelse($quizzes as $quiz)
                    <div class="border border-gray-200 p-4 rounded-xl flex flex-col justify-between bg-gray-50 gap-4 transition hover:bg-gray-100/50">
                        <div class="flex-1 space-y-1.5">
                            <div class="flex items-center flex-wrap gap-2">
                                <h3 class="font-bold text-gray-800 text-base md:text-lg leading-tight">{{ $quiz->title }}</h3>
                                
                                @if($quiz->created_by !== 1 && $quiz->user?->role === 'admin') 
                                    <span class="bg-purple-50 text-purple-700 border border-purple-100 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide">
                                        🏫 Instansi: {{ $quiz->user?->name ?? 'External' }}
                                    </span>
                                    @if($quiz->class_group)
                                        <span class="bg-indigo-50 text-indigo-700 border border-indigo-100 px-2 py-0.5 rounded text-[10px] font-bold">
                                            Kelas: {{ $quiz->class_group }}
                                        </span>
                                    @endif
                                @else
                                    @if($quiz->tier_access === 'premium')
                                        <span class="bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">👑 Premium</span>
                                    @elseif($quiz->tier_access === 'khusus')
                                        <span class="bg-sky-50 text-sky-700 border border-sky-200 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">
                                            ⚓ Tes Khusus {{ $quiz->specialTest ? '('.$quiz->specialTest->name.')' : '' }}
                                        </span>
                                    @else
                                        <span class="bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">🟢 Basic</span>
                                    @endif
                                @endif
                            </div>
                            <p class="text-gray-500 text-xs md:text-sm leading-relaxed">{{ $quiz->description ?? 'Tidak ada deskripsi.' }}</p>
                        </div>
                        
                        <!-- BARIS AKSI KONTROL & LAPORAN -->
                        <div class="shrink-0 pt-2 border-t border-gray-200/60 flex flex-wrap items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- 📊 TOMBOL LAPORAN HASIL KUIS -->
                                <button onclick="openQuizReportModal('{{ $quiz->id }}')" class="text-emerald-700 hover:text-emerald-900 bg-emerald-100/70 hover:bg-emerald-200 px-2.5 py-1.5 rounded-md text-xs font-bold transition cursor-pointer whitespace-nowrap inline-flex items-center gap-1 shadow-2xs">
                                    Laporan Hasil
                                </button>

                                <!-- 📄 TOMBOL CETAK PDF HARDFILE -->
                                <a href="{{ route('admin.quiz.export_pdf', $quiz->id) }}" target="_blank" class="text-sky-700 hover:text-sky-900 bg-sky-100/70 hover:bg-sky-200 px-2.5 py-1.5 rounded-md text-xs font-bold transition cursor-pointer whitespace-nowrap inline-flex items-center gap-1 shadow-2xs">
                                    Cetak PDF Hardfile
                                </a>
                                <a href="{{ route('admin.quiz.export_word', $quiz->id) }}" class="text-blue-700 hover:text-blue-900 bg-blue-100/70 hover:bg-blue-200 px-2.5 py-1.5 rounded-md text-xs font-bold transition cursor-pointer whitespace-nowrap inline-flex items-center gap-1 shadow-2xs">
                                    <span>📝</span> Download Word (.docx)
                                </a>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('admin.quiz.questions', $quiz->id) }}" class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2.5 py-1.5 rounded-md text-xs font-bold transition cursor-pointer whitespace-nowrap inline-block">
                                    Kelola Soal &rarr;
                                </a>

                                <button onclick="openEditQuizModal('{{ $quiz->id }}', '{{ addslashes($quiz->title) }}', '{{ addslashes($quiz->description) }}', '{{ $quiz->tier_access }}', '{{ $quiz->class_group }}', '{{ $quiz->special_test_id }}')" class="text-amber-600 hover:text-amber-800 bg-amber-50 px-2.5 py-1.5 rounded-md text-xs font-bold transition cursor-pointer whitespace-nowrap inline-block">
                                    Edit
                                </button>
                                
                                <form id="form-delete-quiz-{{ $quiz->id }}" action="{{ route('admin.quiz.destroy', $quiz->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            onclick="triggerCustomDeleteModal('form-delete-quiz-{{ $quiz->id }}', '{{ addslashes($quiz->title) }}')"
                                            class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-2.5 py-1.5 rounded-md transition cursor-pointer whitespace-nowrap">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>      
                @empty
                    <p class="text-gray-400 text-center py-4">Belum ada kuis yang dibuat.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- MODAL POP-UP EDIT DATA KUIS -->
    <div id="edit-quiz-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-50 backdrop-blur-xs transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] shadow-2xl animate-fade-in flex flex-col">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-1">
                    <span>✏️</span> Ubah Informasi Kuis
                </h3>
                <button onclick="closeEditQuizModal()" class="text-gray-400 hover:text-gray-600 font-bold cursor-pointer text-lg">&times;</button>
            </div>
            
            <form id="edit-quiz-form" method="POST" class="space-y-4 text-left">
                @csrf
                @method('PUT')
                <input type="hidden" name="auth_role" value="{{ auth()->user()->role }}">

                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Judul Kuis Baru</label>
                    <input type="text" id="edit-quiz-title" name="title" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 font-medium text-gray-800">
                </div>

                <div>
                    <label class="block text-gray-600 text-xs font-semibold mb-1">Deskripsi Baru</label>
                    <textarea id="edit-quiz-description" name="description" rows="2"
                              class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 font-medium text-gray-800"></textarea>
                </div>

                @if(auth()->user()->role === 'admin')
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Target Kelas Kuis (Khusus Instansi)</label>
                        <select id="edit-quiz-class" name="class_group" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 font-bold text-purple-700 bg-purple-50/30">
                            <option value="">-- Semua Kelas (Umum) --</option>
                            @foreach($classGroups as $cg)
                                <option value="{{ $cg->name }}">{{ $cg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if(auth()->user()->role === 'super_admin')
                    <div>
                        <label class="block text-gray-600 text-xs font-semibold mb-1">Ubah Hak Akses Paket (Tier)</label>
                        <select id="edit-quiz-tier" name="tier_access" onchange="toggleSpecialTestDropdown('edit')" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none bg-indigo-50 font-bold text-indigo-700">
                            <option value="basic">Kuis Basic (Diakses Basic & Premium)</option>
                            <option value="premium">Khusus Premium Only</option>
                            <option value="khusus">Tes Khusus (Sertifikasi/Marlins)</option>
                        </select>
                    </div>

                    <div id="container-special-test-edit" class="hidden">
                        <label class="block text-sky-700 text-xs font-semibold mb-1">Pilih Jenis Tes Khusus</label>
                        <select id="edit-quiz-special-test" name="special_test_id" class="w-full px-3 py-2 border border-sky-200 rounded-lg text-xs focus:outline-none bg-sky-50 font-bold text-sky-700">
                            <option value="">-- Pilih Jenis Tes Khusus --</option>
                            @foreach($specialTests as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                <div class="flex flex-row gap-2 w-full pt-1">
                    <button type="button" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 rounded-xl text-xs cursor-pointer transition" onclick="closeEditQuizModal()">
                        Batal
                    </button>
                    <button type="submit" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded-xl text-xs cursor-pointer transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL KONFIRMASI HAPUS KUIS -->
    <div id="custom-delete-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-[60] backdrop-blur-xs transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] text-center shadow-2xl animate-fade-in flex flex-col">
            <div class="w-14 h-14 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <h3 class="text-base font-bold text-gray-800 mb-1">Hapus Paket Kuis?</h3>
            <p class="text-xs text-gray-400 mb-5 leading-relaxed">
                Apakah Anda yakin ingin menghapus kuis <span id="delete-target-name" class="text-red-600 font-bold"></span> beserta seluruh daftar pertanyaan dan rekaman lembar jawab di dalamnya? Tindakan ini permanen.
            </p>
            
            <div class="flex flex-row gap-2 w-full">
                <button type="button" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 rounded-xl text-xs cursor-pointer transition" onclick="closeCustomDeleteModal()">
                    Batal
                </button>
                <button type="button" class="w-1/2 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-xs cursor-pointer transition shadow-xs" onclick="executeFormDelete()">
                    Ya, Hapus Kuis
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL POP-UP LAPORAN HASIL KUIS -->
    <div id="quiz-report-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-[70] backdrop-blur-xs">
        <div class="bg-white p-6 rounded-2xl max-w-2xl w-[90%] shadow-2xl animate-fade-in flex flex-col max-h-[85vh]">
            
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        Laporan Pengerjaan: <span id="report-modal-title" class="text-indigo-600"></span>
                    </h3>
                    <div class="flex gap-4 text-xs mt-1 text-gray-500">
                        <span>Total Peserta: <strong id="report-modal-total" class="text-gray-800">0</strong></span>
                        <span>Rata-Rata Nilai: <strong id="report-modal-avg" class="text-indigo-600">0</strong></span>
                    </div>
                </div>
                <button onclick="closeQuizReportModal()" class="text-gray-400 hover:text-gray-600 font-bold text-xl cursor-pointer">&times;</button>
            </div>

            <div class="overflow-y-auto flex-1 border border-gray-100 rounded-lg">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-400 text-xs uppercase bg-gray-50 sticky top-0">
                            <th class="py-2.5 pl-3 w-10">No</th>
                            <th class="py-2.5 px-3">Nama Peserta / Siswa</th>
                            <th class="py-2.5 px-3">Kelas</th>
                            <th class="py-2.5 px-3 text-center">Skor Akhir</th>
                            <th class="py-2.5 px-3 text-center">Tanggal Pengerjaan</th>
                        </tr>
                    </thead>
                    <tbody id="report-modal-tbody" class="text-gray-600 text-xs divide-y divide-gray-100">
                        <!-- Disuntikkan via JS -->
                    </tbody>
                </table>
            </div>

            <div class="pt-4 text-right">
                <button onclick="closeQuizReportModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-4 py-2 rounded-xl text-xs cursor-pointer transition">
                    Tutup Laporan
                </button>
            </div>
        </div>
    </div>

    <!-- ENGINE JAVASCRIPT KENDALI MODAL -->
    <script>
        let activeDeleteFormId = null;

        function triggerCustomDeleteModal(formId, targetName) {
            activeDeleteFormId = formId;
            document.getElementById('delete-target-name').innerText = targetName;
            
            const modal = document.getElementById('custom-delete-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCustomDeleteModal() {
            const modal = document.getElementById('custom-delete-modal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            activeDeleteFormId = null;
        }

        function executeFormDelete() {
            if (activeDeleteFormId) {
                document.getElementById(activeDeleteFormId).submit();
            }
        }

        function toggleSpecialTestDropdown(type) {
            const selectId = type === 'create' ? 'select-tier-access' : 'edit-quiz-tier';
            const containerId = type === 'create' ? 'container-special-test-create' : 'container-special-test-edit';
            
            const selectEl = document.getElementById(selectId);
            const containerEl = document.getElementById(containerId);

            if (selectEl && containerEl) {
                if (selectEl.value === 'khusus') {
                    containerEl.classList.remove('hidden');
                } else {
                    containerEl.classList.add('hidden');
                }
            }
        }

        function openEditQuizModal(id, title, description, tier, classGroup, specialTestId) {
            const modal = document.getElementById('edit-quiz-modal');
            document.getElementById('edit-quiz-form').action = "/admin/quiz/" + id + "/update";
            document.getElementById('edit-quiz-title').value = title;
            document.getElementById('edit-quiz-description').value = description;
            
            const classSelect = document.getElementById('edit-quiz-class');
            if(classSelect) { classSelect.value = classGroup ?? ''; }

            const tierSelect = document.getElementById('edit-quiz-tier');
            if(tierSelect) { 
                tierSelect.value = tier;
                toggleSpecialTestDropdown('edit');
            }

            const specialTestSelect = document.getElementById('edit-quiz-special-test');
            if(specialTestSelect) { specialTestSelect.value = specialTestId ?? ''; }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => { document.getElementById('edit-quiz-title').focus(); }, 50);
        }

        function closeEditQuizModal() {
            const modal = document.getElementById('edit-quiz-modal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        // FUNGSI KONTROL MODAL LAPORAN HASIL KUIS
        function openQuizReportModal(quizId) {
            const modal = document.getElementById('quiz-report-modal');
            const tbody = document.getElementById('report-modal-tbody');
            tbody.innerHTML = '<tr><td colspan="5" class="py-6 text-center text-gray-400">Memuat data laporan...</td></tr>';
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            fetch("/admin/quiz/" + quizId + "/report-data")
                .then(res => res.json())
                .then(data => {
                    document.getElementById('report-modal-title').innerText = data.quiz_title;
                    document.getElementById('report-modal-total').innerText = data.total_participants + ' Siswa';
                    document.getElementById('report-modal-avg').innerText = data.average_score;

                    tbody.innerHTML = '';
                    if(!data.results || data.results.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="py-6 text-center text-gray-400">Belum ada peserta yang mengerjakan kuis ini.</td></tr>';
                    } else {
                        data.results.forEach((row, idx) => {
                            const tr = document.createElement('tr');
                            tr.className = 'hover:bg-gray-50/70 transition';
                            const scoreBg = row.score >= 70 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800';
                            tr.innerHTML = `
                                <td class="py-2.5 pl-3 font-medium">${idx + 1}</td>
                                <td class="py-2.5 px-3 font-bold text-gray-800">${row.student_name}</td>
                                <td class="py-2.5 px-3"><span class="bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded text-[10px]">${row.class_group}</span></td>
                                <td class="py-2.5 px-3 text-center"><span class="px-2.5 py-0.5 rounded-full text-xs font-black ${scoreBg}">${row.score}</span></td>
                                <td class="py-2.5 px-3 text-center text-gray-400">${row.date}</td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                })
                .catch(() => {
                    tbody.innerHTML = '<tr><td colspan="5" class="py-6 text-center text-red-500 font-semibold">Gagal memuat data laporan.</td></tr>';
                });
        }

        function closeQuizReportModal() {
            const modal = document.getElementById('quiz-report-modal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</body>
</html>