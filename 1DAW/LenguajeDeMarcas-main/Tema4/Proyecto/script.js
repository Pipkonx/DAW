// Variables globales
let currentLanguage = 'es';
let questions = [];
let currentQuestionIndex = 0;
let score = 0;
let timer;
let seconds = 0;
let userAnswers = [];
let xmlDoc;

// Elementos DOM
const startScreen = document.getElementById('start-screen');
const quizScreen = document.getElementById('quiz-screen');
const resultsScreen = document.getElementById('results-screen');
const questionNumber = document.getElementById('question-number');
const questionText = document.getElementById('question-text');
const choicesContainer = document.getElementById('choices');
const prevBtn = document.getElementById('prev-btn');
const nextBtn = document.getElementById('next-btn');
const submitBtn = document.getElementById('submit-btn');
const timerDisplay = document.getElementById('timer');
const progressDisplay = document.getElementById('progress');
const scoreDisplay = document.getElementById('score');
const resultsScore = document.getElementById('results-score');
const resultsTime = document.getElementById('results-time');
const resultsDetails = document.getElementById('results-details');
const restartBtn = document.getElementById('restart-btn');
const esBtn = document.getElementById('es-btn');
const enBtn = document.getElementById('en-btn');

// Textos en diferentes idiomas
const translations = {
    es: {
        mainTitle: 'Cuestionario de XML y DTD',
        subtitle: 'Pon a prueba tus conocimientos sobre XML y DTD',
        welcomeTitle: 'Bienvenido al Cuestionario de XML y DTD',
        instructions: 'Este cuestionario consta de 20 preguntas sobre XML y DTD. Selecciona la respuesta correcta para cada pregunta. Al final, verás tu puntuación total.',
        startBtn: 'Comenzar',
        prevBtn: 'Anterior',
        nextBtn: 'Siguiente',
        submitBtn: 'Finalizar',
        timerLabel: 'Tiempo:',
        progressLabel: 'Pregunta:',
        scoreLabel: 'Puntuación:',
        resultsTitle: 'Resultados del Cuestionario',
        resultsScore: 'Tu puntuación:',
        resultsTime: 'Tiempo empleado:',
        restartBtn: 'Reiniciar Cuestionario',
        footerText: 'Proyecto XML - Cuestionario Interactivo &copy; 2024',
        questionPrefix: 'Pregunta'
    },
    en: {
        mainTitle: 'XML and DTD Quiz',
        subtitle: 'Test your knowledge about XML and DTD',
        welcomeTitle: 'Welcome to the XML and DTD Quiz',
        instructions: 'This quiz consists of 20 questions about XML and DTD. Select the correct answer for each question. At the end, you will see your total score.',
        startBtn: 'Start',
        prevBtn: 'Previous',
        nextBtn: 'Next',
        submitBtn: 'Finish',
        timerLabel: 'Time:',
        progressLabel: 'Question:',
        scoreLabel: 'Score:',
        resultsTitle: 'Quiz Results',
        resultsScore: 'Your score:',
        resultsTime: 'Time spent:',
        restartBtn: 'Restart Quiz',
        footerText: 'XML Project - Interactive Quiz &copy; 2024',
        questionPrefix: 'Question'
    }
};

// Inicializar la aplicación
document.addEventListener('DOMContentLoaded', init);

function init() {
    // Configurar eventos de botones
    document.getElementById('start-btn').addEventListener('click', startQuiz);
    prevBtn.addEventListener('click', showPreviousQuestion);
    nextBtn.addEventListener('click', showNextQuestion);
    submitBtn.addEventListener('click', submitQuiz);
    restartBtn.addEventListener('click', restartQuiz);
    esBtn.addEventListener('click', () => changeLanguage('es'));
    enBtn.addEventListener('click', () => changeLanguage('en'));

    // Cargar el idioma inicial
    updateUILanguage();
}

// Cambiar idioma
function changeLanguage(lang) {
    if (currentLanguage === lang) return;
    
    currentLanguage = lang;
    
    // Actualizar clases de botones
    if (lang === 'es') {
        esBtn.classList.add('active');
        enBtn.classList.remove('active');
    } else {
        esBtn.classList.remove('active');
        enBtn.classList.add('active');
    }
    
    // Actualizar textos de la interfaz
    updateUILanguage();
    
    // Si el cuestionario ya ha comenzado, cargar las preguntas en el nuevo idioma
    if (quizScreen.style.display === 'block') {
        loadQuestions();
    }
}

// Actualizar textos de la interfaz según el idioma
function updateUILanguage() {
    const texts = translations[currentLanguage];
    
    document.getElementById('main-title').textContent = texts.mainTitle;
    document.getElementById('subtitle').textContent = texts.subtitle;
    document.getElementById('welcome-title').textContent = texts.welcomeTitle;
    document.getElementById('instructions').textContent = texts.instructions;
    document.getElementById('start-btn').textContent = texts.startBtn;
    prevBtn.textContent = texts.prevBtn;
    nextBtn.textContent = texts.nextBtn;
    submitBtn.textContent = texts.submitBtn;
    document.getElementById('timer-label').textContent = texts.timerLabel;
    document.getElementById('progress-label').textContent = texts.progressLabel;
    document.getElementById('score-label').textContent = texts.scoreLabel;
    document.getElementById('results-title').textContent = texts.resultsTitle;
    document.getElementById('footer-text').textContent = texts.footerText;
    
    // Actualizar textos que contienen valores dinámicos
    updateDynamicTexts();
}

// Actualizar textos que contienen valores dinámicos
function updateDynamicTexts() {
    const texts = translations[currentLanguage];
    
    if (questions.length > 0) {
        questionNumber.textContent = `${texts.questionPrefix} ${currentQuestionIndex + 1}`;
        progressDisplay.textContent = `${currentQuestionIndex + 1}/${questions.length}`;
    }
    
    resultsScore.textContent = `${texts.resultsScore} ${score}/${questions.length}`;
    resultsTime.textContent = `${texts.resultsTime} ${formatTime(seconds)}`;
}

// Iniciar el cuestionario
function startQuiz() {
    startScreen.style.display = 'none';
    quizScreen.style.display = 'block';
    
    // Cargar preguntas
    loadQuestions();
    
    // Iniciar temporizador
    startTimer();
}

// Cargar preguntas desde el archivo XML
function loadQuestions() {
    const xhr = new XMLHttpRequest();
    const xmlFile = currentLanguage === 'es' ? 'preguntas_es.xml' : 'preguntas_en.xml';
    
    xhr.onload = function() {
        if (this.status === 200) {
            xmlDoc = this.responseXML;
            parseQuestions();
            showQuestion(0);
        }
    };
    
    xhr.open('GET', xmlFile);
    xhr.send();
}

// Analizar preguntas desde el documento XML
function parseQuestions() {
    questions = [];
    const questionElements = xmlDoc.getElementsByTagName('question');
    
    for (let i = 0; i < questionElements.length; i++) {
        const questionElement = questionElements[i];
        const id = questionElement.getAttribute('id');
        const wording = questionElement.getElementsByTagName('wording')[0].textContent;
        const choiceElements = questionElement.getElementsByTagName('choice');
        const choices = [];
        let correctIndex = -1;
        
        for (let j = 0; j < choiceElements.length; j++) {
            const choiceElement = choiceElements[j];
            const text = choiceElement.textContent;
            const isCorrect = choiceElement.getAttribute('correct') === 'yes';
            
            choices.push(text);
            if (isCorrect) {
                correctIndex = j;
            }
        }
        
        questions.push({
            id,
            wording,
            choices,
            correctIndex
        });
    }
    
    // Inicializar respuestas del usuario
    userAnswers = new Array(questions.length).fill(-1);
}

// Mostrar pregunta actual
function showQuestion(index) {
    if (index < 0 || index >= questions.length) return;
    
    currentQuestionIndex = index;
    const question = questions[index];
    const texts = translations[currentLanguage];
    
    // Actualizar número de pregunta y texto
    questionNumber.textContent = `${texts.questionPrefix} ${index + 1}`;
    questionText.textContent = question.wording;
    progressDisplay.textContent = `${index + 1}/${questions.length}`;
    
    // Generar opciones
    choicesContainer.innerHTML = '';
    question.choices.forEach((choice, i) => {
        const choiceElement = document.createElement('div');
        choiceElement.className = 'choice';
        choiceElement.textContent = choice;
        
        // Marcar la opción seleccionada por el usuario
        if (userAnswers[index] === i) {
            choiceElement.classList.add('selected');
        }
        
        choiceElement.addEventListener('click', () => selectChoice(i));
        choicesContainer.appendChild(choiceElement);
    });
    
    // Actualizar estado de botones
    prevBtn.disabled = index === 0;
    
    if (index === questions.length - 1) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'block';
    } else {
        nextBtn.style.display = 'block';
        submitBtn.style.display = 'none';
    }
}

// Seleccionar una opción
function selectChoice(choiceIndex) {
    userAnswers[currentQuestionIndex] = choiceIndex;
    
    // Actualizar visualización
    const choices = choicesContainer.querySelectorAll('.choice');
    choices.forEach((choice, i) => {
        if (i === choiceIndex) {
            choice.classList.add('selected');
        } else {
            choice.classList.remove('selected');
        }
    });
    
    // Actualizar puntuación
    updateScore();
}

// Mostrar pregunta anterior
function showPreviousQuestion() {
    if (currentQuestionIndex > 0) {
        showQuestion(currentQuestionIndex - 1);
    }
}

// Mostrar siguiente pregunta
function showNextQuestion() {
    if (currentQuestionIndex < questions.length - 1) {
        showQuestion(currentQuestionIndex + 1);
    }
}

// Iniciar temporizador
function startTimer() {
    seconds = 0;
    timerDisplay.textContent = formatTime(seconds);
    
    timer = setInterval(() => {
        seconds++;
        timerDisplay.textContent = formatTime(seconds);
    }, 1000);
}

// Formatear tiempo en formato mm:ss
function formatTime(totalSeconds) {
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

// Actualizar puntuación
function updateScore() {
    score = 0;
    
    for (let i = 0; i < questions.length; i++) {
        if (userAnswers[i] === questions[i].correctIndex) {
            score++;
        }
    }
    
    scoreDisplay.textContent = `${score}/${questions.length}`;
}

// Finalizar cuestionario
function submitQuiz() {
    // Detener temporizador
    clearInterval(timer);
    
    // Actualizar puntuación final
    updateScore();
    
    // Mostrar pantalla de resultados
    quizScreen.style.display = 'none';
    resultsScreen.style.display = 'block';
    
    // Actualizar textos de resultados
    const texts = translations[currentLanguage];
    resultsScore.textContent = `${texts.resultsScore} ${score}/${questions.length}`;
    resultsTime.textContent = `${texts.resultsTime} ${formatTime(seconds)}`;
    
    // Generar detalles de resultados
    generateResultsDetails();
}

// Generar detalles de resultados
function generateResultsDetails() {
    resultsDetails.innerHTML = '';
    
    for (let i = 0; i < questions.length; i++) {
        const question = questions[i];
        const userAnswer = userAnswers[i];
        const isCorrect = userAnswer === question.correctIndex;
        
        const resultItem = document.createElement('div');
        resultItem.className = `result-item ${isCorrect ? 'correct' : 'incorrect'}`;
        
        const questionText = document.createElement('p');
        questionText.textContent = `${i + 1}. ${question.wording}`;
        resultItem.appendChild(questionText);
        
        const userAnswerText = document.createElement('p');
        userAnswerText.innerHTML = userAnswer === -1 
            ? '<strong>Sin respuesta</strong>' 
            : `Tu respuesta: <strong>${question.choices[userAnswer]}</strong>`;
        resultItem.appendChild(userAnswerText);
        
        if (!isCorrect && userAnswer !== -1) {
            const correctAnswerText = document.createElement('p');
            correctAnswerText.innerHTML = `Respuesta correcta: <strong>${question.choices[question.correctIndex]}</strong>`;
            resultItem.appendChild(correctAnswerText);
        }
        
        resultsDetails.appendChild(resultItem);
    }
}

// Reiniciar cuestionario
function restartQuiz() {
    // Reiniciar variables
    currentQuestionIndex = 0;
    score = 0;
    userAnswers = [];
    
    // Mostrar pantalla de inicio
    resultsScreen.style.display = 'none';
    startScreen.style.display = 'block';
    
    // Actualizar textos
    updateUILanguage();
}