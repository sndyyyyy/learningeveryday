<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Detail Hasil {{ $quiz->title }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans pb-16">

    @include('layouts.navbar')

    <div class="bg-white border-t border-gray-100 py-4 px-4 md:px-8 shadow-xs sticky top-16 z-30">
        <div class="max-w-4xl mx-auto flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-xs text-gray-500 hover:text-indigo-600 font-semibold transition">&larr; Kembali ke Dashboard</a>
                <h2 class="text-base md:text-lg font-bold text-gray-800 mt-1">Lembar Jawaban: {{ $result->user->name }}</h2>
                <p class="text-xs text-gray-400">Mata Uji kuis: <span class="text-indigo-600 font-medium">{{ $quiz->title }}</span></p>
            </div>
            <div class="flex items-center space-x-3 bg-gray-50 p-2 px-4 rounded-xl border border-gray-100">
                <span class="text-xs font-medium text-gray-500">Skor Peserta:</span>
                <span class="text-xl font-black {{ $result->score >= 70 ? 'text-emerald-500' : 'text-red-500' }}">{{ $result->score }} / 100</span>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto mt-8 px-4 space-y-6">
        
        @foreach($questions as $index => $q)
            @php
                $userAnswer = $result->answers[$q->id] ?? '';
                $isCorrect = ($userAnswer === $q->correct_answer);
            @endphp

            <div class="bg-white p-6 rounded-2xl shadow-sm border {{ $isCorrect ? 'border-emerald-100' : 'border-red-100' }}">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pertanyaan {{ $index + 1 }}</span>
                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide {{ $isCorrect ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        {{ $isCorrect ? '✓ Benar' : '✗ Salah' }}
                    </span>
                </div>

                <p class="font-semibold text-gray-800 text-base mb-4">{{ $q->question_text }}</p>

                @if($q->image)
                    <div class="mb-4">
                        <img src="/storage/{{ $q->image }}" alt="Gambar Soal" class="max-h-48 object-contain rounded-xl border border-gray-100">
                    </div>
                @endif
                @if($q->audio)
                    <div class="mb-4">
<audio src="{{ route('audio.stream', ['path' => $q->audio]) }}" controls class="w-full max-w-sm outline-none scale-90 origin-left"></audio>                    </div>
                @endif

                <div class="space-y-2 pl-2">
                    @foreach($q->options as $key => $optionText)
                        @php
                            $bgClass = 'bg-gray-50 text-gray-700 border border-gray-100';
                            if ($key === $q->correct_answer) {
                                $bgClass = 'bg-emerald-50 text-emerald-700 border border-emerald-300 font-semibold';
                            } elseif ($key === $userAnswer && !$isCorrect) {
                                $bgClass = 'bg-red-50 text-red-700 border border-red-200 line-through';
                            }
                        @endphp

                        <div class="p-3.5 rounded-xl text-sm flex justify-between items-center {{ $bgClass }}">
                            <span><strong>{{ $key }}.</strong> {{ $optionText }}</span>
                            @if($key === $q->correct_answer)
                                <span class="text-emerald-600 text-xs font-bold font-mono">[Kunci Jawaban]</span>
                            @elseif($key === $userAnswer)
                                <span class="text-red-500 text-xs font-bold font-mono">[Jawaban Peserta]</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($q->explanation)
                    <div class="mt-4 bg-indigo-50/50 border-l-4 border-indigo-600 p-4 rounded-r-xl text-xs md:text-sm">
                        <span class="font-bold text-indigo-700 block mb-1">💡 Pembahasan Utama:</span>
                        <p class="text-gray-600">{{ $q->explanation }}</p>
                    </div>
                @endif
            </div>
        @endforeach

    </div>

</body>
</html>