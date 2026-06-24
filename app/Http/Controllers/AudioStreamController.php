<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AudioStreamController extends Controller
{
    public function stream(Request $request)
    {
        // Ambil path file dari query string agar karakter '/' tidak merusak routing Laravel
        $filePath = $request->query('path');

        if (!$filePath) {
            abort(400, 'Parameter path tidak ditentukan.');
        }

        // Cari file di folder storage/app/public/
        $path = storage_path('app/public/' . $filePath);

        // Jika file tidak ditemukan secara fisik, kembalikan response 404 kosong (tidak crash)
        if (!file_exists($path) || is_dir($path)) {
            abort(404, 'File audio tidak ditemukan di server.');
        }

        // Buat response streaming
        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', mime_content_type($path));
        $response->headers->set('Accept-Ranges', 'bytes');

        return $response;
    }
}