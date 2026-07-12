<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kuis Percobaan Gratis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        .animate-pop-in { animation: popInModal 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes popInModal { to { transform: scale(1); opacity: 1; } }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25%, 75% { transform: translateX(-5px); } 50% { transform: translateX(5px); } }
        .shake { animation: shake 0.4s ease-in-out; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 flex justify-center items-center min-h-screen p-4">

    <!-- MODAL POP UP 1: AKSES DIBATASI -->
    <div id="auth-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-[60] backdrop-blur-sm transition-all duration-300">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[90%] text-center shadow-2xl scale-80 opacity-0 transition-all duration-300" id="auth-modal-box">
            <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Akses Terbatas</h3>
            <p class="text-xs text-gray-500 mb-6 leading-relaxed">Fitur pembahasan lengkap dan penyimpanan nilai permanen hanya tersedia untuk pengguna terdaftar. Silakan buat akun untuk menikmati akses penuh.</p>
            <div class="flex gap-2">
                <button onclick="dismissAuthModalAndContinue()" class="w-1/2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 rounded-xl text-xs transition cursor-pointer">Lanjutkan Kuis</button>
                <a href="{{ url('/login') }}" class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl text-xs transition cursor-pointer text-center flex items-center justify-center">Login Akun</a>
            </div>
        </div>
    </div>

    <!-- MODAL POP UP 2: STATUS EVALUASI PER JAWABAN SOAL -->
    <div id="result-modal" class="fixed top-0 left-0 w-full h-full bg-black/50 hidden justify-center items-center z-50 backdrop-blur-sm">
        <div class="bg-white p-6 rounded-2xl max-w-md w-[92%] text-center shadow-2xl scale-80 opacity-0 transition-all duration-300 flex flex-col max-h-[85vh]">
            <h3 id="modal-status" class="text-xl font-bold mb-1">Status</h3>
            
            <!-- Tempat Munculnya Pembahasan Asli -->
            <div id="explanation-container" class="hidden flex-col bg-indigo-50/50 border-l-4 border-indigo-600 p-4 rounded-r-xl text-left text-xs md:text-sm leading-relaxed my-3 overflow-y-auto flex-1">
                <div class="font-bold text-indigo-700 mb-1 flex items-center space-x-1 shrink-0">
                    <span>💡</span> <span>Materi & Pembahasan:</span>
                </div>
                <div class="w-full text-gray-600 break-words" id="modal-explanation"></div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 mt-3 w-full shrink-0">
                <button id="btn-explanation-toggle" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 rounded-xl text-sm" onclick="handleExplanationClick()">Lihat Pembahasan</button>
                <button class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl text-sm" onclick="nextQuestion()">Next Soal &rarr;</button>
            </div>
        </div>
    </div>

    <!-- MODAL POP UP 3: REKAP EVALUASI AKHIR -->
    <div id="completion-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-50 backdrop-blur-md">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-[92%] text-center shadow-2xl scale-80 opacity-0 transition-all duration-300">
            <h3 class="text-lg font-bold text-gray-800 mb-1">Demo Selesai! 🎉</h3>
            <p class="text-xs text-gray-500 mb-4">Skor Percobaan Kamu:</p>
            <div class="text-5xl font-black text-indigo-600 mb-6" id="final-score-display">0</div>
            <button onclick="showAuthModal()" class="w-full bg-amber-500 text-white font-bold py-3 rounded-xl text-sm mb-2 hover:bg-amber-600">Review & Pembahasan Lengkap</button>
            <a href="{{ url('/') }}" class="block w-full bg-gray-100 text-gray-600 font-bold py-3 rounded-xl text-sm hover:bg-gray-200">Kembali ke Beranda</a>
        </div>
    </div>

    <!-- INTERFACE UTAMA -->
    <div id="app" class="bg-white w-[90%] max-w-[500px] p-6 md:p-8 rounded-2xl shadow-xl relative z-20 m-auto">
        
        <!-- Screen 1: Start Screen -->
        <div id="start-screen" class="screen flex flex-col items-center text-center animate-fade-in">
            <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mb-2">Kuis Percobaan Gratis</h1>
            <p class="text-gray-500 text-xs leading-relaxed mb-6">Uji kemampuanmu langsung dengan 10 soal variasi pilihan ganda dan rumpang. Fitur evaluasi instan tanpa perlu mendaftar akun.</p>
            <button class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 cursor-pointer" onclick="startQuiz()">Mulai Sekarang</button>
        </div>

        <!-- Screen 2: Lembar Pengerjaan Soal -->
        <div id="quiz-screen" class="screen hidden flex-col w-full animate-fade-in text-center">
            <div class="w-full h-1.5 bg-gray-200 rounded-full mb-4"><div class="h-full bg-indigo-600 w-0 transition-all" id="progress"></div></div>
            <p id="question-counter" class="text-indigo-600 text-[10px] font-bold bg-indigo-50 px-3 py-1 rounded-full mb-3 mx-auto w-max border border-indigo-100"></p>
            <h2 id="question-text" class="text-base font-bold text-gray-800 mb-5 leading-relaxed">Loading Soal...</h2>
            <div id="options" class="w-full space-y-2"></div>
        </div>

    </div>

    <!-- LOGIKA JAVASCRIPT -->
    <script>
        // DATA SET 10 SOAL DUMMY CAMPURAN PILIHAN GANDA & ESSAY
        const quizData = [
            { type: 'multiple_choice', question_text: 'Siapakah Presiden pertama Republik Indonesia?', options: { 'A': 'Soekarno', 'B': 'Soeharto', 'C': 'B.J. Habibie', 'D': 'Gus Dur' }, correct_answer: 'A', explanation: 'Ir. Soekarno adalah proklamator sekaligus Presiden pertama Indonesia.' },
            { type: 'essay', question_text: 'Ibukota dari negara Jepang adalah [blank].', correct_answer: '["Tokyo"]', explanation: 'Tokyo adalah pusat pemerintahan, ekonomi, dan ibukota resmi negara Jepang.' },
            { type: 'multiple_choice', question_text: 'Planet manakah yang memiliki diameter terbesar di Tata Surya?', options: { 'A': 'Mars', 'B': 'Bumi', 'C': 'Jupiter', 'D': 'Saturnus' }, correct_answer: 'C', explanation: 'Jupiter merupakan planet terbesar dengan ukuran massa mencapai dua kali lipat total seluruh planet lainnya.' },
            { type: 'essay', question_text: 'Rumus molekul kimia dari unsur air murni adalah [blank].', correct_answer: '["H2O"]' },
            { type: 'multiple_choice', question_text: 'Benua manakah yang memiliki wilayah daratan terluas di dunia?', options: { 'A': 'Afrika', 'B': 'Asia', 'C': 'Amerika Utara', 'D': 'Eropa' }, correct_answer: 'B' },
            { type: 'essay', question_text: 'Pencipta dari lagu kebangsaan Indonesia Raya adalah [blank].', correct_answer: '["Wage Rudolf Supratman", "W.R. Supratman", "WR Supratman"]' },
            { type: 'multiple_choice', question_text: 'Hewan mamalia darat manakah yang dikenal memiliki ukuran tubuh terbesar?', options: { 'A': 'Gajah', 'B': 'Badak', 'C': 'Kudanil', 'D': 'Jerapah' }, correct_answer: 'A' },
            { type: 'essay', question_text: 'Hasil perhitungan matematika dasar dari perkalian 12 x 12 adalah [blank].', correct_answer: '["144"]' },
            { type: 'multiple_choice', question_text: 'Ibukota resmi dari negara Prancis adalah kota?', options: { 'A': 'Berlin', 'B': 'Madrid', 'C': 'Paris', 'D': 'London' }, correct_answer: 'C' },
            { type: 'essay', question_text: 'Kategori kuis isian ini disebut juga dengan tipe soal [blank].', correct_answer: '["essay", "isian rumpang"]' }
        ];

        let currentQuestionIndex = 0;
        let totalEarnedScore = 0; 
        let isAnswering = false;

        function showAuthModal() {
            const modal = document.getElementById("auth-modal");
            const box = document.getElementById("auth-modal-box");
            modal.classList.remove("hidden");
            modal.classList.add("flex");
            setTimeout(() => { box.classList.remove("scale-80", "opacity-0"); box.classList.add("scale-100", "opacity-100"); }, 10);
        }

        function dismissAuthModalAndContinue() {
            const modal = document.getElementById("auth-modal");
            document.getElementById("auth-modal-box").classList.remove("scale-100", "opacity-100");
            setTimeout(() => { 
                modal.classList.replace("flex", "hidden");
                nextQuestion();
            }, 150);
        }

        // TOGGLE PEMBAHASAN BUKA-TUTUP (3 SOAL PERTAMA) ATAU ALUR MODAL (SOAL 4-10)
        function handleExplanationClick() {
            if (currentQuestionIndex < 3) {
                const expBox = document.getElementById("explanation-container");
                const btn = document.getElementById("btn-explanation-toggle");
                
                if (expBox.classList.contains("hidden")) {
                    expBox.classList.remove("hidden");
                    expBox.classList.add("flex");
                    btn.innerText = "Tutup Pembahasan";
                } else {
                    expBox.classList.remove("flex");
                    expBox.classList.add("hidden");
                    btn.innerText = "Lihat Pembahasan";
                }
            } else {
                showAuthModal();
            }
        }

        function switchScreen(id) {
            document.querySelectorAll(".screen").forEach(s => { s.classList.remove("flex"); s.classList.add("hidden"); });
            document.getElementById(id).classList.remove("hidden");
            document.getElementById(id).classList.add("flex");
        }

        function startQuiz() {
            switchScreen("quiz-screen");
            loadQuestion();
        }

        function loadQuestion() {
            isAnswering = false;
            const q = quizData[currentQuestionIndex];
            
            document.getElementById("explanation-container").classList.replace("flex", "hidden");
            document.getElementById("btn-explanation-toggle").innerText = "Lihat Pembahasan";

            document.getElementById("question-counter").innerText = `Soal ${currentQuestionIndex + 1} dari ${quizData.length}`;
            document.getElementById("progress").style.width = `${(currentQuestionIndex / quizData.length) * 100}%`;

            const containerText = document.getElementById("question-text");
            const containerOptions = document.getElementById("options");
            containerOptions.innerHTML = "";

            if (q.type === 'essay') {
                let parts = q.question_text.split('[blank]');
                let htmlText = '';
                for(let i=0; i<parts.length; i++) {
                    htmlText += parts[i];
                    if(i < parts.length - 1) {
                        htmlText += `<input type="text" class="essay-input mx-1 px-2 py-0.5 border-b-2 border-indigo-400 bg-indigo-50 w-24 text-center text-sm font-bold focus:outline-none focus:border-indigo-600 rounded-t">`;
                    }
                }
                containerText.innerHTML = htmlText;
                
                const btn = document.createElement("button");
                btn.className = "w-full bg-indigo-600 text-white font-bold py-3 mt-4 rounded-xl text-sm cursor-pointer shadow-sm";
                btn.innerHTML = "Kunci & Cek Jawaban 🚀";
                btn.onclick = () => submitEssay();
                containerOptions.appendChild(btn);

                // FIX: Tambah Event Listener agar Input Rumpang Bisa Langsung di-Enter
                const inputs = document.querySelectorAll('.essay-input');
                inputs.forEach(inp => {
                    inp.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') submitEssay();
                    });
                });
                if(inputs.length > 0) inputs[0].focus();
            } else {
                containerText.innerText = q.question_text;
                Object.keys(q.options).forEach(key => {
                    const btn = document.createElement("button");
                    btn.className = "option-btn w-full text-left bg-white text-gray-700 border-2 border-gray-200 p-3.5 rounded-xl font-medium text-sm cursor-pointer transition duration-150 hover:bg-gray-50";
                    btn.innerHTML = `<strong class="text-indigo-600 mr-2">${key}.</strong> ${q.options[key]}`;
                    btn.onclick = () => checkMC(key, btn);
                    containerOptions.appendChild(btn);
                });
            }
        }

        function checkMC(selected, btn) {
            if (isAnswering) return;
            isAnswering = true;
            const q = quizData[currentQuestionIndex];
            const isCorrect = selected === q.correct_answer;

            if (isCorrect) totalEarnedScore += (100 / quizData.length);

            btn.classList.replace("bg-white", isCorrect ? "bg-emerald-500" : "bg-red-500");
            btn.classList.replace("text-gray-700", "text-white");
            btn.classList.replace("border-gray-200", isCorrect ? "border-emerald-500" : "border-red-500");
            
            if(!isCorrect) document.getElementById('app').classList.add('shake');
            setTimeout(() => { document.getElementById('app').classList.remove('shake'); showModal(isCorrect); }, 600);
        }

function submitEssay() {
            if (isAnswering) return;
            isAnswering = true;
            const q = quizData[currentQuestionIndex];
            const inputs = document.querySelectorAll('.essay-input');
            
            let correctAnswers = [];
            try { correctAnswers = JSON.parse(q.correct_answer); } catch(e) { correctAnswers = [q.correct_answer]; }
            
            let correctCount = 0;
            
            // Cek apakah jumlah rumpang di teks (inputs) sama dengan jumlah elemen kunci di database
            // Jika rumpang cuma 1 tapi isi array kunci ada banyak, berarti itu opsi pilihan alternatif jawaban!
            const isAlternativeOption = (inputs.length === 1 && correctAnswers.length > 1);

            inputs.forEach((inp, idx) => {
                let uAns = inp.value.trim().toLowerCase();
                let isMatched = false;

                if (isAlternativeOption) {
                    // Jika mode alternatif, asalkan COCOK dengan salah satu isi array, langsung dianggap BENAR
                    isMatched = correctAnswers.some(ans => ans.trim().toLowerCase() === uAns);
                } else {
                    // Jika rumpang multi-kolom berurutan, cocokkan indeks ke-x dengan kunci indeks ke-x
                    let cAns = (correctAnswers[idx] || "").trim().toLowerCase();
                    isMatched = (uAns === cAns);
                }
                
                if (isMatched && uAns !== "") {
                    inp.classList.remove("border-indigo-400", "bg-indigo-50");
                    inp.classList.add("border-emerald-500", "bg-emerald-50", "text-emerald-700");
                    correctCount++;
                } else {
                    inp.classList.remove("border-indigo-400", "bg-indigo-50");
                    inp.classList.add("border-red-500", "bg-red-50", "text-red-700");
                }
                inp.disabled = true;
            });

            // Kalkulasi penambahan skor kuis percobaan
            if (isAlternativeOption) {
                if (correctCount > 0) totalEarnedScore += (100 / quizData.length);
            } else {
                if (correctAnswers.length > 0) {
                    totalEarnedScore += (correctCount / correctAnswers.length) * (100 / quizData.length);
                }
            }
            
            // Evaluasi penentuan status modal pop up (Benar total / Salah)
            let isAllCorrect = isAlternativeOption ? (correctCount > 0) : (correctCount === correctAnswers.length);
            
            if(!isAllCorrect) document.getElementById('app').classList.add('shake');
            setTimeout(() => { document.getElementById('app').classList.remove('shake'); showModal(isAllCorrect); }, 600);
        }

        function showModal(isCorrect) {
            const modal = document.getElementById("result-modal");
            const box = modal.querySelector('div');
            const status = document.getElementById("modal-status");
            const q = quizData[currentQuestionIndex];

            if (isCorrect) {
                status.innerText = "Jawaban Benar!";
                status.className = "text-xl font-black mb-1 text-emerald-500";
            } else {
                status.innerText = "Jawaban Kurang Tepat!";
                status.className = "text-xl font-black mb-1 text-red-500";
            }

            document.getElementById("modal-explanation").innerText = q.explanation || "Pembahasan lengkap dikunci.";

            // FIX: Khusus untuk 3 soal pertama (0, 1, 2) -> Langsung tampilkan pembahasannya secara otomatis sejak awal
            if (currentQuestionIndex < 3) {
                const expBox = document.getElementById("explanation-container");
                expBox.classList.remove("hidden");
                expBox.classList.add("flex");
                document.getElementById("btn-explanation-toggle").innerText = "Tutup Pembahasan";
            }

            modal.classList.remove("hidden");
            modal.classList.add("flex");
            setTimeout(() => { box.classList.remove("scale-80", "opacity-0"); box.classList.add("scale-100", "opacity-100"); }, 10);
        }

        function nextQuestion() {
            document.getElementById("result-modal").classList.replace("flex", "hidden");
            document.getElementById("result-modal").querySelector('div').classList.remove("scale-100", "opacity-100");
            currentQuestionIndex++;
            
            if (currentQuestionIndex < quizData.length) {
                loadQuestion();
            } else {
                document.getElementById('quiz-screen').classList.replace('flex', 'hidden');
                const completionModal = document.getElementById("completion-modal");
                document.getElementById("final-score-display").innerText = Math.round(totalEarnedScore);
                completionModal.classList.remove("hidden");
                completionModal.classList.add("flex");
                setTimeout(() => { completionModal.querySelector('div').classList.add("scale-100", "opacity-100"); }, 10);
            }
        }
    </script>
</body>
</html>