<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use App\Models\MediaFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaManagerController extends Controller
{
    // Mengambil konten folder & file (Satu Galeri Terpadu)

    public function index()
    {
        return view('admin.media.index');
    }
    
    public function getMedia(Request $request)
    {
        $userId = auth()->id();
        $folderId = $request->query('folder_id'); // null jika di root folder

        // Ambil seluruh sub-folder
        $folders = MediaFolder::where('user_id', $userId)
            ->where('parent_id', $folderId)
            ->oldest('name')
            ->get();

        // Ambil seluruh file (Gambar & Audio ditampilkan bersamaan dalam folder)
        $files = MediaFile::where('user_id', $userId)
            ->where('folder_id', $folderId)
            ->latest()
            ->get()
            ->map(function ($f) {
                return [
                    'id' => $f->id,
                    'file_name' => $f->file_name,
                    'file_path' => $f->file_path,
                    'url' => asset('storage/' . $f->file_path),
                    'file_type' => $f->file_type,
                    'created_at' => $f->created_at->format('d M Y'),
                ];
            });

        $currentFolder = $folderId ? MediaFolder::find($folderId) : null;

        return response()->json([
            'current_folder' => $currentFolder,
            'folders' => $folders,
            'files' => $files,
        ]);
    }

    public function storeFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:media_folders,id',
        ]);

        $folder = MediaFolder::create([
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'name' => $request->name,
        ]);

        return response()->json(['success' => true, 'folder' => $folder]);
    }

    public function updateFolder(Request $request, MediaFolder $folder)
    {
        if ($folder->user_id !== auth()->id()) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $request->validate(['name' => 'required|string|max:255']);
        $folder->update(['name' => $request->name]);

        return response()->json(['success' => true]);
    }

    public function destroyFolder(MediaFolder $folder)
    {
        if ($folder->user_id !== auth()->id()) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $files = MediaFile::where('folder_id', $folder->id)->get();
        foreach ($files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        }

        $folder->delete();
        return response()->json(['success' => true]);
    }

    // Upload File ke Galeri (Satu Pintu untuk Gambar & Sound)
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,mp3,wav,ogg,m4a,opus,aac|max:10240',
            'folder_id' => 'nullable|exists:media_folders,id',
        ]);

        $uploadedFile = $request->file('file');
        $ext = strtolower($uploadedFile->getClientOriginalExtension());
        
        // Deteksi Tipe Media Berdasarkan Ekstensi File
        $audioExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'opus', 'aac'];
        $type = in_array($ext, $audioExtensions) ? 'audio' : 'image';

        $path = $uploadedFile->store('media/' . $type . 's', 'public');

        $fileRecord = MediaFile::create([
            'user_id' => auth()->id(),
            'folder_id' => $request->folder_id,
            'file_name' => $uploadedFile->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $type,
            'file_size' => $uploadedFile->getSize(),
        ]);

        return response()->json(['success' => true, 'file' => $fileRecord, 'url' => asset('storage/' . $path)]);
    }

    public function destroyFile(MediaFile $file)
    {
        if ($file->user_id !== auth()->id()) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();
        return response()->json(['success' => true]);
    }
}