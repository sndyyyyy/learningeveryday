<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Persetujuan Pendaftar</title>
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

    <div class="max-w-6xl mx-auto mt-8 px-4">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800">Manajemen Persetujuan Pendaftar</h2>
                <p class="text-xs text-gray-400 mt-0.5">Daftar calon instansi atau siswa mandiri yang mengajukan akses langganan kuis.</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-xs font-semibold">{{ session('success') }}</div>
            @endif

            <div class="w-full overflow-x-auto border border-gray-100 rounded-lg">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-400 text-xs uppercase bg-gray-50">
                            <th class="py-3 pl-3 w-12">No</th>
                            <th class="py-3 px-3">Nama Lengkap / Instansi</th>
                            <th class="py-3 px-3">Email / Username</th>
                            <th class="py-3 px-3">Paket Langganan</th>
                            <th class="py-3 px-3">Password Mentah</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-xs md:text-sm divide-y divide-gray-100">
                        @forelse($pendingUsers as $index => $pending)
                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="py-3.5 pl-3 font-medium">{{ $index + 1 }}</td>
                                <td class="py-3.5 px-3 font-semibold text-gray-800">{{ $pending->name }}</td>
                                <td class="py-3.5 px-3 text-gray-500">{{ $pending->email }}</td>
                                <td class="py-3.5 px-3">
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[11px] uppercase tracking-wide
                                        {{ str_contains($pending->subscription, 'premium') ? 'bg-purple-50 text-purple-700 border border-purple-100' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                                        {{ str_replace('_', ' ', $pending->subscription) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 font-mono text-gray-400">{{ $pending->raw_password }}</td>
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Form Setujui (Approve) -->
                                        <form id="form-approve-{{ $pending->id }}" action="{{ route('admin.approval.approve', $pending->id) }}" method="POST" class="hidden">
                                            @csrf @method('PUT')
                                        </form>
                                        <button onclick="triggerActionModal('form-approve-{{ $pending->id }}', 'Setujui', 'Apakah Anda yakin ingin menyetujui akun {{ $pending->name }}? Pengguna ini akan langsung mendapatkan hak akses login.', 'bg-indigo-600 hover:bg-indigo-700')" 
                                                class="text-emerald-600 hover:text-emerald-800 bg-emerald-50 px-3 py-1.5 rounded-md text-xs font-bold transition cursor-pointer">
                                            Setujui
                                        </button>

                                        <!-- Form Tolak (Reject) -->
                                        <form id="form-reject-{{ $pending->id }}" action="{{ route('admin.approval.reject', $pending->id) }}" method="POST" class="hidden">
                                            @csrf @method('PUT')
                                        </form>
                                        <button onclick="triggerActionModal('form-reject-{{ $pending->id }}', 'Tolak', 'Apakah Anda yakin ingin menolak pengajuan akun {{ $pending->name }}?', 'bg-red-600 hover:bg-red-700')" 
                                                class="text-red-500 hover:text-red-700 bg-red-50 px-3 py-1.5 rounded-md text-xs font-bold transition cursor-pointer">
                                            Tolak
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">Tidak ada pengajuan pendaftaran akun yang tertunda saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL KONFIRMASI AKSI CUSTOM -->
    <div id="action-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-50 backdrop-blur-xs transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] text-center shadow-2xl animate-fade-in flex flex-col">
            <h3 id="modal-title" class="text-base font-bold text-gray-800 mb-2">Judul Aksi</h3>
            <p id="modal-desc" class="text-xs text-gray-400 mb-5 leading-relaxed">Deskripsi aksi...</p>
            
            <div class="flex flex-row gap-2 w-full">
                <button type="button" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 rounded-xl text-xs cursor-pointer transition" onclick="closeActionModal()">
                    Batal
                </button>
                <button type="button" id="btn-modal-submit" class="w-1/2 text-white font-bold py-2.5 rounded-xl text-xs cursor-pointer transition shadow-xs">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>

    <script>
        let targetedFormId = null;

        function triggerActionModal(formId, title, description, buttonClass) {
            targetedFormId = formId;
            document.getElementById('modal-title').innerText = title + " Pengajuan Akun";
            document.getElementById('modal-desc').innerText = description;
            
            const submitBtn = document.getElementById('btn-modal-submit');
            // Reset class tombol submit lama agar warna tidak bertumpuk
            submitBtn.className = "w-1/2 text-white font-bold py-2.5 rounded-xl text-xs cursor-pointer transition shadow-xs " + buttonClass;

            const modal = document.getElementById('action-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeActionModal() {
            const modal = document.getElementById('action-modal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            targetedFormId = null;
        }

        function executeAction() {
            if (targetedFormId) {
                document.getElementById(targetedFormId).submit();
            }
        }

        // Ikat fungsi eksekusi ke tombol submit modal
        document.getElementById('btn-modal-submit').onclick = executeAction;
    </script>
</body>
</html>