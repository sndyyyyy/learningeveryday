<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mengerjakan: {{ $quiz->title }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
      /* Kita sisakan animasi kustom ini di CSS karena lebih smooth daripada class bawaan */
      @keyframes popInModal { to { transform: scale(1); opacity: 1; } }
      @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
      @keyframes shake { 0%, 100% { transform: translateX(0); } 20%, 60% { transform: translateX(-5px); } 40%, 80% { transform: translateX(5px); } }
      
      .animate-pop-in { animation: popInModal 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
      .animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
      .shake { animation: shake 0.4s ease-in-out; }
    </style>
  </head>
  <body class="bg-gray-100 text-gray-800 flex justify-center items-center min-h-screen overflow-y-auto py-8">
    
    <canvas id="confetti" class="fixed top-0 left-0 w-full h-full pointer-events-none z-10"></canvas>

    <div id="result-modal" class="fixed top-0 left-0 w-full h-full bg-black/50 hidden justify-center items-center z-50 backdrop-blur-sm transition-all duration-300">
      <div class="bg-white p-8 rounded-2xl max-w-[450px] w-[90%] text-center shadow-2xl scale-80 opacity-0 transition-all duration-300">
        <h3 id="modal-status" class="text-xl font-bold mb-2">Status Jawaban</h3>
        
        <div class="bg-gray-100 border-l-4 border-indigo-600 p-4 rounded-lg text-left text-sm leading-relaxed my-4 max-h-[150px] overflow-y-auto">
            <p id="modal-explanation" class="text-gray-600">Pembahasan...</p>
        </div>
        
        <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition duration-200 cursor-pointer" onclick="nextQuestion()">Next &rarr;</button>
      </div>
    </div>

    <div id="app" class="bg-white w-[90%] max-w-[500px] p-8 rounded-2xl shadow-xl relative z-20 m-auto transition-all duration-300">
      
      <div id="start-screen" class="screen hidden flex-col items-center text-center animate-fade-in">
        <h1 class="text-2xl font-bold text-indigo-600 mb-3">{{ $quiz->title }}</h1>
        <p class="text-gray-500 text-sm leading-relaxed mb-6">{{ $quiz->description ?? 'Uji kemampuanmu sekarang dengan kuis interaktif ini!' }}</p>
        <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl shadow-md transition duration-200 cursor-pointer" onclick="startQuiz()">Start Quiz</button>
      </div>

      <div id="quiz-screen" class="screen hidden flex-col items-center text-center animate-fade-in">
        <div class="w-full h-2 bg-gray-200 rounded-full mb-6 overflow-hidden">
          <div class="h-full bg-indigo-600 w-0 transition-all duration-300 ease-out" id="progress"></div>
        </div>
        
        <p id="question-counter" class="text-gray-400 text-xs font-semibold mb-2"></p>
        <h2 id="question-text" class="text-lg font-bold text-gray-800 mb-6 leading-snug">Loading Question...</h2>
        
        <div id="media-container" class="w-full"></div>

        <div class="options-container w-full mt-4" id="options"></div>
      </div>

      <div id="loading-screen" class="screen hidden flex-col items-center text-center animate-fade-in">
        <h1 class="text-xl font-bold text-indigo-600 mb-2">Menghitung Skor...</h1>
        <p class="text-gray-500 text-sm leading-relaxed">Mohon tunggu sebentar, hasil kamu sedang dikirim ke sistem database.</p>
        
        <form id="submit-form" action="{{ route('peserta.quiz.submit', $quiz->id) }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="final_score" id="form-score">
            <input type="hidden" name="peserta_answers" id="form-answers">
        </form>
      </div>
    </div>

    <script>
      // Ambil data kuis dari database
      const quizData = @json($questions);

      let currentQuestionIndex = 0;
      let correctAnswersCount = 0;
      let isAnswering = false;
      let pesertaAnswers = {}; 

      // Setup Audio Sfx Benar/Salah bawaan referensi
      const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      function playTone(frequency, type, duration) {
        if (audioCtx.state === "suspended") audioCtx.resume();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();
        oscillator.type = type;
        oscillator.frequency.value = frequency;
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        oscillator.start();
        gainNode.gain.setValueAtTime(1, audioCtx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + duration);
        oscillator.stop(audioCtx.currentTime + duration);
      }

      function playCorrectSound() {
        playTone(600, "sine", 0.1);
        setTimeout(() => playTone(800, "sine", 0.2), 100);
      }

      function playWrongSound() {
        playTone(300, "sawtooth", 0.3);
        document.getElementById("app").classList.add("shake");
        setTimeout(() => document.getElementById("app").classList.remove("shake"), 400);
      }

      function switchScreen(screenId) {
        document.querySelectorAll(".screen").forEach((s) => s.classList.replace("flex", "hidden"));
        document.getElementById(screenId).classList.replace("hidden", "flex");
      }

      function startQuiz() {
        switchScreen("quiz-screen");
        loadQuestion();
      }

      function loadQuestion() {
        isAnswering = false;
        const q = quizData[currentQuestionIndex];

        document.getElementById("question-text").innerText = q.question_text;
        document.getElementById("question-counter").innerText = `Question ${currentQuestionIndex + 1} of ${quizData.length}`;

        const progress = (currentQuestionIndex / quizData.length) * 100;
        document.getElementById("progress").style.width = `${progress}%`;

        const optionsContainer = document.getElementById("options");
        const mediaContainer = document.getElementById("media-container");
        
        optionsContainer.innerHTML = "";
        mediaContainer.innerHTML = ""; // Reset media lama secara bersih

        // 1. Render Gambar jika ada
        if (q.image) {
          const img = document.createElement("img");
          img.src = `/storage/${q.image}`; 
          img.className = "w-full max-h-[200px] object-contain rounded-xl mb-4 shadow-sm border border-gray-100 animate-fade-in";
          mediaContainer.appendChild(img);
        }

        // 2. Render Audio jika ada
        if (q.audio) {
          const audioPlayer = document.createElement("audio");
          audioPlayer.src = `/storage/${q.audio}`;
          audioPlayer.controls = true;
          audioPlayer.className = "w-full mb-4 outline-none";
          mediaContainer.appendChild(audioPlayer);
        }

        // 3. Render Pilihan Ganda (Meniru tombol native CSS dengan utilitas Tailwind)
        Object.keys(q.options).forEach((key) => {
          const btn = document.createElement("button");
          // Class dasar tombol pilihan ganda dipasang di sini
          btn.className = "option-btn w-full text-left bg-white text-gray-700 border-2 border-gray-200 p-4 rounded-xl mb-3 font-medium transition-all duration-200 hover:border-indigo-600 hover:bg-indigo-50 cursor-pointer text-base";
          btn.innerHTML = `<strong class="text-indigo-600 mr-1">${key}.</strong> ${q.options[key]}`;
          btn.onclick = () => selectAnswer(key, btn);
          optionsContainer.appendChild(btn);
        });
      }

      function selectAnswer(selectedKey, btnElement) {
        if (isAnswering) return;
        isAnswering = true;

        const q = quizData[currentQuestionIndex];
        const isCorrect = selectedKey === q.correct_answer;
        
        pesertaAnswers[q.id] = selectedKey;

        // Ambil semua tombol opsi untuk manipulasi warna akhir
        const buttons = document.querySelectorAll(".option-btn");

        if (isCorrect) {
          // Warna Sukses Hijau Tailwind (Menggantikan .correct)
          btnElement.classList.replace("bg-white", "bg-emerald-500");
          btnElement.classList.replace("text-gray-700", "text-white");
          btnElement.classList.replace("border-gray-200", "border-emerald-500");
          btnElement.innerHTML = `<strong class="text-white mr-1">${selectedKey}.</strong> ${q.options[selectedKey]}`;
          correctAnswersCount++;
          playCorrectSound();
        } else {
          // Warna Gagal Merah Tailwind (Menggantikan .wrong)
          btnElement.classList.replace("bg-white", "bg-red-500");
          btnElement.classList.replace("text-gray-700", "text-white");
          btnElement.classList.replace("border-gray-200", "border-red-500");
          btnElement.innerHTML = `<strong class="text-white mr-1">${selectedKey}.</strong> ${q.options[selectedKey]}`;
          playWrongSound();
          
          // Cari kunci jawaban yang benar, paksa ganti jadi hijau biar peserta tahu
          Object.keys(q.options).forEach((key, index) => {
             if(key === q.correct_answer) {
                 buttons[index].classList.replace("bg-white", "bg-emerald-500");
                 buttons[index].classList.replace("text-gray-700", "text-white");
                 buttons[index].classList.replace("border-gray-200", "border-emerald-500");
                 buttons[index].innerHTML = `<strong class="text-white mr-1">${key}.</strong> ${q.options[key]}`;
             }
          });
        }

        setTimeout(() => {
          showModal(isCorrect, q);
        }, 800);
      }

      function showModal(isCorrect, currentQuestion) {
        const modal = document.getElementById("result-modal");
        const modalBox = modal.querySelector('div');
        const statusText = document.getElementById("modal-status");
        const expText = document.getElementById("modal-explanation");

        if (isCorrect) {
          statusText.innerText = "🎉 Benar Sekali!";
          statusText.className = "text-xl font-bold mb-2 text-emerald-500";
        } else {
          statusText.innerText = "❌ Kurang Tepat!";
          statusText.className = "text-xl font-bold mb-2 text-red-500";
        }

        expText.innerText = currentQuestion.explanation || "Tidak ada pembahasan spesifik untuk soal ini.";
        
        // Munculkan Modal dengan Efek Pop In bawaan Tailwind + Kustom CSS
        modal.classList.replace("hidden", "flex");
        setTimeout(() => {
            modalBox.classList.replace("scale-80", "scale-100");
            modalBox.classList.replace("opacity-0", "opacity-100");
            modalBox.classList.add("animate-pop-in");
        }, 10);
      }

      function nextQuestion() {
        const modal = document.getElementById("result-modal");
        const modalBox = modal.querySelector('div');
        
        // Sembunyikan Modal kembali
        modalBox.classList.replace("scale-100", "scale-80");
        modalBox.classList.replace("opacity-100", "opacity-0");
        modalBox.classList.remove("animate-pop-in");
        
        setTimeout(() => {
            modal.classList.replace("flex", "hidden");
            currentQuestionIndex++;
            
            if (currentQuestionIndex < quizData.length) {
              loadQuestion();
            } else {
              finishQuizAndSubmit();
            }
        }, 200);
      }

      function finishQuizAndSubmit() {
        switchScreen("loading-screen");
        const finalScore = Math.round((correctAnswersCount / quizData.length) * 100);
        document.getElementById("form-score").value = finalScore;
        document.getElementById("form-answers").value = JSON.stringify(pesertaAnswers);
        document.getElementById("submit-form").submit();
      }

      // Jalankan inisialisasi screen pertama kali
      document.addEventListener("DOMContentLoaded", () => {
          switchScreen("start-screen");
      });
    </script>
  </body>
</html>