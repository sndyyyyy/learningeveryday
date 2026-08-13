<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Media Internal - Management Storage</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.25s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-100 font-sans pb-16">

    @include('layouts.navbar')

    <!-- BREADCRUMB HEADER -->
    <div class="bg-white border-t border-gray-100 py-3 px-4 md:px-8 flex items-center shadow-xs">
        <a href="{{ route('admin.dashboard.utama') }}" class="text-xs md:text-sm text-gray-500 hover:text-indigo-600 font-semibold transition">
            &larr; Kembali ke Beranda
        </a>
    </div>

    <!-- CONTAINER UTAMA -->
    <div class="max-w-6xl mx-auto mt-6 px-4">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6 space-y-5">
            
            <!-- TOOLBAR ATAS (SEARCH & KONTROL AKSI) -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200/80">
                
                <!-- BREADCRUMB FOLDER CURRENT -->
                <div class="flex items-center gap-2">
                    <button id="page-btn-back" onclick="navigateParentFolder()" class="hidden bg-white hover:bg-gray-100 text-indigo-600 font-bold border border-gray-200 px-3 py-1.5 rounded-lg text-xs transition shadow-2xs cursor-pointer">
                        &larr; Kembali
                    </button>
                    <div>
                        <span id="page-current-path" class="text-sm font-black text-indigo-600 flex items-center gap-1">
                            <span>📁</span> Root Folder
                        </span>
                    </div>
                </div>

                <!-- TOMBOL-TOMBOL AKSI -->
                <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                    <!-- Search Input -->
                    <input type="text" id="page-search-input" onkeyup="filterMediaDisplay()" placeholder="Cari nama file/folder..." class="px-3 py-1.5 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-indigo-500 bg-white font-medium w-full sm:w-48">

                    <!-- Buat Folder Baru -->
                    <button onclick="promptCreateFolderPage()" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-3.5 py-1.5 rounded-lg border border-indigo-200 transition text-xs cursor-pointer flex items-center gap-1 whitespace-nowrap">
                        <span>➕ Folder Baru</span>
                    </button>

                    <!-- Upload File Baru (Support Multiple Selection) -->
                    <label class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-1.5 rounded-lg transition shadow-2xs text-xs cursor-pointer flex items-center gap-1.5 whitespace-nowrap">
                        <span>Upload File</span>
                        <input type="file" id="page-upload-input" onchange="handlePageFileUpload(this)" class="hidden" accept="image/*,audio/*" multiple>
                    </label>
                </div>
            </div>

            <!-- GRID AREA PENYIMPANAN -->
            <div class="min-h-[400px] border border-gray-100 rounded-xl p-4 bg-gray-50/40">
                <div id="page-media-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                    <!-- Disuntikkan via JS -->
                </div>
            </div>

        </div>
    </div>

    <!-- 🌟 MODAL PREVIEW FULLSCREEN POPUP -->
    <div id="preview-fullscreen-modal" class="fixed inset-0 bg-black/80 hidden justify-center items-center z-[150] backdrop-blur-md p-4 animate-fade-in">
        <button onclick="closePreviewModal()" class="absolute top-4 right-6 text-white hover:text-red-400 text-3xl font-black cursor-pointer z-[160] transition">
            &times; <span class="text-xs font-semibold align-middle hidden sm:inline">Tutup (ESC)</span>
        </button>

        <div class="max-w-4xl w-full h-full flex flex-col items-center justify-center relative p-2">
            <h4 id="preview-title" class="text-white font-bold text-xs md:text-sm mb-4 text-center line-clamp-1 bg-black/50 px-4 py-1.5 rounded-full border border-white/20">
                Nama File
            </h4>

            <div id="preview-image-container" class="hidden w-full h-[75vh] items-center justify-center">
                <img id="preview-img-element" src="" class="max-h-full max-w-full object-contain rounded-xl shadow-2xl border border-white/10" />
            </div>

            <div id="preview-audio-container" class="hidden w-full max-w-md bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-2xl flex-col items-center justify-center space-y-4 text-center shadow-2xl">
                <div class="text-6xl animate-bounce">🎵</div>
                <audio id="preview-audio-element" src="" controls class="w-full outline-none"></audio>
            </div>
        </div>
    </div>

    <!-- 🔐 MODAL OTORITAS HAPUS FOLDER GALERI -->
    <div id="folder-security-gate-modal" class="fixed inset-0 bg-black/60 hidden justify-center items-center z-[170] backdrop-blur-sm transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] text-center shadow-2xl animate-fade-in flex flex-col">
            <div class="text-3xl mb-2">🔐</div>
            <h3 class="text-base font-bold text-gray-800 mb-1">Otorisasi Hapus Folder</h3>
            <p class="text-xs text-gray-500 mb-4">Masukkan kata sandi otoritas untuk menghapus folder <span id="target-folder-name" class="text-red-600 font-bold"></span> beserta seluruh isinya.</p>
            
            <div class="mb-4 text-left">
                <input type="password" id="folder-gate-auth-input" placeholder="Masukkan Sandi Otoritas..." class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 font-medium">
                <p id="folder-gate-error-msg" class="hidden text-[11px] text-red-500 font-semibold mt-1">⚠️ Sandi otoritas salah, akses ditolak!</p>
            </div>
            
            <div class="flex flex-row gap-2 w-full">
                <button type="button" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 rounded-xl text-xs cursor-pointer transition" onclick="closeFolderSecurityGateModal()">
                    Batal
                </button>
                <button type="button" class="w-1/2 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-xl text-xs cursor-pointer transition shadow-xs" onclick="verifyAndExecuteFolderDelete()">
                    Konfirmasi Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- ENGINE JAVASCRIPT HALAMAN GALERI -->
    <script>
        const MASTER_PASSWORD_KEY = "adminrahasia"; // 👈 Sesuai password otorisasi di Kelola Peserta
        let targetFolderIdToDelete = null;

        let currentFolderId = null;
        let parentFolderId = null;
        let allCurrentFiles = [];
        let allCurrentFolders = [];

        function loadPageMediaData() {
            const grid = document.getElementById('page-media-grid');
            grid.innerHTML = '<div class="col-span-full text-center py-16 text-gray-400 font-medium text-xs">Memuat galeri media...</div>';

            fetch(`/media-manager/data?folder_id=${currentFolderId || ''}`)
                .then(res => res.json())
                .then(data => {
                    allCurrentFolders = data.folders;
                    allCurrentFiles = data.files;

                    const btnBack = document.getElementById('page-btn-back');
                    const pathText = document.getElementById('page-current-path');

                    if (data.current_folder) {
                        parentFolderId = data.current_folder.parent_id;
                        btnBack.classList.remove('hidden');
                        pathText.innerHTML = `<span>📁</span> Folder: ${data.current_folder.name}`;
                    } else {
                        parentFolderId = null;
                        btnBack.classList.add('hidden');
                        pathText.innerHTML = `<span>📁</span> Root Folder`;
                    }

                    renderGridItems(allCurrentFolders, allCurrentFiles);
                });
        }

        function renderGridItems(folders, files) {
            const grid = document.getElementById('page-media-grid');
            grid.innerHTML = '';

            if (folders.length === 0 && files.length === 0) {
                grid.innerHTML = '<div class="col-span-full text-center py-16 text-gray-400 text-xs">Folder ini masih kosong. Silakan upload file gambar/sound atau buat sub-folder baru.</div>';
                return;
            }

            // Render Sub-Folder
            folders.forEach(f => {
                const card = document.createElement('div');
                card.className = "bg-white p-3.5 rounded-xl border border-gray-200 hover:border-indigo-500 hover:shadow-md transition cursor-pointer flex flex-col justify-between group relative animate-fade-in";
                card.innerHTML = `
                    <div onclick="openPageFolder(${f.id})" class="flex items-center gap-2.5">
                        <span class="text-3xl">📁</span>
                        <span class="font-bold text-gray-800 text-xs line-clamp-1">${f.name}</span>
                    </div>
                    <div class="flex justify-end gap-2 mt-3 opacity-0 group-hover:opacity-100 transition pt-2 border-t border-gray-100">
                        <button onclick="event.stopPropagation(); promptRenameFolderPage(${f.id}, '${f.name}')" class="text-[10px] text-amber-600 hover:underline font-bold cursor-pointer">Rename</button>
                        <span class="text-gray-300 text-[10px]">•</span>
                        <button onclick="event.stopPropagation(); openFolderSecurityGate(${f.id}, '${f.name}')" class="text-[10px] text-red-500 hover:underline font-bold cursor-pointer">Hapus</button>
                    </div>
                `;
                grid.appendChild(card);
            });

            // Render Files
            files.forEach(file => {
                const card = document.createElement('div');
                card.className = "bg-white p-3 rounded-xl border border-gray-200 hover:border-indigo-500 hover:shadow-md transition flex flex-col justify-between group relative animate-fade-in";
                
                let preview = '';
                if (file.file_type === 'image') {
                    preview = `
                        <div class="h-28 w-full bg-gray-50 rounded-lg overflow-hidden mb-2 border border-gray-100 flex items-center justify-center cursor-pointer" onclick="openPreviewModal('${file.url}', '${file.file_type}', '${file.file_name}')">
                            <img src="${file.url}" class="h-full w-full object-contain hover:scale-105 transition duration-300" />
                        </div>
                    `;
                } else {
                    preview = `
                        <div class="h-28 w-full bg-amber-50/70 rounded-lg border border-amber-100/80 flex flex-col items-center justify-center mb-2 p-2 text-center cursor-pointer" onclick="openPreviewModal('${file.url}', '${file.file_type}', '${file.file_name}')">
                            <span class="text-3xl mb-1">🎵</span>
                            <span class="text-[10px] text-amber-700 font-bold bg-amber-100 px-2 py-0.5 rounded-full">Klik Pratinjau</span>
                        </div>
                    `;
                }

                card.innerHTML = `
                    <div>
                        ${preview}
                        <p class="font-bold text-gray-800 text-xs line-clamp-1" title="${file.file_name}">${file.file_name}</p>
                        <div class="flex justify-between items-center mt-0.5">
                            <span class="text-[9px] text-gray-400 uppercase font-semibold">${file.file_type}</span>
                            <span class="text-[9px] text-gray-400">${file.created_at}</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-2.5 opacity-0 group-hover:opacity-100 transition pt-2 border-t border-gray-100">
                        <button onclick="openPreviewModal('${file.url}', '${file.file_type}', '${file.file_name}')" class="text-[10px] text-indigo-600 font-bold hover:underline cursor-pointer">👁️ Pratinjau</button>
                        <button onclick="deleteFilePage(${file.id}, '${file.file_name}')" class="text-[10px] text-red-500 font-bold hover:underline cursor-pointer">🗑️ Hapus</button>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        function openPageFolder(folderId) {
            currentFolderId = folderId;
            loadPageMediaData();
        }

        function navigateParentFolder() {
            currentFolderId = parentFolderId;
            loadPageMediaData();
        }

        function promptCreateFolderPage() {
            const name = prompt("Masukkan nama folder baru:");
            if (name) {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('name', name);
                if(currentFolderId) formData.append('parent_id', currentFolderId);

                fetch("{{ route('media.folder.store') }}", { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(() => loadPageMediaData());
            }
        }

        function promptRenameFolderPage(id, currentName) {
            const name = prompt("Ubah nama folder:", currentName);
            if (name && name !== currentName) {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PUT');
                formData.append('name', name);

                fetch(`/media-manager/folder/${id}`, { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(() => loadPageMediaData());
            }
        }

        // 🔐 OPEN MODAL OTORITAS PENGHAPUSAN FOLDER
        function openFolderSecurityGate(folderId, folderName) {
            targetFolderIdToDelete = folderId;
            document.getElementById('target-folder-name').innerText = folderName;
            document.getElementById('folder-gate-auth-input').value = '';
            document.getElementById('folder-gate-error-msg').classList.add('hidden');

            const modal = document.getElementById('folder-security-gate-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => { document.getElementById('folder-gate-auth-input').focus(); }, 50);
        }

        function closeFolderSecurityGateModal() {
            targetFolderIdToDelete = null;
            const modal = document.getElementById('folder-security-gate-modal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        function verifyAndExecuteFolderDelete() {
            const inputVal = document.getElementById('folder-gate-auth-input').value;
            const errorMsg = document.getElementById('folder-gate-error-msg');

            if (inputVal === MASTER_PASSWORD_KEY) {
                if (targetFolderIdToDelete) {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'DELETE');

                    fetch(`/media-manager/folder/${targetFolderIdToDelete}`, { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(() => {
                            closeFolderSecurityGateModal();
                            loadPageMediaData();
                        });
                }
            } else {
                errorMsg.classList.remove('hidden');
            }
        }

        document.getElementById('folder-gate-auth-input').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                verifyAndExecuteFolderDelete();
            }
        });

        function handlePageFileUpload(input) {
            if (input.files && input.files.length > 0) {
                const files = input.files;
                let uploadedCount = 0;
                let totalFiles = files.length;

                const grid = document.getElementById('page-media-grid');
                grid.innerHTML = `<div class="col-span-full text-center py-16 text-indigo-600 font-bold text-xs animate-pulse">Mengunggah ${totalFiles} file sekaligus ke folder ini...</div>`;

                Array.from(files).forEach(file => {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('file', file);
                    if (currentFolderId) formData.append('folder_id', currentFolderId);

                    fetch("{{ route('media.upload') }}", { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(res => {
                            uploadedCount++;
                            if (uploadedCount === totalFiles) {
                                input.value = '';
                                loadPageMediaData();
                            }
                        })
                        .catch(err => {
                            uploadedCount++;
                            if (uploadedCount === totalFiles) {
                                input.value = '';
                                loadPageMediaData();
                            }
                        });
                });
            }
        }

        function deleteFilePage(id, name) {
            if (confirm(`Yakin ingin menghapus file "${name}" dari storage?`)) {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'DELETE');

                fetch(`/media-manager/file/${id}`, { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(() => loadPageMediaData());
            }
        }

        function filterMediaDisplay() {
            const query = document.getElementById('page-search-input').value.toLowerCase();
            
            const filteredFolders = allCurrentFolders.filter(f => f.name.toLowerCase().includes(query));
            const filteredFiles = allCurrentFiles.filter(f => f.file_name.toLowerCase().includes(query));

            renderGridItems(filteredFolders, filteredFiles);
        }

        function openPreviewModal(url, type, fileName) {
            const modal = document.getElementById('preview-fullscreen-modal');
            const title = document.getElementById('preview-title');
            const imgBox = document.getElementById('preview-image-container');
            const audioBox = document.getElementById('preview-audio-container');
            const imgEl = document.getElementById('preview-img-element');
            const audioEl = document.getElementById('preview-audio-element');

            title.innerText = fileName;

            if (type === 'image') {
                imgEl.src = url;
                imgBox.classList.remove('hidden');
                imgBox.classList.add('flex');
                audioBox.classList.remove('flex');
                audioBox.classList.add('hidden');
            } else {
                audioEl.src = url;
                audioBox.classList.remove('hidden');
                audioBox.classList.add('flex');
                imgBox.classList.remove('flex');
                imgBox.classList.add('hidden');
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePreviewModal() {
            const modal = document.getElementById('preview-fullscreen-modal');
            const audioEl = document.getElementById('preview-audio-element');
            const imgEl = document.getElementById('preview-img-element');

            audioEl.pause();
            audioEl.src = '';
            imgEl.src = '';

            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePreviewModal();
            }
        });

        document.addEventListener("DOMContentLoaded", () => {
            loadPageMediaData();
        });
    </script>
</body>
</html>