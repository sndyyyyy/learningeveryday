<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Mengerjakan: {{ $quiz->title }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <!-- 🌟 LIBRARY DRAG & DROP SORTABLEJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; }
        @keyframes popInModal { to { transform: scale(1); opacity: 1; } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 20%, 60% { transform: translateX(-5px); } 40%, 80% { transform: translateX(5px); } }
      
        .animate-pop-in { animation: popInModal 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
        .shake { animation: shake 0.4s ease-in-out; }

        /* Gaya Khusus Pointer Tag Panah Marlins */
        .marlins-pointer {
            position: absolute;
            z-index: 30;
            display: inline-flex;
            align-items: center;
            background-color: #0084ad;
            color: white;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 9999px 0 0 9999px;
            cursor: grab;
            user-select: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
            transition: background-color 0.2s;
        }
        .marlins-pointer:after {
            content: '';
            position: absolute;
            right: -10px;
            top: 0;
            width: 0;
            height: 0;
            border-top: 13px solid transparent;
            border-bottom: 13px solid transparent;
            border-left: 10px solid #0084ad;
        }
        .marlins-pointer.correct {
            background-color: #059669;
        }
        .marlins-pointer.correct:after {
            border-left-color: #059669;
        }
        .marlins-pointer.wrong {
            background-color: #dc2626;
        }
        .marlins-pointer.wrong:after {
            border-left-color: #dc2626;
        }
    </style>
  </head>
  <body class="bg-gray-100 text-gray-800 flex justify-center items-center min-h-screen overflow-y-auto py-6">
    
    <canvas id="confetti" class="fixed top-0 left-0 w-full h-full pointer-events-none z-10"></canvas>

    <div id="result-modal" class="fixed top-0 left-0 w-full h-full bg-black/50 hidden justify-center items-center z-50 backdrop-blur-sm transition-all duration-300">
      <div class="bg-white p-6 rounded-2xl max-w-2xl w-[92%] text-center shadow-2xl scale-80 opacity-0 transition-all duration-300 flex flex-col max-h-[90vh]">
        <h3 id="modal-status" class="text-xl font-bold mb-2">Status Jawaban</h3>
        
        <div id="explanation-container" class="hidden flex-col bg-indigo-50/50 border-l-4 border-indigo-600 p-4 rounded-r-xl text-left text-xs md:text-sm leading-relaxed my-3 overflow-y-auto flex-1">
            <div class="font-bold text-indigo-700 mb-1 flex items-center space-x-1 shrink-0">
                <span>💡</span> <span>Materi & Pembahasan:</span>
            </div>
            <div class="w-full text-gray-600 break-words" id="modal-explanation">
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2.5 mt-3 w-full">
            <button id="btn-pelajari" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm transition duration-200 cursor-pointer text-xs md:text-sm" onclick="showExplanationContent()">
                Pelajari Soal
            </button>
            <button class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm transition duration-200 cursor-pointer text-xs md:text-sm" onclick="nextQuestion()">
                Next Soal &rarr;
            </button>
        </div>
      </div>
    </div>

    <div id="quiz-completion-modal" class="fixed top-0 left-0 w-full h-full bg-black/60 hidden justify-center items-center z-50 backdrop-blur-md transition-all duration-300">
      <div class="bg-white p-5 md:p-6 rounded-2xl max-w-lg w-[92%] shadow-2xl scale-80 opacity-0 transition-all duration-300 flex flex-col max-h-[92vh]">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4 shrink-0">
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 flex flex-col items-center justify-center text-center">
                <span class="text-[10px] text-indigo-500 font-bold uppercase tracking-widest mb-1">Skor Akhir</span>
                <span id="completion-score-badge" class="text-5xl font-black text-indigo-600 leading-none">0</span>
            </div>
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 flex items-center justify-center text-center">
                <p id="completion-desc-text" class="text-xs md:text-sm text-gray-700 font-medium leading-relaxed"></p>
            </div>
        </div>

        <div id="completion-review-list" class="hidden max-h-[280px] overflow-y-auto space-y-4 pr-1 mb-3 border-y border-gray-100 py-3">
        </div>

        <div class="flex flex-row gap-2 w-full shrink-0 mt-1">
            <button id="btn-toggle-review" class="w-1/2 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-2 rounded-xl shadow-sm transition duration-150 cursor-pointer text-[11px] md:text-xs text-center flex items-center justify-center space-x-1" onclick="toggleFullReviewList(this)">
                <span>👁️ Lihat Review</span>
                <span class="transition-transform duration-150 transform inline-block text-[9px]">&#9662;</span>
            </button>
            <button class="w-1/2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-2 rounded-xl shadow-sm transition duration-150 cursor-pointer text-[11px] md:text-xs text-center flex items-center justify-center space-x-1" onclick="redirectToDashboard()">
                <span>🚪 Keluar Dashboard</span>
            </button>
        </div>

      </div>
    </div>

    <div id="app" class="bg-white w-[90%] max-w-[650px] p-6 md:p-8 rounded-2xl shadow-xl relative z-20 m-auto transition-all duration-300">
      
      <div id="start-screen" class="screen hidden flex-col items-center text-center animate-fade-in">
        <h1 class="text-xl md:text-2xl font-bold text-indigo-600 mb-2">{{ $quiz->title }}</h1>
        <p class="text-gray-500 text-xs md:text-sm leading-relaxed mb-5">{{ $quiz->description ?? 'Uji kemampuanmu sekarang dengan kuis interaktif ini!' }}</p>
        <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-md transition duration-200 cursor-pointer text-sm" onclick="startQuiz()">Start Quiz</button>
      </div>

      <div id="section-transition-screen" class="screen hidden flex-col items-center justify-center text-center py-10 animate-fade-in">
          <div class="bg-indigo-50 p-3 rounded-2xl mb-3 border border-indigo-100">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 text-indigo-600 animate-pulse">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.967 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.967 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.967 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
              </svg>
          </div>
          <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">Mempersiapkan Bagian Ujian</p>
          <h2 id="transition-section-name" class="text-xl font-black text-gray-800 mt-1 tracking-tight">== Loading Section ==</h2>
          <div class="mt-4 flex space-x-1 justify-center items-center">
              <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
              <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
              <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
          </div>
      </div>

      <div id="quiz-screen" class="screen hidden flex-col items-center text-center animate-fade-in w-full">
        <div class="w-full h-1.5 bg-gray-200 rounded-full mb-4 overflow-hidden">
          <div class="h-full bg-indigo-600 w-0 transition-all duration-300 ease-out" id="progress"></div>
        </div>
        
        <p id="question-counter" class="text-indigo-600 text-[10px] font-bold bg-indigo-50 px-2.5 py-1 rounded-full mb-3 tracking-wide border border-indigo-100/50"></p>
        
        <h2 id="question-text" class="text-base md:text-lg font-bold text-gray-800 mb-5 leading-relaxed w-full">Loading Question...</h2>
        
        <div id="media-container" class="w-full"></div>
        <div class="options-container w-full mt-3" id="options"></div>
      </div>

      <div id="loading-screen" class="screen hidden flex-col items-center text-center animate-fade-in">
        <h1 class="text-lg font-bold text-indigo-600 mb-1">Menghitung Skor...</h1>
        <p class="text-gray-500 text-xs leading-relaxed">Mohon tunggu sebentar, hasil kamu sedang dikirim ke sistem database.</p>
        
        <form id="submit-form" action="{{ route('peserta.quiz.submit', $quiz->id) }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="final_score" id="form-score">
            <input type="hidden" name="peserta_answers" id="form-answers">
        </form>
      </div>
    </div>

    <script>
      const quizData = @json($questions);
      const pesertaUsername = "{{ Auth::user()->email ?? Auth::user()->name }}"; 

      let currentQuestionIndex = 0;
      let totalEarnedScore = 0; 
      let isAnswering = false;
      let pesertaAnswers = {}; 

      let currentSectionId = null;
      let currentSectionName = "";
      let sectionQuestionCounter = 0;

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
        document.querySelectorAll(".screen").forEach((s) => {
            s.classList.remove("flex");
            s.classList.add("hidden");
        });
        document.getElementById(screenId).classList.remove("hidden");
        document.getElementById(screenId).classList.add("flex");
      }

      function startQuiz() {
        checkAndHandleSectionTransition(true);
      }

      function checkAndHandleSectionTransition(isFirstStart = false) {
          const q = quizData[currentQuestionIndex];
          const partId = q.bank_part_id ? q.bank_part_id : 0;
          
          const bankName = (q.bank_part && q.bank_part.bank) ? q.bank_part.bank.bank_name : "";
          const partName = q.bank_part ? q.bank_part.part_name : "General Test";
          const fullSectionName = bankName ? `${bankName} - ${partName}` : partName;

          if (partId !== currentSectionId) {
              currentSectionId = partId;
              currentSectionName = fullSectionName;
              sectionQuestionCounter = 1;

              document.getElementById("transition-section-name").innerText = `== ${currentSectionName} ==`;
              switchScreen("section-transition-screen");

              setTimeout(() => {
                  switchScreen("quiz-screen");
                  loadQuestion();
              }, 3000);
          } else {
              if (!isFirstStart) {
                  sectionQuestionCounter++;
              }
              switchScreen("quiz-screen");
              loadQuestion();
          }
      }

      function isOptionImage(val) {
          if (!val || typeof val !== 'string') return false;
          return val.match(/\.(jpeg|jpg|gif|png|webp)$/i) || 
                 val.startsWith('options/') || 
                 val.startsWith('media/') || 
                 val.startsWith('questions/') ||
                 val.startsWith('bank/');
      }

      function getOptionContentHtml(key, val, textClass = "text-indigo-600") {
          if (isOptionImage(val)) {
              return `
                  <span class="flex items-center gap-2">
                      <strong class="${textClass} shrink-0">${key}.</strong>
                      <img src="/storage/${val}" class="max-h-24 md:max-h-28 object-contain rounded-lg border border-gray-200 p-1 bg-white" />
                  </span>
              `;
          }
          return `<strong class="${textClass} mr-1">${key}.</strong> ${val}`;
      }

      function loadQuestion() {
        isAnswering = false;
        const q = quizData[currentQuestionIndex];

        const currentPartId = q.bank_part_id ? q.bank_part_id : 0;
        const totalSoalInThisSection = quizData.filter(item => {
            const itemPartId = item.bank_part_id ? item.bank_part_id : 0;
            return itemPartId === currentPartId;
        }).length;

        document.getElementById("question-counter").innerText = `⚡ ${currentSectionName} • Soal ${sectionQuestionCounter} dari ${totalSoalInThisSection}`;
        
        const progress = (currentQuestionIndex / quizData.length) * 100;
        document.getElementById("progress").style.width = `${progress}%`;

        const questionTextContainer = document.getElementById("question-text");
        const optionsContainer = document.getElementById("options");
        const mediaContainer = document.getElementById("media-container");
        
        optionsContainer.innerHTML = "";
        mediaContainer.innerHTML = ""; 

        // Gambar utama ditampilkan di mediaContainer kecuali pada mode Labeling (di-render khusus di canvas interaktif)
        if (q.image && q.type !== 'labeling') {
          const img = document.createElement("img");
          img.src = `/storage/${q.image}`; 
          img.className = "w-full max-h-[180px] object-contain rounded-xl mb-3 shadow-sm border border-gray-100 animate-fade-in";
          mediaContainer.appendChild(img);
        }
        if (q.audio) {
          const audioPlayer = document.createElement("audio");
          audioPlayer.src = `{{ route('audio.stream') }}?path=${encodeURIComponent(q.audio)}`;
          audioPlayer.controls = true;
          audioPlayer.className = "w-full mb-3 outline-none text-xs";
          mediaContainer.appendChild(audioPlayer);
        }

        // ==========================================
        // 1. TIPE ESSAY
        // ==========================================
        if (q.type === 'essay') {
            let parts = q.question_text.split('[blank]');
            let htmlText = '';
            
            for(let i=0; i<parts.length; i++) {
                htmlText += parts[i];
                if(i < parts.length - 1) {
                    htmlText += `<input type="text" class="essay-blank-input mx-1 px-2 py-0.5 border-b-2 border-indigo-400 bg-indigo-50/50 focus:outline-none focus:border-indigo-600 focus:bg-white text-center w-24 md:w-32 text-sm font-bold text-indigo-700 transition rounded-t-md placeholder-gray-300" placeholder="...">`;
                }
            }
            questionTextContainer.innerHTML = htmlText;

            const btn = document.createElement("button");
            btn.className = "w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition duration-200 cursor-pointer mt-4 text-sm";
            btn.innerHTML = "Kunci & Cek Jawaban 🚀";
            btn.onclick = () => submitEssayAnswer();
            optionsContainer.appendChild(btn);

            const inputs = document.querySelectorAll('.essay-blank-input');
            inputs.forEach(input => {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') submitEssayAnswer();
                });
            });
            if(inputs.length > 0) inputs[0].focus();

        // ==========================================
        // 2. TIPE SORTING
        // ==========================================
        } else if (q.type === 'sorting') {
            questionTextContainer.innerText = q.question_text || "Rearrange the words to make a correct sentence.";

            let words = [];
            if (Array.isArray(q.options) && q.options.length > 0) {
                words = q.options;
            } else if (typeof q.options === 'object' && q.options !== null) {
                words = Object.values(q.options).filter(w => w && w !== '-');
            } else {
                words = (q.correct_answer || "").split(' ').sort(() => Math.random() - 0.5);
            }

            const sortContainer = document.createElement("div");
            sortContainer.id = "sorting-box";
            sortContainer.className = "flex flex-wrap items-center justify-center gap-2.5 p-4 bg-gray-50/80 border-2 border-dashed border-gray-200 rounded-2xl min-h-[100px] select-none my-2";

            words.forEach((word) => {
                const item = document.createElement("div");
                item.setAttribute("data-word", word.trim());
                item.className = "sortable-item bg-[#243746] hover:bg-[#1a2936] text-white px-4 py-2 rounded-full font-bold text-xs md:text-sm shadow-md cursor-grab active:cursor-grabbing transition-transform transform active:scale-95 border border-white/20";
                item.innerText = word.trim();
                sortContainer.appendChild(item);
            });

            optionsContainer.appendChild(sortContainer);

            new Sortable(sortContainer, {
                animation: 200,
                ghostClass: 'opacity-40',
                chosenClass: 'scale-105'
            });

            const btnConfirm = document.createElement("button");
            btnConfirm.className = "w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition duration-200 cursor-pointer mt-4 text-sm";
            btnConfirm.innerHTML = "Kunci Susunan Kalimat 🚀";
            btnConfirm.onclick = () => submitSortingAnswer();
            optionsContainer.appendChild(btnConfirm);

        // ==========================================
        // 3. TIPE GROUPING
        // ==========================================
        } else if (q.type === 'grouping') {
            questionTextContainer.innerText = q.question_text || "Drag the words into the correct group.";

            let categories = [];
            let words = [];

            if (q.options && typeof q.options === 'object') {
                categories = q.options.categories || [];
                words = q.options.words || [];
            } else {
                try {
                    const parsed = JSON.parse(q.correct_answer);
                    categories = Object.keys(parsed);
                    words = Object.values(parsed).flat().sort(() => Math.random() - 0.5);
                } catch(e) {
                    categories = ['Category 1', 'Category 2'];
                    words = [];
                }
            }

            const poolWrapper = document.createElement("div");
            poolWrapper.className = "bg-sky-500/90 p-4 rounded-2xl mb-4 shadow-sm";
            
            const poolContainer = document.createElement("div");
            poolContainer.id = "grouping-pool";
            poolContainer.className = "flex flex-wrap items-center justify-center gap-2 min-h-[50px]";

            words.forEach(w => {
                const item = document.createElement("div");
                item.setAttribute("data-word", w.trim());
                item.className = "grouping-item bg-white text-gray-800 px-4 py-1.5 rounded-full font-bold text-xs md:text-sm shadow-xs cursor-grab active:cursor-grabbing hover:shadow-md transition";
                item.innerText = w.trim();
                poolContainer.appendChild(item);
            });

            poolWrapper.appendChild(poolContainer);
            optionsContainer.appendChild(poolWrapper);

            const catGrid = document.createElement("div");
            catGrid.className = `grid grid-cols-1 sm:grid-cols-${Math.min(categories.length, 3)} gap-3 mb-2`;

            categories.forEach(cat => {
                const catCard = document.createElement("div");
                catCard.className = "bg-white border-2 border-[#00607d] rounded-2xl overflow-hidden shadow-xs flex flex-col";

                const catHeader = document.createElement("div");
                catHeader.className = "bg-[#00607d] text-white font-bold py-2 px-3 text-center text-xs md:text-sm capitalize tracking-wide";
                catHeader.innerText = cat;
                catCard.appendChild(catHeader);

                const catDropzone = document.createElement("div");
                catDropzone.setAttribute("data-category", cat);
                catDropzone.className = "grouping-dropzone p-3 min-h-[110px] flex flex-col gap-2 bg-gray-50/50 flex-1 border-t border-dashed border-[#00607d]/30";
                
                catCard.appendChild(catDropzone);
                catGrid.appendChild(catCard);
            });

            optionsContainer.appendChild(catGrid);

            new Sortable(poolContainer, {
                group: 'grouping-words',
                animation: 200,
                ghostClass: 'opacity-30'
            });

            document.querySelectorAll('.grouping-dropzone').forEach(dz => {
                new Sortable(dz, {
                    group: 'grouping-words',
                    animation: 200,
                    ghostClass: 'opacity-30'
                });
            });

            const btnConfirm = document.createElement("button");
            btnConfirm.className = "w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition duration-200 cursor-pointer mt-4 text-sm";
            btnConfirm.innerHTML = "Kunci Pengelompokan 🚀";
            btnConfirm.onclick = () => submitGroupingAnswer();
            optionsContainer.appendChild(btnConfirm);

        // ==========================================
        // 4. 🌟 TIPE LABELING (PINPOINT IMAGE DRAG & DROP)
        // ==========================================
        } else if (q.type === 'labeling') {
            questionTextContainer.innerText = q.question_text || "Drag the words to label the objects in the image.";

            let labelList = [];
            if (Array.isArray(q.options) && q.options.length > 0) {
                labelList = q.options;
            } else {
                try {
                    labelList = Object.keys(JSON.parse(q.correct_answer));
                } catch(e) {
                    labelList = [];
                }
            }

            // Wrapper Layout: Pool Kiri + Canvas Gambar Kanan
            const labelWrapper = document.createElement("div");
            labelWrapper.className = "flex flex-col md:flex-row gap-4 items-start w-full my-2";

            // Sisi Kiri: Pool Kapsul Panah
            const labelPool = document.createElement("div");
            labelPool.id = "labeling-pool";
            labelPool.className = "w-full md:w-44 flex flex-wrap md:flex-col gap-2.5 p-3 bg-gray-50 border border-gray-200 rounded-2xl min-h-[80px]";

            labelList.forEach(lbl => {
                const tag = document.createElement("div");
                tag.className = "marlins-pointer";
                tag.setAttribute("data-label", lbl);
                tag.innerText = lbl;
                labelPool.appendChild(tag);
            });

            // Sisi Kanan: Area Canvas Gambar
            const imageCanvas = document.createElement("div");
            imageCanvas.id = "labeling-canvas";
            imageCanvas.className = "relative flex-1 w-full rounded-2xl overflow-hidden border-2 border-gray-300 shadow-sm bg-black select-none";

            const mainImg = document.createElement("img");
            mainImg.src = `/storage/${q.image}`;
            mainImg.className = "w-full h-auto object-contain block pointer-events-none";
            imageCanvas.appendChild(mainImg);

            labelWrapper.appendChild(labelPool);
            labelWrapper.appendChild(imageCanvas);
            optionsContainer.appendChild(labelWrapper);

            // Inisialisasi Absolute Free Dragging pada Canvas
            setupFreeDragPointers(imageCanvas, labelPool);

            const btnConfirm = document.createElement("button");
            btnConfirm.className = "w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition duration-200 cursor-pointer mt-4 text-sm";
            btnConfirm.innerHTML = "Kunci Posisi Label 🚀";
            btnConfirm.onclick = () => submitLabelingAnswer();
            optionsContainer.appendChild(btnConfirm);

        // ==========================================
        // 5. TIPE PILIHAN GANDA (MULTIPLE CHOICE)
        // ==========================================
        } else {
            questionTextContainer.innerText = q.question_text;
            Object.keys(q.options).forEach((key) => {
              const btn = document.createElement("button");
              btn.className = "option-btn w-full text-left bg-white text-gray-700 border-2 border-gray-200 p-3 rounded-xl mb-2.5 font-medium transition-all duration-200 hover:border-indigo-600 hover:bg-indigo-50 cursor-pointer text-sm md:text-base flex items-center justify-between";
              
              btn.innerHTML = getOptionContentHtml(key, q.options[key]);
              btn.onclick = () => selectAnswer(key, btn);
              optionsContainer.appendChild(btn);
            });
        }
      }

      // 🛠️ DRAG ENGINE UNTUK POINTER LABEL BEBAS DI CANVAS
      function setupFreeDragPointers(canvas, pool) {
          const pointers = document.querySelectorAll(".marlins-pointer");

          pointers.forEach(el => {
              let isDragging = false;
              let startX, startY;

              const onStart = (e) => {
                  if (isAnswering) return;
                  isDragging = true;
                  const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                  const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                  
                  const rect = el.getBoundingClientRect();
                  startX = clientX - rect.left;
                  startY = clientY - rect.top;

                  if (el.parentElement !== canvas) {
                      canvas.appendChild(el);
                  }
                  el.style.position = 'absolute';
                  el.style.zIndex = 1000;
                  document.addEventListener(e.touches ? 'touchmove' : 'mousemove', onMove);
                  document.addEventListener(e.touches ? 'touchend' : 'mouseup', onEnd);
              };

              const onMove = (e) => {
                  if (!isDragging) return;
                  const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                  const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                  const canvasRect = canvas.getBoundingClientRect();

                  let posX = clientX - canvasRect.left - startX;
                  let posY = clientY - canvasRect.top - startY;

                  // Batasi gerakan di dalam batas gambar
                  posX = Math.max(0, Math.min(posX, canvasRect.width - el.offsetWidth));
                  posY = Math.max(0, Math.min(posY, canvasRect.height - el.offsetHeight));

                  const pctX = (posX / canvasRect.width) * 100;
                  const pctY = (posY / canvasRect.height) * 100;

                  el.style.left = `${pctX}%`;
                  el.style.top = `${pctY}%`;
                  el.setAttribute('data-pct-x', (pctX + (el.offsetWidth / canvasRect.width * 100)).toFixed(1)); // Ujung panah di sisi kanan
                  el.setAttribute('data-pct-y', (pctY + (el.offsetHeight / 2 / canvasRect.height * 100)).toFixed(1));
              };

              const onEnd = () => {
                  isDragging = false;
                  el.style.zIndex = 30;
                  document.removeEventListener('mousemove', onMove);
                  document.removeEventListener('mouseup', onEnd);
                  document.removeEventListener('touchmove', onMove);
                  document.removeEventListener('touchend', onEnd);
              };

              el.addEventListener('mousedown', onStart);
              el.addEventListener('touchstart', onStart, { passive: true });
          });
      }

      function selectAnswer(selectedKey, btnElement) {
        if (isAnswering) return;
        isAnswering = true;

        const q = quizData[currentQuestionIndex];
        const isCorrect = selectedKey === q.correct_answer;
        
        pesertaAnswers[q.id] = selectedKey;
        const buttons = document.querySelectorAll(".option-btn");

        if (isCorrect) {
          totalEarnedScore += (100 / quizData.length);
          btnElement.classList.replace("bg-white", "bg-emerald-500");
          btnElement.classList.replace("text-gray-700", "text-white");
          btnElement.classList.replace("border-gray-200", "border-emerald-500");
          btnElement.innerHTML = getOptionContentHtml(selectedKey, q.options[selectedKey], "text-white");
          playCorrectSound();
        } else {
          btnElement.classList.replace("bg-white", "bg-red-500");
          btnElement.classList.replace("text-gray-700", "text-white");
          btnElement.classList.replace("border-gray-200", "border-red-500");
          btnElement.innerHTML = getOptionContentHtml(selectedKey, q.options[selectedKey], "text-white");
          playWrongSound();
          
          Object.keys(q.options).forEach((key, index) => {
             if(key === q.correct_answer) {
                 buttons[index].classList.replace("bg-white", "bg-emerald-500");
                 buttons[index].classList.replace("text-gray-700", "text-white");
                 buttons[index].classList.replace("border-gray-200", "border-emerald-500");
                 buttons[index].innerHTML = getOptionContentHtml(key, q.options[key], "text-white");
             }
          });
        }

        setTimeout(() => { showModal(isCorrect ? 'correct' : 'wrong', q); }, 800);
      }

      function submitEssayAnswer() {
          if (isAnswering) return;
          isAnswering = true;

          const q = quizData[currentQuestionIndex];
          const inputs = document.querySelectorAll('.essay-blank-input');
          let userAnswers = [];
          
          let correctAnswersGroup = [];
          try { 
              correctAnswersGroup = JSON.parse(q.correct_answer); 
          } catch(e) { 
              correctAnswersGroup = [[(q.correct_answer || "").trim().toLowerCase()]]; 
          }

          let correctBlanks = 0;
          let totalBlanks = correctAnswersGroup.length;

          inputs.forEach((input, index) => {
              let uAns = input.value.trim().toLowerCase();
              userAnswers.push(input.value.trim());

              let validAliases = correctAnswersGroup[index] || [];
              let isMatch = Array.isArray(validAliases) && validAliases.includes(uAns);

              if (isMatch) {
                  input.classList.remove("border-indigo-400", "bg-indigo-50/50");
                  input.classList.add("border-emerald-500", "text-emerald-600", "bg-emerald-50");
                  correctBlanks++;
              } else {
                  input.classList.remove("border-indigo-400", "bg-indigo-50/50");
                  input.classList.add("border-red-500", "text-red-600", "bg-red-50");
              }
              input.disabled = true; 
          });

          pesertaAnswers[q.id] = JSON.stringify(userAnswers);

          let pointsPerQuestion = 100 / quizData.length;
          if (totalBlanks > 0) {
              totalEarnedScore += (correctBlanks / totalBlanks) * pointsPerQuestion;
          }

          let allCorrect = (correctBlanks === totalBlanks && totalBlanks > 0);
          let partialCorrect = (correctBlanks > 0 && correctBlanks < totalBlanks);

          if (allCorrect || partialCorrect) {
              playCorrectSound();
          } else {
              playWrongSound();
          }

          let status = allCorrect ? 'correct' : (partialCorrect ? 'partial' : 'wrong');
          setTimeout(() => { showModal(status, q); }, 800);
      }

      function submitSortingAnswer() {
          if (isAnswering) return;
          isAnswering = true;

          const q = quizData[currentQuestionIndex];
          const items = document.querySelectorAll("#sorting-box .sortable-item");
          
          let userArr = [];
          items.forEach(el => userArr.push(el.getAttribute("data-word")));
          
          const userSentence = userArr.join(" ").trim().replace(/\s+/g, ' ').toLowerCase();
          const targetSentence = (q.correct_answer || "").trim().replace(/\s+/g, ' ').toLowerCase();

          const isCorrect = (userSentence === targetSentence);
          pesertaAnswers[q.id] = userArr.join(" ");

          if (isCorrect) {
              totalEarnedScore += (100 / quizData.length);
              items.forEach(el => {
                  el.classList.remove("bg-[#243746]");
                  el.classList.add("bg-emerald-600");
              });
              playCorrectSound();
          } else {
              items.forEach(el => {
                  el.classList.remove("bg-[#243746]");
                  el.classList.add("bg-red-500");
              });
              playWrongSound();
          }

          setTimeout(() => {
              showModal(isCorrect ? 'correct' : 'wrong', q);
          }, 800);
      }

      function submitGroupingAnswer() {
          if (isAnswering) return;
          isAnswering = true;

          const q = quizData[currentQuestionIndex];
          const dropzones = document.querySelectorAll(".grouping-dropzone");
          
          let userGrouping = {};
          let targetGrouping = {};
          try {
              targetGrouping = JSON.parse(q.correct_answer);
          } catch(e) {
              targetGrouping = {};
          }

          let totalWords = 0;
          let correctWordsCount = 0;

          dropzones.forEach(dz => {
              const catName = dz.getAttribute("data-category");
              const items = dz.querySelectorAll(".grouping-item");
              let wordsInThisCat = [];

              items.forEach(item => {
                  wordsInThisCat.push(item.getAttribute("data-word").trim().toLowerCase());
              });

              userGrouping[catName] = wordsInThisCat;

              const targetWords = (targetGrouping[catName] || []).map(w => w.toLowerCase());
              totalWords += targetWords.length;

              items.forEach(item => {
                  const w = item.getAttribute("data-word").trim().toLowerCase();
                  if (targetWords.includes(w)) {
                      correctWordsCount++;
                      item.classList.add("bg-emerald-100", "text-emerald-800", "border-emerald-300");
                  } else {
                      item.classList.add("bg-red-100", "text-red-800", "border-red-300");
                  }
              });
          });

          pesertaAnswers[q.id] = JSON.stringify(userGrouping);

          let pointsPerQuestion = 100 / quizData.length;
          if (totalWords > 0) {
              totalEarnedScore += (correctWordsCount / totalWords) * pointsPerQuestion;
          }

          let allCorrect = (correctWordsCount === totalWords && totalWords > 0);
          let partialCorrect = (correctWordsCount > 0 && correctWordsCount < totalWords);

          if (allCorrect || partialCorrect) {
              playCorrectSound();
          } else {
              playWrongSound();
          }

          let status = allCorrect ? 'correct' : (partialCorrect ? 'partial' : 'wrong');
          setTimeout(() => { showModal(status, q); }, 800);
      }

      function submitLabelingAnswer() {
          if (isAnswering) return;
          isAnswering = true;

          const q = quizData[currentQuestionIndex];
          const canvas = document.getElementById("labeling-canvas");
          const pointers = canvas.querySelectorAll(".marlins-pointer");

          let targetMap = {};
          try {
              targetMap = JSON.parse(q.correct_answer);
          } catch(e) {
              targetMap = {};
          }

          let userResults = {};
          let totalLabels = Object.keys(targetMap).length;
          let correctCount = 0;
          const TOLERANCE_RADIUS = 14; // Toleransi radius 14% dari titik target

          pointers.forEach(el => {
              const labelName = el.getAttribute("data-label");
              const userX = parseFloat(el.getAttribute("data-pct-x")) || 0;
              const userY = parseFloat(el.getAttribute("data-pct-y")) || 0;

              userResults[labelName] = { x: userX, y: userY };

              if (targetMap[labelName]) {
                  const targetX = targetMap[labelName].x;
                  const targetY = targetMap[labelName].y;
                  const distance = Math.sqrt(Math.pow(userX - targetX, 2) + Math.pow(userY - targetY, 2));

                  if (distance <= TOLERANCE_RADIUS) {
                      correctCount++;
                      el.classList.add("correct");
                  } else {
                      el.classList.add("wrong");
                  }
              }
          });

          pesertaAnswers[q.id] = JSON.stringify(userResults);

          let pointsPerQuestion = 100 / quizData.length;
          if (totalLabels > 0) {
              totalEarnedScore += (correctCount / totalLabels) * pointsPerQuestion;
          }

          let allCorrect = (correctCount === totalLabels && totalLabels > 0);
          let partialCorrect = (correctCount > 0 && correctCount < totalLabels);

          if (allCorrect || partialCorrect) {
              playCorrectSound();
          } else {
              playWrongSound();
          }

          let status = allCorrect ? 'correct' : (partialCorrect ? 'partial' : 'wrong');
          setTimeout(() => { showModal(status, q); }, 800);
      }

      function showModal(status, currentQuestion) {
        const modal = document.getElementById("result-modal");
        const modalBox = modal.querySelector('div');
        const statusText = document.getElementById("modal-status");
        
        const expBox = document.getElementById("explanation-container");
        const btnPelajari = document.getElementById("btn-pelajari");

        expBox.classList.remove("flex");
        expBox.classList.add("hidden");

        if (status === 'correct') {
          statusText.innerText = "Benar Sempurna!";
          statusText.className = "text-lg font-black mb-1 text-emerald-500";
        } else if (status === 'partial') {
          statusText.innerText = "Benar Sebagian!";
          statusText.className = "text-lg font-black mb-1 text-amber-500";
        } else {
          statusText.innerText = "Kurang Tepat!";
          statusText.className = "text-lg font-black mb-1 text-red-500";
        }

        if (currentQuestion.is_show_explanation == 1 || currentQuestion.is_show_explanation == true) {
            btnPelajari.classList.remove("hidden");
            
            let expContent = `<p>${currentQuestion.explanation || "Tidak ada teks pembahasan spesifik."}</p>`;
            if (currentQuestion.explanation_link) {
                expContent += `<div class="mt-3"><a href="${currentQuestion.explanation_link}" target="_blank" class="inline-flex items-center text-indigo-700 bg-indigo-100 hover:bg-indigo-200 px-3 py-1.5 rounded-lg text-xs font-bold transition">🎥 Tonton Video Referensi</a></div>`;
            }
            document.getElementById("modal-explanation").innerHTML = expContent;
        } else {
            btnPelajari.classList.add("hidden");
        }
        
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        setTimeout(() => {
            modalBox.classList.remove("scale-80", "opacity-0");
            modalBox.classList.add("scale-100", "opacity-100", "animate-pop-in");
        }, 10);
      }

      function showExplanationContent() {
          const expBox = document.getElementById("explanation-container");
          expBox.classList.remove("hidden");
          expBox.classList.add("flex");
          expBox.scrollIntoView({ behavior: 'smooth' });
      }

      function nextQuestion() {
        const modal = document.getElementById("result-modal");
        const modalBox = modal.querySelector('div');
        
        modalBox.classList.remove("scale-100", "opacity-100", "animate-pop-in");
        modalBox.classList.add("scale-80", "opacity-0");
        
        setTimeout(() => {
            modal.classList.remove("flex");
            modal.classList.add("hidden");
            currentQuestionIndex++;
            
            if (currentQuestionIndex < quizData.length) {
              checkAndHandleSectionTransition(false);
            } else {
              processAutoSubmitAndShowResults();
            }
        }, 200);
      }

      function processAutoSubmitAndShowResults() {
          switchScreen("loading-screen");

          const finalScore = Math.round(totalEarnedScore);
          
          const formData = new FormData();
          formData.append('_token', '{{ csrf_token() }}');
          formData.append('final_score', finalScore);
          formData.append('peserta_answers', JSON.stringify(pesertaAnswers));

          fetch(document.getElementById("submit-form").action, {
              method: "POST",
              body: formData
          })
          .then(response => {
              renderCompletionModalView(finalScore);
          })
          .catch(error => {
              console.error("Gagal mengirim lembar jawaban:", error);
              renderCompletionModalView(finalScore);
          });
      }

      function toggleFullReviewList(btnElement) {
          const reviewList = document.getElementById("completion-review-list");
          const arrow = btnElement.querySelector('span:last-child');

          if (reviewList.classList.contains("hidden")) {
              reviewList.classList.remove("hidden");
              reviewList.classList.add("block", "animate-fade-in");
              btnElement.classList.replace("bg-amber-500", "bg-amber-600");
              btnElement.querySelector('span:first-child').innerText = "🙈 Tutup Review";
              arrow.style.transform = "rotate(180deg)";
          } else {
              reviewList.classList.remove("block", "animate-fade-in");
              reviewList.classList.add("hidden");
              btnElement.classList.replace("bg-amber-600", "bg-amber-500");
              btnElement.querySelector('span:first-child').innerText = "👁️ Lihat Review";
              arrow.style.transform = "rotate(0deg)";
          }
      }

      function togglePembahasan(idx) {
          const bahasDiv = document.getElementById(`bahas-${idx}`);
          const icon = document.getElementById(`icon-bahas-${idx}`);

          if (bahasDiv.classList.contains("hidden")) {
              bahasDiv.classList.remove("hidden");
              bahasDiv.classList.add("block", "animate-fade-in");
              icon.style.transform = "rotate(180deg)";
          } else {
              bahasDiv.classList.remove("block", "animate-fade-in");
              bahasDiv.classList.add("hidden");
              icon.style.transform = "rotate(0deg)";
          }
      }

      function renderCompletionModalView(finalScore) {
          const completionModal = document.getElementById("quiz-completion-modal");
          const completionModalBox = completionModal.querySelector('div');
          
          document.getElementById("completion-score-badge").innerText = finalScore;

          let descText = "";
          let userHighlight = `<strong class="text-indigo-600">${pesertaUsername}</strong>`;
          
          if (finalScore < 60) {
              descText = `Wah, ${userHighlight} kamu masih gagal :( , tingkatkan lagi ya!`;
          } else if (finalScore <= 80) {
              descText = `Nilai kamu cukup bagus ${userHighlight}, good job!`;
          } else if (finalScore <= 99) {
              descText = `Mendekati sempurna ${userHighlight}, very good!`;
          } else {
              descText = `Nilai sempurna ${userHighlight}, perfect! 🎉`;
          }
          
          document.getElementById("completion-desc-text").innerHTML = descText;

          const reviewListContainer = document.getElementById("completion-review-list");
          reviewListContainer.innerHTML = ""; 

          quizData.forEach((q, idx) => {
              const chosen = pesertaAnswers[q.id] || '';
              
              const questionCard = document.createElement("div");
              questionCard.className = "p-3 rounded-xl border border-gray-100 bg-gray-50/60 text-left text-xs leading-normal mb-2";
              
              let optionsHtml = '';
              let badgeLabel = '';
              let badgeClass = '';

              if (q.type === 'essay') {
                  let userAnsArr = [];
                  try { userAnsArr = JSON.parse(chosen); } catch(e) { userAnsArr = []; }
                  
                  let corrAnsArr = [];
                  try { corrAnsArr = JSON.parse(q.correct_answer); } catch(e) { corrAnsArr = [[q.correct_answer]]; }

                  let correctBlanksCount = 0;

                  optionsHtml += `<div class="p-2.5 bg-white border border-gray-100 rounded-lg space-y-2">`;
                  for(let i=0; i<corrAnsArr.length; i++) {
                      let u = (userAnsArr[i] || "").trim();
                      let validAliases = corrAnsArr[i] || [];
                      let isMatch = Array.isArray(validAliases) && validAliases.includes(u.toLowerCase());
                      
                      if(isMatch) correctBlanksCount++;

                      let displayKunci = Array.isArray(validAliases) ? validAliases.join(' / ') : validAliases;

                      let statusBadge = isMatch
                          ? `<span class="text-emerald-600 font-bold text-[10px] ml-1 bg-emerald-50 px-1 rounded">✓ Benar</span>`
                          : `<span class="text-red-500 font-bold text-[10px] ml-1 bg-red-50 px-1 rounded">✗ Salah (Kunci: ${displayKunci})</span>`;

                      optionsHtml += `<p class="text-[11px] text-gray-700"><strong>Isian ${i+1}:</strong> <span class="${isMatch ? 'text-emerald-600 font-bold' : 'text-red-500 line-through'}">${u || 'Kosong'}</span> ${statusBadge}</p>`;
                  }
                  optionsHtml += `</div>`;

                  if (correctBlanksCount === corrAnsArr.length && corrAnsArr.length > 0) {
                      badgeLabel = '✓ Benar';
                      badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                  } else if (correctBlanksCount > 0) {
                      badgeLabel = '◐ Sebagian';
                      badgeClass = 'bg-amber-50 text-amber-700 border border-amber-200';
                  } else {
                      badgeLabel = '✗ Salah';
                      badgeClass = 'bg-red-50 text-red-700 border border-red-200';
                  }
              } 
              else if (q.type === 'sorting') {
                  const userSentence = (chosen || "").trim().replace(/\s+/g, ' ').toLowerCase();
                  const targetSentence = (q.correct_answer || "").trim().replace(/\s+/g, ' ').toLowerCase();
                  const isCorrect = (userSentence === targetSentence);

                  if (isCorrect) {
                      badgeLabel = '✓ Benar';
                      badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                  } else {
                      badgeLabel = '✗ Salah';
                      badgeClass = 'bg-red-50 text-red-700 border border-red-200';
                  }

                  const kunciHtml = !isCorrect 
                      ? `<p class="text-emerald-700 font-semibold mt-1"><strong>Kunci Jawaban:</strong> ${q.correct_answer}</p>` 
                      : '';

                  optionsHtml = `
                      <div class="p-2.5 bg-white border border-gray-100 rounded-lg space-y-1.5 text-[11px]">
                          <p class="text-gray-600"><strong>Jawaban Kamu:</strong> <span class="${isCorrect ? 'text-emerald-600 font-bold' : 'text-red-600 line-through'}">${chosen || 'Belum diisi'}</span></p>
                          ${kunciHtml}
                      </div>
                  `;
              }
              else if (q.type === 'grouping') {
                  let userG = {};
                  let targetG = {};
                  try { userG = JSON.parse(chosen); } catch(e) { userG = {}; }
                  try { targetG = JSON.parse(q.correct_answer); } catch(e) { targetG = {}; }

                  let totalWords = 0;
                  let correctWordsCount = 0;

                  let groupDetailsHtml = '<div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-1.5">';
                  
                  Object.keys(targetG).forEach(cat => {
                      const tWords = (targetG[cat] || []).map(w => w.toLowerCase());
                      const uWords = (userG[cat] || []).map(w => w.toLowerCase());
                      totalWords += tWords.length;

                      let wordsBadges = '';
                      uWords.forEach(uw => {
                          if (tWords.includes(uw)) {
                              correctWordsCount++;
                              wordsBadges += `<span class="bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded text-[10px] font-bold">✓ ${uw}</span>`;
                          } else {
                              wordsBadges += `<span class="bg-red-100 text-red-800 px-1.5 py-0.5 rounded text-[10px] font-bold line-through">✗ ${uw}</span>`;
                          }
                      });

                      if (uWords.length === 0) {
                          wordsBadges = '<span class="text-gray-400 italic text-[10px]">Kosong</span>';
                      }

                      groupDetailsHtml += `
                          <div class="bg-gray-50 p-2 rounded-lg border border-gray-200">
                              <span class="font-bold text-gray-700 block uppercase text-[10px] mb-1 pb-0.5 border-b border-gray-200">${cat}</span>
                              <div class="flex flex-wrap gap-1">${wordsBadges}</div>
                              <p class="text-[9px] text-gray-400 mt-1.5">Kunci: ${targetG[cat].join(', ')}</p>
                          </div>
                      `;
                  });
                  groupDetailsHtml += '</div>';

                  if (correctWordsCount === totalWords && totalWords > 0) {
                      badgeLabel = '✓ Benar';
                      badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                  } else if (correctWordsCount > 0) {
                      badgeLabel = '◐ Sebagian';
                      badgeClass = 'bg-amber-50 text-amber-700 border border-amber-200';
                  } else {
                      badgeLabel = '✗ Salah';
                      badgeClass = 'bg-red-50 text-red-700 border border-red-200';
                  }

                  optionsHtml = `
                      <div class="p-2.5 bg-white border border-gray-100 rounded-lg space-y-1 text-[11px]">
                          <p class="text-gray-600 font-semibold">Hasil Pengelompokan Kategori:</p>
                          ${groupDetailsHtml}
                      </div>
                  `;
              }
              else if (q.type === 'labeling') {
                  let userL = {};
                  let targetL = {};
                  try { userL = JSON.parse(chosen); } catch(e) { userL = {}; }
                  try { targetL = JSON.parse(q.correct_answer); } catch(e) { targetL = {}; }

                  let totalL = Object.keys(targetL).length;
                  let correctLCount = 0;

                  let labelBadges = '';
                  Object.keys(targetL).forEach(lbl => {
                      const uCoord = userL[lbl];
                      const tCoord = targetL[lbl];
                      let isOk = false;

                      if (uCoord) {
                          const dist = Math.sqrt(Math.pow(uCoord.x - tCoord.x, 2) + Math.pow(uCoord.y - tCoord.y, 2));
                          if (dist <= 14) {
                              isOk = true;
                              correctLCount++;
                          }
                      }

                      labelBadges += isOk
                          ? `<span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full text-[10px] font-bold">✓ ${lbl}</span>`
                          : `<span class="bg-red-100 text-red-800 px-2 py-0.5 rounded-full text-[10px] font-bold line-through">✗ ${lbl}</span>`;
                  });

                  if (correctLCount === totalL && totalL > 0) {
                      badgeLabel = '✓ Benar';
                      badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                  } else if (correctLCount > 0) {
                      badgeLabel = '◐ Sebagian';
                      badgeClass = 'bg-amber-50 text-amber-700 border border-amber-200';
                  } else {
                      badgeLabel = '✗ Salah';
                      badgeClass = 'bg-red-50 text-red-700 border border-red-200';
                  }

                  optionsHtml = `
                      <div class="p-2.5 bg-white border border-gray-100 rounded-lg space-y-1.5 text-[11px]">
                          <p class="text-gray-600 font-semibold">Hasil Penempatan Label Titik:</p>
                          <div class="flex flex-wrap gap-1">${labelBadges}</div>
                      </div>
                  `;
              }
              else {
                  let isCorrect = (chosen === q.correct_answer);
                  if (isCorrect) {
                      badgeLabel = '✓ Benar';
                      badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                  } else {
                      badgeLabel = '✗ Salah';
                      badgeClass = 'bg-red-50 text-red-700 border border-red-200';
                  }
                  
                  Object.keys(q.options || {}).forEach((key) => {
                      let badgeText = '';
                      let textStyle = 'text-gray-500';
                      let optVal = q.options[key] || '';
                      
                      if (key === q.correct_answer) {
                          badgeText = ' <span class="text-emerald-600 font-bold text-[10px]">[Kunci]</span>';
                          textStyle = 'text-emerald-700 font-semibold';
                      } else if (key === chosen && !isCorrect) {
                          badgeText = ' <span class="text-red-500 font-bold text-[10px]">[Kamu]</span>';
                          textStyle = 'text-red-600 line-through';
                      }

                      let displayVal = isOptionImage(optVal)
                          ? `<img src="/storage/${optVal}" class="max-h-12 inline-block rounded border p-0.5" />`
                          : optVal;

                      optionsHtml += `<div class="pl-2 py-0.5 flex items-center ${textStyle}"><strong>${key}.</strong> &nbsp;${displayVal} ${badgeText}</div>`;
                  });
                  optionsHtml = `<div class="space-y-0.5 bg-white p-2 rounded-lg border border-gray-100/70 text-[11px]">${optionsHtml}</div>`;
              }

              let explanationSection = '';
              if (q.is_show_explanation == 1 || q.is_show_explanation == true) {
                  let expText = `<p>${q.explanation || "Pembahasan belum tersedia untuk soal ini."}</p>`;
                  if (q.explanation_link) {
                      expText += `<div class="mt-2"><a href="${q.explanation_link}" target="_blank" class="inline-flex items-center text-indigo-700 bg-indigo-100 hover:bg-indigo-200 px-2 py-1 rounded text-[10px] font-bold transition">🎥 Tonton Video Referensi</a></div>`;
                  }

                  explanationSection = `
                      <div class="mt-2">
                          <button onclick="togglePembahasan(${idx})" class="w-full text-left bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold py-1.5 px-2.5 rounded-lg border border-indigo-100 text-[11px] flex justify-between items-center transition duration-150 cursor-pointer">
                              <span>💡 Lihat Pembahasan</span>
                              <span id="icon-bahas-${idx}" class="transition-transform duration-200 inline-block">&#9662;</span>
                          </button>
                          <div id="bahas-${idx}" class="hidden mt-1.5 bg-indigo-50/40 p-2 rounded-lg border-l-2 border-indigo-400 transition-all duration-300">
                              <div class="text-[11px] text-gray-600 leading-relaxed">${expText}</div>
                          </div>
                      </div>
                  `;
              }

              questionCard.innerHTML = `
                  <div class="flex justify-between items-center border-b border-gray-200 pb-1.5 mb-1.5">
                      <span class="font-bold text-gray-400 text-[11px]">Soal #${idx + 1}</span>
                      <span class="px-1.5 py-0.5 rounded text-[9px] font-bold ${badgeClass}">
                          ${badgeLabel}
                      </span>
                  </div>
                  
                  <p class="font-bold text-gray-700 mb-1.5 text-xs">${q.type === 'essay' ? q.question_text.replace(/\[blank\]/g, '___') : q.question_text}</p>
                  
                  ${optionsHtml}
                  
                  ${explanationSection}
              `;

              reviewListContainer.appendChild(questionCard);
          });

          completionModal.classList.remove("hidden");
          completionModal.classList.add("flex");
          setTimeout(() => {
              completionModalBox.classList.remove("scale-80", "opacity-0");
              completionModalBox.classList.add("scale-100", "opacity-100");
          }, 10);
      }

      function redirectToDashboard() {
          window.location.href = "{{ route('peserta.dashboard') }}"; 
      }

      document.addEventListener("DOMContentLoaded", () => {
          switchScreen("start-screen");
      });
    </script>
  </body>
</html>