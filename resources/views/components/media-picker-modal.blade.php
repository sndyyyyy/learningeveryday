<!-- MODAL GALERI MEDIA INTERNAL -->
<div id="media-picker-modal" class="fixed inset-0 bg-black/60 hidden justify-center items-center z-[100] backdrop-blur-xs">
    <div class="bg-white p-5 rounded-2xl max-w-4xl w-[92%] shadow-2xl flex flex-col max-h-[88vh] animate-fade-in">
        
        <!-- HEADER MODAL -->
        <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-3">
            <div>
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span>📁</span> <span>Galeri Media Internal</span>
                </h3>
                <p class="text-[11px] text-gray-400">Pilih file gambar/audio dari perpustakaan atau buat folder baru.</p>
            </div>
            <button onclick="closeMediaPickerModal()" class="text-gray-400 hover:text-gray-600 font-bold text-2xl cursor-pointer">&times;</button>
        </div>

        <!-- TOOLBAR & BREADCRUMB -->
        <div class="flex flex-wrap justify-between items-center gap-2 mb-3 bg-gray-50 p-2.5 rounded-xl border border-gray-200/80 text-xs">
            <div class="flex items-center gap-2 font-bold text-gray-700">
                <button id="media-btn-back" onclick="navigateMediaParent()" class="hidden bg-white hover:bg-gray-100 text-indigo-600 border border-gray-200 px-2.5 py-1 rounded-lg transition shadow-2xs cursor-pointer">
                    &larr; Kembali
                </button>
                <span id="media-current-path" class="text-indigo-600 font-bold">Root Folder</span>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="promptCreateFolder()" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-3 py-1.5 rounded-lg border border-indigo-200 transition cursor-pointer flex items-center gap-1">
                    <span>➕ Folder Baru</span>
                </button>
                <label class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-lg transition shadow-2xs cursor-pointer flex items-center gap-1">
                    <span>📤 Upload File</span>
                    <input type="file" id="media-upload-input" onchange="handleMediaUpload(this)" class="hidden" accept="image/*,audio/*" multiple>
                </label>
            </div>
        </div>

        <!-- CONTAINER CONTENT -->
        <div class="overflow-y-auto flex-1 min-h-[300px] border border-gray-100 rounded-xl p-3 bg-gray-50/50">
            <div id="media-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                <!-- Disuntikkan via JS -->
            </div>
        </div>

        <!-- FOOTER MODAL -->
        <div class="pt-3 border-t border-gray-100 flex justify-between items-center text-xs">
            <span class="text-gray-400 italic">Klik pada file untuk memilihnya.</span>
            <button onclick="closeMediaPickerModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-4 py-2 rounded-xl transition cursor-pointer">
                Batal
            </button>
        </div>
    </div>
</div>

<!-- 🔐 MODAL OTORITAS HAPUS FOLDER GALERI (POPUP MODAL PICKER) -->
<div id="picker-folder-security-gate-modal" class="fixed inset-0 bg-black/70 hidden justify-center items-center z-[200] backdrop-blur-sm transition-all duration-300">
    <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] text-center shadow-2xl animate-fade-in flex flex-col">
        <div class="text-3xl mb-2">🔐</div>
        <h3 class="text-base font-bold text-gray-800 mb-1">Otorisasi Hapus Folder</h3>
        <p class="text-xs text-gray-500 mb-4">Masukkan kata sandi otoritas untuk menghapus folder <span id="picker-target-folder-name" class="text-red-600 font-bold"></span> beserta isinya.</p>
        
        <div class="mb-4 text-left">
            <input type="password" id="picker-folder-gate-auth-input" placeholder="Masukkan Sandi Otoritas..." class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-indigo-500 font-medium">
            <p id="picker-folder-gate-error-msg" class="hidden text-[11px] text-red-500 font-semibold mt-1">⚠️ Sandi otoritas salah, akses ditolak!</p>
        </div>
        
        <div class="flex flex-row gap-2 w-full">
            <button type="button" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2 rounded-xl text-xs cursor-pointer transition" onclick="closePickerFolderSecurityGateModal()">
                Batal
            </button>
            <button type="button" class="w-1/2 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-xl text-xs cursor-pointer transition shadow-xs" onclick="verifyAndExecutePickerFolderDelete()">
                Konfirmasi Hapus
            </button>
        </div>
    </div>
</div>

<script>
    const PICKER_MASTER_PASSWORD_KEY = "adminrahasia"; // 👈 Sandi Otoritas
    let pickerTargetFolderIdToDelete = null;

    let currentFolderId = null;
    let parentFolderId = null;
    let targetInputId = null;

    function openMediaPickerModal(inputId) {
        targetInputId = inputId;
        currentFolderId = null;
        
        const modal = document.getElementById('media-picker-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        loadMediaData();
    }

    function closeMediaPickerModal() {
        const modal = document.getElementById('media-picker-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function loadMediaData() {
        const grid = document.getElementById('media-grid');
        grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-400 font-medium text-xs">Memuat galeri media...</div>';

        let url = `/media-manager/data?folder_id=${currentFolderId || ''}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                grid.innerHTML = '';
                
                const btnBack = document.getElementById('media-btn-back');
                const pathText = document.getElementById('media-current-path');

                if (data.current_folder) {
                    parentFolderId = data.current_folder.parent_id;
                    btnBack.classList.remove('hidden');
                    pathText.innerText = `📁 ${data.current_folder.name}`;
                } else {
                    parentFolderId = null;
                    btnBack.classList.add('hidden');
                    pathText.innerText = '📁 Root Folder';
                }

                if (data.folders.length === 0 && data.files.length === 0) {
                    grid.innerHTML = '<div class="col-span-full text-center py-12 text-gray-400 text-xs">Folder ini masih kosong. Upload file atau buat folder baru.</div>';
                    return;
                }

                // Render Folder
                data.folders.forEach(f => {
                    const card = document.createElement('div');
                    card.className = "bg-white p-3 rounded-xl border border-gray-200 hover:border-indigo-400 hover:shadow-md transition cursor-pointer flex flex-col justify-between group relative";
                    card.innerHTML = `
                        <div onclick="openFolder(${f.id})" class="flex items-center gap-2">
                            <span class="text-2xl">📁</span>
                            <span class="font-bold text-gray-800 text-xs line-clamp-1">${f.name}</span>
                        </div>
                        <div class="flex justify-end gap-1 mt-2 opacity-0 group-hover:opacity-100 transition pt-1 border-t border-gray-100">
                            <button onclick="event.stopPropagation(); promptRenameFolder(${f.id}, '${f.name}')" class="text-[10px] text-amber-600 hover:underline font-bold cursor-pointer">Rename</button>
                            <span class="text-gray-300 text-[10px]">•</span>
                            <button onclick="event.stopPropagation(); openPickerFolderSecurityGate(${f.id}, '${f.name}')" class="text-[10px] text-red-500 hover:underline font-bold cursor-pointer">Hapus</button>
                        </div>
                    `;
                    grid.appendChild(card);
                });

                // Render File
                data.files.forEach(file => {
                    const card = document.createElement('div');
                    card.className = "bg-white p-2 rounded-xl border border-gray-200 hover:border-emerald-500 hover:shadow-md transition cursor-pointer flex flex-col justify-between group relative";
                    
                    let preview = '';
                    if (file.file_type === 'image') {
                        preview = `<img src="${file.url}" class="h-20 w-full object-contain rounded-lg bg-gray-50 mb-1.5" />`;
                    } else {
                        preview = `<div class="h-20 w-full bg-amber-50 rounded-lg flex items-center justify-center text-3xl mb-1.5">🎵</div>`;
                    }

                    card.innerHTML = `
                        <div onclick="selectMediaFile('${file.file_path}')">
                            ${preview}
                            <p class="font-bold text-gray-800 text-[11px] line-clamp-1">${file.file_name}</p>
                            <span class="text-[9px] text-gray-400 uppercase font-semibold">${file.file_type}</span>
                        </div>
                        <div class="flex justify-between items-center mt-2 opacity-0 group-hover:opacity-100 transition pt-1 border-t border-gray-100">
                            <button onclick="event.stopPropagation(); selectMediaFile('${file.file_path}')" class="text-[10px] text-emerald-600 font-bold hover:underline cursor-pointer">✓ Pilih</button>
                            <button onclick="event.stopPropagation(); deleteFile(${file.id}, '${file.file_name}')" class="text-[10px] text-red-500 font-bold hover:underline cursor-pointer">Hapus</button>
                        </div>
                    `;
                    grid.appendChild(card);
                });
            });
    }

    function openFolder(folderId) {
        currentFolderId = folderId;
        loadMediaData();
    }

    function navigateMediaParent() {
        currentFolderId = parentFolderId;
        loadMediaData();
    }

    function promptCreateFolder() {
        const name = prompt("Masukkan nama folder baru:");
        if (name) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('name', name);
            if(currentFolderId) formData.append('parent_id', currentFolderId);

            fetch("{{ route('media.folder.store') }}", { method: 'POST', body: formData })
                .then(res => res.json())
                .then(() => loadMediaData());
        }
    }

    function promptRenameFolder(id, currentName) {
        const name = prompt("Ubah nama folder:", currentName);
        if (name && name !== currentName) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PUT');
            formData.append('name', name);

            fetch(`/media-manager/folder/${id}`, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(() => loadMediaData());
        }
    }

    // 🔐 OTORITAS HAPUS FOLDER DI MODAL PICKER
    function openPickerFolderSecurityGate(folderId, folderName) {
        pickerTargetFolderIdToDelete = folderId;
        document.getElementById('picker-target-folder-name').innerText = folderName;
        document.getElementById('picker-folder-gate-auth-input').value = '';
        document.getElementById('picker-folder-gate-error-msg').classList.add('hidden');

        const modal = document.getElementById('picker-folder-security-gate-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => { document.getElementById('picker-folder-gate-auth-input').focus(); }, 50);
    }

    function closePickerFolderSecurityGateModal() {
        pickerTargetFolderIdToDelete = null;
        const modal = document.getElementById('picker-folder-security-gate-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function verifyAndExecutePickerFolderDelete() {
        const inputVal = document.getElementById('picker-folder-gate-auth-input').value;
        const errorMsg = document.getElementById('picker-folder-gate-error-msg');

        if (inputVal === PICKER_MASTER_PASSWORD_KEY) {
            if (pickerTargetFolderIdToDelete) {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'DELETE');

                fetch(`/media-manager/folder/${pickerTargetFolderIdToDelete}`, { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(() => {
                        closePickerFolderSecurityGateModal();
                        loadMediaData();
                    });
            }
        } else {
            errorMsg.classList.remove('hidden');
        }
    }

    document.getElementById('picker-folder-gate-auth-input').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            verifyAndExecutePickerFolderDelete();
        }
    });

    function handleMediaUpload(input) {
        if (input.files && input.files.length > 0) {
            const files = input.files;
            let uploadedCount = 0;
            let totalFiles = files.length;

            const grid = document.getElementById('media-grid');
            grid.innerHTML = `<div class="col-span-full text-center py-12 text-indigo-600 font-bold text-xs animate-pulse">Mengunggah ${totalFiles} file sekaligus...</div>`;

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
                            loadMediaData();
                        }
                    })
                    .catch(err => {
                        uploadedCount++;
                        if (uploadedCount === totalFiles) {
                            input.value = '';
                            loadMediaData();
                        }
                    });
            });
        }
    }

    function deleteFile(id, name) {
        if (confirm(`Yakin ingin menghapus file "${name}"?`)) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');

            fetch(`/media-manager/file/${id}`, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(() => loadMediaData());
        }
    }

    function selectMediaFile(filePath) {
        if (targetInputId) {
            const inputTarget = document.getElementById(targetInputId);
            if (inputTarget) {
                inputTarget.value = filePath;
                const event = new Event('change', { bubbles: true });
                inputTarget.dispatchEvent(event);
            }
        }
        closeMediaPickerModal();
    }
</script>