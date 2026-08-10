<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LEMBAR SOAL - {{ $quiz->title }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .page-break { page-break-after: always; }
        }
        body { font-family: 'Times New Roman', Times, serif; }
    </style>
</head>
<body class="bg-gray-100 p-8">

    <!-- BARIS AKSI ATAS (TIDAK TERCETAK) -->
    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center no-print">
        <button onclick="window.history.back()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-xs font-bold transition cursor-pointer">
            &larr; Kembali
        </button>
        <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-xs font-bold transition shadow-md cursor-pointer flex items-center gap-2">
            <span>🖨️ Cetak / Simpan sebagai PDF (Ctrl+P)</span>
        </button>
    </div>

    <!-- KERTAS UJIAN HARDFILE -->
    <div class="max-w-4xl mx-auto bg-white p-10 border border-gray-300 shadow-lg print:border-0 print:shadow-none">
        
        <!-- KOP SURAT INSTANSI / PUSAT -->
        <div class="flex items-center justify-between border-b-4 border-black pb-4 mb-6">
            <div class="flex items-center gap-4">
                <!-- LOGO DYNAMIC: Prioritaskan Logo Sekolah/Pusat, jika kosong gunakan Logo LE default -->
                @if($instansi?->school_logo && file_exists(public_path('storage/' . $instansi->school_logo)))
                    <img src="{{ asset('storage/' . $instansi->school_logo) }}" class="w-16 h-16 object-contain" />
                @else
                    <img src="{{ asset('images/le.jpg') }}" 
                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-16 h-16 bg-indigo-600 text-white font-bold text-xl flex items-center justify-center rounded\'>LE</div>';" 
                         class="w-16 h-16 object-contain rounded" />
                @endif

                <div>
                    <h1 class="text-xl font-bold uppercase tracking-wider text-black">{{ $instansi?->name ?? 'ADMIN PUSAT' }}</h1>
                    <p class="text-xs text-gray-600">NASKAH SOAL UJIAN / PENILAIAN SUMATIF</p>
                </div>
            </div>
            <div class="text-right text-xs border-l-2 border-black pl-4">
                <p><strong>Mata Ujian:</strong> {{ $quiz->title }}</p>
                <p><strong>Kelas/Target:</strong> {{ $quiz->class_group ?? 'Umum / Semua Kelas' }}</p>
                <p><strong>Total Soal:</strong> {{ $mcQuestions->count() + $essayQuestions->count() }} Nomor</p>
            </div>
        </div>

        <!-- ISIAN IDENTITAS SISWA (HARDFILE) -->
        <div class="border border-black p-3 mb-6 grid grid-cols-2 gap-4 text-xs font-semibold">
            <div>
                <p class="mb-2">NAMA SISWA : ................................................................</p>
                <p>KELAS / NO   : ................................................................</p>
            </div>
            <div>
                <p class="mb-2">TANGGAL UJIAN : ................................................................</p>
                <p>PARAF SISWA  : ................................................................</p>
            </div>
        </div>

        <!-- PETUNJUK UMUM -->
        <div class="text-xs mb-6 bg-gray-50 print:bg-transparent p-3 border border-gray-200 print:border-black">
            <p class="font-bold mb-1">PETUNJUK PENGERJAAN:</p>
            <ol class="list-decimal pl-4 space-y-0.5">
                <li>Tuliskan nama dan identitas lengkap Anda pada kolom yang telah disediakan.</li>
                <li>Bacalah setiap butir soal dengan teliti sebelum memberikan jawaban.</li>
                <li>Untuk soal Pilihan Ganda, berilah tanda silang (X) pada huruf jawaban yang Anda anggap paling benar.</li>
                <li>Untuk soal Isian/Essay, tuliskan jawaban secara singkat dan jelas pada tempat yang disediakan.</li>
            </ol>
        </div>

        @php $noUrut = 1; @endphp

        <!-- 🔴 BAGIAN 1: SOAL PILIHAN GANDA (SELALU DI ATAS) -->
        @if($mcQuestions->isNotEmpty())
            <div class="mb-6">
                <h3 class="font-bold text-sm uppercase mb-3 border-b border-black pb-1">I. PILIHAN GANDA</h3>
                <div class="space-y-5 text-sm text-black">
                    @foreach($mcQuestions as $q)
                        <div class="space-y-1">
                            <div class="flex items-start gap-2">
                                <span class="font-bold">{{ $noUrut++ }}.</span>
                                <div class="flex-1">
                                    <p class="leading-relaxed">{!! nl2br(e($q->question_text)) !!}</p>

                                    @if($q->image)
                                        <div class="my-2">
                                            <img src="/storage/{{ $q->image }}" class="max-h-48 object-contain rounded border border-gray-300" />
                                        </div>
                                    @endif

                                    @if(is_array($q->options))
                                        <div class="grid grid-cols-2 gap-x-6 gap-y-1 mt-2 text-xs pl-2">
                                            <p>A. {{ $q->options['A'] ?? '' }}</p>
                                            <p>B. {{ $q->options['B'] ?? '' }}</p>
                                            <p>C. {{ $q->options['C'] ?? '' }}</p>
                                            <p>D. {{ $q->options['D'] ?? '' }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 🟡 BAGIAN 2: SOAL ESSAY / ISIAN (SELALU DI BOWER) -->
        @if($essayQuestions->isNotEmpty())
            <div class="mb-6">
                <h3 class="font-bold text-sm uppercase mb-3 border-b border-black pb-1">II. ISIAN / ESSAY</h3>
                <div class="space-y-5 text-sm text-black">
                    @foreach($essayQuestions as $q)
                        <div class="space-y-1">
                            <div class="flex items-start gap-2">
                                <span class="font-bold">{{ $noUrut++ }}.</span>
                                <div class="flex-1">
                                    <p class="leading-relaxed">{!! nl2br(e($q->question_text)) !!}</p>

                                    @if($q->image)
                                        <div class="my-2">
                                            <img src="/storage/{{ $q->image }}" class="max-h-48 object-contain rounded border border-gray-300" />
                                        </div>
                                    @endif

                                    <!-- Garis Jawaban Isian Hardfile -->
                                    <div class="mt-3 border-b border-dotted border-gray-500 h-10 w-full"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

</body>
</html>