<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <title>NASKAH SOAL - {{ $quiz->title }}</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.3; }
        table { width: 100%; border-collapse: collapse; }
        .kop-table { border-bottom: 3px solid #000; margin-bottom: 15px; }
        .box-identitas { border: 1px solid #000; padding: 8px; margin-bottom: 15px; font-size: 10pt; }
        .box-petunjuk { border: 1px solid #ccc; background-color: #f9f9f9; padding: 8px; font-size: 10pt; margin-bottom: 15px; }
        .title-section { font-weight: bold; font-size: 11pt; border-bottom: 1px solid #000; margin-top: 15px; margin-bottom: 10px; }
        .question-item { margin-bottom: 12px; }
        .options-table { width: 100%; font-size: 11pt; margin-top: 5px; }
        .options-table td { width: 50%; padding-left: 15px; vertical-align: top; }
        .essay-line { border-bottom: 1px商店 dotted #666; height: 30px; width: 100%; margin-top: 10px; }
    </style>
</head>
<body>

    <!-- KOP SURAT INSTANSI -->
    <table class="kop-table">
        <tr>
            <td style="width: 15%; text-align: center; vertical-align: middle;">
                @if($instansi?->school_logo && file_exists(public_path('storage/' . $instansi->school_logo)))
                    <img src="{{ public_path('storage/' . $instansi->school_logo) }}" width="60" height="60" />
                @else
                    <div style="font-weight: bold; font-size: 18pt; color: #4f46e5;">LE</div>
                @endif
            </td>
            <td style="width: 55%; text-align: left; vertical-align: middle;">
                <h2 style="margin:0; font-size: 14pt; text-transform: uppercase;">{{ $instansi?->name ?? 'ADMIN PUSAT' }}</h2>
                <p style="margin:0; font-size: 10pt; color: #555;">NASKAH SOAL UJIAN / PENILAIAN SUMATIF</p>
            </td>
            <td style="width: 30%; text-align: right; vertical-align: middle; font-size: 9pt; border-left: 1px solid #000; padding-left: 10px;">
                <b>Mata Ujian:</b> {{ $quiz->title }}<br>
                <b>Kelas:</b> {{ $quiz->class_group ?? 'Umum / Semua Kelas' }}<br>
                <b>Total Soal:</b> {{ $mcQuestions->count() + $essayQuestions->count() }} Nomor
            </td>
        </tr>
    </table>

    <!-- ISIAN IDENTITAS SISWA HARDFILE -->
    <div class="box-identitas">
        <table>
            <tr>
                <td><b>NAMA SISWA :</b> ................................................................</td>
                <td><b>TANGGAL UJIAN :</b> ................................................................</td>
            </tr>
            <tr>
                <td><b>KELAS / NO :</b> ................................................................</td>
                <td><b>PARAF SISWA :</b> ................................................................</td>
            </tr>
        </table>
    </div>

    <!-- PETUNJUK PENGERJAAN -->
    <div class="box-petunjuk">
        <b>PETUNJUK PENGERJAAN:</b>
        <ol style="margin-top: 3px; margin-bottom: 0; padding-left: 20px;">
            <li>Tuliskan nama dan identitas lengkap Anda pada kolom yang telah disediakan.</li>
            <li>Bacalah setiap butir soal dengan teliti sebelum memberikan jawaban.</li>
            <li>Untuk soal Pilihan Ganda, berilah tanda silang (X) pada huruf jawaban pilihan Anda.</li>
            <li>Untuk soal Essay/Isian, jawablah dengan singkat dan jelas pada tempat yang disediakan.</li>
        </ol>
    </div>

    @php $noUrut = 1; @endphp

    <!-- BAGIAN I: PILIHAN GANDA -->
    @if($mcQuestions->isNotEmpty())
        <div class="title-section">I. PILIHAN GANDA</div>
        @foreach($mcQuestions as $q)
            <div class="question-item">
                <table>
                    <tr>
                        <td style="width: 25px; vertical-align: top; font-weight: bold;">{{ $noUrut++ }}.</td>
                        <td style="vertical-align: top;">
                            <div>{!! nl2br(e($q->question_text)) !!}</div>

                            @if($q->image && file_exists(public_path('storage/' . $q->image)))
                                <div style="margin-top: 5px;"><img src="{{ public_path('storage/' . $q->image) }}" max-height="150" /></div>
                            @endif

                            @if(is_array($q->options))
                                <table class="options-table">
                                    <tr>
                                        <td>A. {{ $q->options['A'] ?? '' }}</td>
                                        <td>B. {{ $q->options['B'] ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>C. {{ $q->options['C'] ?? '' }}</td>
                                        <td>D. {{ $q->options['D'] ?? '' }}</td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        @endforeach
    @endif

    <!-- BAGIAN II: ESSAY / ISIAN -->
    @if($essayQuestions->isNotEmpty())
        <div class="title-section">II. ISIAN / ESSAY</div>
        @foreach($essayQuestions as $q)
            <div class="question-item">
                <table>
                    <tr>
                        <td style="width: 25px; vertical-align: top; font-weight: bold;">{{ $noUrut++ }}.</td>
                        <td style="vertical-align: top;">
                            <div>{!! nl2br(e($q->question_text)) !!}</div>

                            @if($q->image && file_exists(public_path('storage/' . $q->image)))
                                <div style="margin-top: 5px;"><img src="{{ public_path('storage/' . $q->image) }}" max-height="150" /></div>
                            @endif

                            <div style="border-bottom: 1px dotted #888; height: 35px; width: 100%; margin-top: 5px;"></div>
                        </td>
                    </tr>
                </table>
            </div>
        @endforeach
    @endif

</body>
</html>