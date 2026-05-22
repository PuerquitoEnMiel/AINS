@extends('layouts.app')

@section('header-title', 'EdTech Certification Quiz')
@section('header-subtitle', 'Demonstrate your skills to earn the micro-credential')

@section('content')
<!-- Canvas Confetti CDN -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<div class="max-w-3xl mx-auto" id="quiz-container">
    <!-- Quiz Card -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden relative">
        <!-- Top header gradient -->
        <div class="h-4 bg-gradient-to-r from-ans-dark-green via-ans-orange to-ans-light-green"></div>

        <!-- Introduction Screen -->
        <div id="intro-screen" class="p-8 md:p-10 text-center">
            <span class="text-6xl mb-4 block animate-bounce" style="animation-duration: 3s;">{{ $badge->icon }}</span>
            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-500 mb-2 inline-block">
                {{ str_replace('_', ' ', $badge->category) }}
            </span>
            <h3 class="text-2xl font-heading font-extrabold text-gray-800 mb-3">{{ $quiz->title }}</h3>
            <p class="text-sm text-gray-500 max-w-md mx-auto mb-6">{{ $quiz->description ?? 'Complete this quiz to unlock the badge.' }}</p>

            <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto mb-8">
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Passing Score</p>
                    <p class="text-lg font-heading font-extrabold text-ans-dark-green">{{ $quiz->passing_score }}%</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Questions</p>
                    <p class="text-lg font-heading font-extrabold text-ans-orange">{{ count($quiz->questions) }}</p>
                </div>
            </div>

            <button onclick="startQuiz()" class="w-full sm:w-auto px-8 py-3.5 bg-ans-dark-green text-white font-bold rounded-2xl hover:bg-ans-seal-green transition-all shadow-md">
                Start Certification Quiz
            </button>
        </div>

        <!-- Question Wizard Screen (Hidden initially) -->
        <div id="wizard-screen" class="hidden">
            <!-- Progress Row -->
            <div class="px-8 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-500">Question</span>
                    <span class="text-xs font-extrabold text-ans-dark-green" id="question-indicator">1/5</span>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                    <svg class="w-4 h-4 text-ans-orange animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span id="timer">00:00</span>
                </div>
            </div>

            <!-- Quiz Questions Inner Container -->
            <div class="p-8 md:p-10">
                <!-- Question Title -->
                <h4 class="font-heading font-extrabold text-gray-800 text-lg mb-6 leading-snug" id="question-text">
                    Question text goes here
                </h4>

                <!-- Options -->
                <div class="space-y-3 mb-8" id="options-container">
                    <!-- Options buttons will be injected here -->
                </div>

                <!-- Footer Actions -->
                <div class="flex justify-between items-center">
                    <button onclick="prevQuestion()" id="prev-btn" class="px-5 py-2.5 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-all text-xs disabled:opacity-50" disabled>
                        Previous
                    </button>
                    <button onclick="nextQuestion()" id="next-btn" class="px-6 py-2.5 bg-ans-dark-green text-white font-bold rounded-xl hover:bg-ans-seal-green transition-all text-xs">
                        Next Question
                    </button>
                </div>
            </div>
        </div>

        <!-- Results / Graded Screen (Hidden initially) -->
        <div id="result-screen" class="hidden p-8 md:p-10 text-center">
            <div id="result-graphic" class="text-7xl mb-4">🎉</div>
            <h3 class="text-2xl font-heading font-extrabold text-gray-800 mb-2" id="result-title">Congratulations!</h3>
            <p class="text-sm text-gray-500 max-w-md mx-auto mb-6" id="result-text">You have successfully unlocked the certification badge.</p>

            <div class="flex justify-center items-center gap-4 mb-8">
                <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100 text-center min-w-[120px]">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Your Score</p>
                    <p class="text-2xl font-heading font-extrabold text-ans-dark-green" id="result-score">90%</p>
                </div>
                <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100 text-center min-w-[120px]">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Correct Answers</p>
                    <p class="text-2xl font-heading font-extrabold text-ans-orange" id="result-correct">4 / 5</p>
                </div>
            </div>

            <!-- Detailed Explanations section -->
            <div class="text-left border-t border-gray-100 pt-6 mt-6 max-h-96 overflow-y-auto space-y-4" id="explanations-container">
                <h4 class="font-heading font-bold text-gray-800 text-sm mb-3">Review Answers</h4>
                <!-- Dynamic grading list here -->
            </div>

            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-3">
                <a href="{{ route('badges.show', $badge->slug) }}" id="finish-btn" class="px-6 py-3 bg-ans-dark-green text-white font-bold rounded-xl hover:bg-ans-seal-green transition-all shadow-md text-xs">
                    Return to Badge
                </a>
                <button onclick="restartQuiz()" id="retry-btn" class="hidden px-6 py-3 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all text-xs">
                    Retry Quiz
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const questions = @json($quiz->questions);
    const totalQuestions = questions.length;
    let currentQuestionIndex = 0;
    let userAnswers = new Array(totalQuestions).fill(null);
    
    // Timer state
    let secondsElapsed = 0;
    let timerInterval = null;

    function formatTime(secs) {
        const mins = Math.floor(secs / 60);
        const remainingSecs = secs % 60;
        return `${mins.toString().padStart(2, '0')}:${remainingSecs.toString().padStart(2, '0')}`;
    }

    function startTimer() {
        secondsElapsed = 0;
        document.getElementById('timer').innerText = formatTime(secondsElapsed);
        timerInterval = setInterval(() => {
            secondsElapsed++;
            document.getElementById('timer').innerText = formatTime(secondsElapsed);
        }, 1000);
    }

    function stopTimer() {
        if (timerInterval) {
            clearInterval(timerInterval);
        }
    }

    function startQuiz() {
        document.getElementById('intro-screen').classList.add('hidden');
        document.getElementById('wizard-screen').classList.remove('hidden');
        currentQuestionIndex = 0;
        userAnswers.fill(null);
        renderQuestion();
        startTimer();
    }

    function renderQuestion() {
        const question = questions[currentQuestionIndex];
        
        // Update Indicator
        document.getElementById('question-indicator').innerText = `${currentQuestionIndex + 1}/${totalQuestions}`;
        
        // Update Question text
        document.getElementById('question-text').innerText = question.question;
        
        // Update Prev Button status
        document.getElementById('prev-btn').disabled = (currentQuestionIndex === 0);
        
        // Update Next Button text
        const nextBtn = document.getElementById('next-btn');
        if (currentQuestionIndex === totalQuestions - 1) {
            nextBtn.innerText = "Submit Assessment";
            nextBtn.classList.remove('bg-ans-dark-green', 'hover:bg-ans-seal-green');
            nextBtn.classList.add('bg-ans-orange', 'hover:bg-ans-orange/90');
        } else {
            nextBtn.innerText = "Next Question";
            nextBtn.classList.remove('bg-ans-orange', 'hover:bg-ans-orange/90');
            nextBtn.classList.add('bg-ans-dark-green', 'hover:bg-ans-seal-green');
        }
        
        // Options rendering
        const optionsContainer = document.getElementById('options-container');
        optionsContainer.innerHTML = '';
        
        const selectedOption = userAnswers[currentQuestionIndex];
        
        Object.keys(question.options).forEach(key => {
            const val = question.options[key];
            const isSelected = (selectedOption === key);
            
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.onclick = () => selectOption(key);
            
            btn.className = `w-full p-4 rounded-2xl border text-left flex items-start gap-3 transition-all ${
                isSelected 
                ? 'border-ans-dark-green bg-ans-dark-green/5 shadow-inner ring-2 ring-ans-dark-green/10' 
                : 'border-gray-200 bg-white hover:bg-gray-50/50'
            }`;
            
            btn.innerHTML = `
                <span class="w-6 h-6 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 ${
                    isSelected 
                    ? 'bg-ans-dark-green text-white' 
                    : 'bg-gray-100 text-gray-500'
                }">${key.toUpperCase()}</span>
                <span class="text-sm font-medium ${isSelected ? 'text-ans-dark-green' : 'text-gray-700'}">${val}</span>
            `;
            
            optionsContainer.appendChild(btn);
        });
    }

    function selectOption(key) {
        userAnswers[currentQuestionIndex] = key;
        renderQuestion();
    }

    function prevQuestion() {
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
            renderQuestion();
        }
    }

    function nextQuestion() {
        // Validate that an answer is selected
        if (!userAnswers[currentQuestionIndex]) {
            alert('Please select an option before continuing.');
            return;
        }

        if (currentQuestionIndex < totalQuestions - 1) {
            currentQuestionIndex++;
            renderQuestion();
        } else {
            // Submit
            submitAnswers();
        }
    }

    function submitAnswers() {
        stopTimer();
        
        // Show loading state
        const nextBtn = document.getElementById('next-btn');
        nextBtn.disabled = true;
        nextBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline-block" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Grading...
        `;

        fetch("{{ route('quizzes.submit', $quiz) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                answers: userAnswers
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderResults(data);
            } else {
                alert('An error occurred while grading the quiz.');
                nextBtn.disabled = false;
                nextBtn.innerText = "Submit Assessment";
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network error. Please try again.');
            nextBtn.disabled = false;
            nextBtn.innerText = "Submit Assessment";
        });
    }

    function renderResults(data) {
        document.getElementById('wizard-screen').classList.add('hidden');
        document.getElementById('result-screen').classList.remove('hidden');
        
        // Score values
        document.getElementById('result-score').innerText = `${data.score}%`;
        document.getElementById('result-correct').innerText = `${data.correct_count} / ${data.total_questions}`;
        
        const passed = data.passed;
        const resultGraphic = document.getElementById('result-graphic');
        const resultTitle = document.getElementById('result-title');
        const resultText = document.getElementById('result-text');
        const finishBtn = document.getElementById('finish-btn');
        const retryBtn = document.getElementById('retry-btn');
        
        if (passed) {
            resultGraphic.innerText = '🏆';
            resultTitle.className = "text-2xl font-heading font-extrabold text-emerald-600 mb-2";
            resultTitle.innerText = "Assessment Approved!";
            resultText.innerText = "Fantastic job! You answered enough questions correctly to earn your EdTech badge.";
            retryBtn.classList.add('hidden');
            
            // Confetti explosion!
            confetti({
                particleCount: 150,
                spread: 80,
                origin: { y: 0.6 }
            });
        } else {
            resultGraphic.innerText = '📚';
            resultTitle.className = "text-2xl font-heading font-extrabold text-ans-orange mb-2";
            resultTitle.innerText = "Keep Learning!";
            resultText.innerText = "You did not reach the passing score of " + {{ $quiz->passing_score }} + "%. Review the concepts and try again!";
            retryBtn.classList.remove('hidden');
        }
        
        // Explanations list
        const expContainer = document.getElementById('explanations-container');
        expContainer.innerHTML = '<h4 class="font-heading font-bold text-gray-800 text-sm mb-3">Review Questions & Explanations</h4>';
        
        data.graded.forEach((q, idx) => {
            const expDiv = document.createElement('div');
            expDiv.className = `p-4 rounded-xl border ${q.is_correct ? 'border-emerald-100 bg-emerald-50/20' : 'border-rose-100 bg-rose-50/20'}`;
            
            const userOptionText = q.options[q.user_answer] || 'None';
            const correctOptionText = q.options[q.correct];
            
            expDiv.innerHTML = `
                <div class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full shrink-0 flex items-center justify-center font-bold text-[10px] mt-0.5 ${
                        q.is_correct ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white'
                    }">
                        ${q.is_correct ? '✓' : '✗'}
                    </span>
                    <div>
                        <h5 class="text-xs font-bold text-gray-800 leading-snug mb-2">${idx + 1}. ${q.question}</h5>
                        <p class="text-[11px] text-gray-600 mb-1">
                            Your answer: <span class="font-semibold ${q.is_correct ? 'text-emerald-700' : 'text-rose-700'}">${q.user_answer.toUpperCase()}) ${userOptionText}</span>
                        </p>
                        ${!q.is_correct ? `
                            <p class="text-[11px] text-gray-600 mb-1">
                                Correct answer: <span class="font-semibold text-emerald-700">${q.correct.toUpperCase()}) ${correctOptionText}</span>
                            </p>
                        ` : ''}
                        @if($quiz->questions[0]['explanation'] ?? false)
                            <p class="text-[10px] text-gray-500 mt-2 bg-white/50 p-2 rounded-lg border border-gray-100 leading-relaxed italic">
                                💡 <strong>Explanation:</strong> ${q.explanation}
                            </p>
                        @endif
                    </div>
                </div>
            `;
            expContainer.appendChild(expDiv);
        });
    }

    function restartQuiz() {
        document.getElementById('result-screen').classList.add('hidden');
        document.getElementById('wizard-screen').classList.remove('hidden');
        const nextBtn = document.getElementById('next-btn');
        nextBtn.disabled = false;
        nextBtn.innerText = "Next Question";
        currentQuestionIndex = 0;
        userAnswers.fill(null);
        renderQuestion();
        startTimer();
    }
</script>
@endsection
