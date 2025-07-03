<script setup lang="ts">
import {ref, computed} from 'vue';

interface Question {
    id: number;
    title: string;
    type: string;
    content: any;
    answer?: any;
}

interface Bloc {
    id: number;
    name: string;
    questions: Question[];
}

interface QuizProps {
    quizz: {
        id: number;
        title: string;
        subject: string;
        blocs: Bloc[];
    }
}

const props = defineProps<QuizProps>();

// État du quiz
const currentBlocIndex = ref(0);
const currentQuestionIndex = ref(0);
const userAnswers = ref({});
const quizFinished = ref(false);
const showResults = ref(false); // Nouvel état pour afficher les résultats

// Pour les réponses des différents types de questions
const shortAnswer = ref('');
const longAnswer = ref('');
const blankAnswers = ref([]);

// Calcul des blocs et questions courantes
const currentBloc = computed(() => {
    return props.quizz.blocs[currentBlocIndex.value] || null;
});

const currentQuestion = computed(() => {
    if (!currentBloc.value || !currentBloc.value.questions) return null;
    return currentBloc.value.questions[currentQuestionIndex.value] || null;
});

const totalQuestions = computed(() => {
    return props.quizz.blocs.reduce((total, bloc) => total + bloc.questions.length, 0);
});

const questionsAnswered = computed(() => {
    return Object.keys(userAnswers.value).length;
});

const progress = computed(() => {
    return (questionsAnswered.value / totalQuestions.value) * 100;
});

// Calcul du score total
const totalScore = computed(() => {
    let correctAnswers = 0;
    let scorePercentage = 0;

    props.quizz.blocs.forEach(bloc => {
        bloc.questions.forEach(question => {
            if (isAnswerCorrect(question)) {
                correctAnswers++;
            }
        });
    });

    if (totalQuestions.value > 0) {
        scorePercentage = Math.round((correctAnswers / totalQuestions.value) * 100);
    }

    return {
        correct: correctAnswers,
        total: totalQuestions.value,
        percentage: scorePercentage
    };
});

// Organisation des réponses par bloc pour l'affichage des résultats
const resultsByBloc = computed(() => {
    const results = [];

    props.quizz.blocs.forEach(bloc => {
        const blocQuestions = bloc.questions.map(question => {
            return {
                ...question,
                userAnswer: userAnswers.value[question.id] || null,
                isCorrect: isAnswerCorrect(question)
            };
        });

        results.push({
            ...bloc,
            questions: blocQuestions
        });
    });

    return results;
});

// Fonctions pour gérer les réponses et la navigation
function saveAnswer(answer) {
    if (!currentQuestion.value) return;

    userAnswers.value[currentQuestion.value.id] = answer;

    // Réinitialiser les champs de réponse
    shortAnswer.value = '';
    longAnswer.value = '';
    blankAnswers.value = [];

    goToNextQuestion();
}

function goToNextQuestion() {
    const currentBlocQuestions = currentBloc.value?.questions || [];

    if (currentQuestionIndex.value < currentBlocQuestions.length - 1) {
        // Passer à la question suivante dans ce bloc
        currentQuestionIndex.value++;
    } else if (currentBlocIndex.value < props.quizz.blocs.length - 1) {
        // Passer au bloc suivant
        currentBlocIndex.value++;
        currentQuestionIndex.value = 0;
    } else {
        // Quiz terminé
        finishQuiz();
    }
}

function finishQuiz() {
    quizFinished.value = true;
}

function viewResults() {
    showResults.value = true;
}

function retakeQuiz() {
    // Réinitialiser le quiz
    currentBlocIndex.value = 0;
    currentQuestionIndex.value = 0;
    userAnswers.value = {};
    quizFinished.value = false;
    showResults.value = false;
}

function extractBlanks(text) {
    const matches = text.match(/\[blank\]/g) || [];
    return matches;
}

function formatBlankText(text) {
    let index = 0;
    return text.replace(/\[blank\]/g, () => {
        index++;
        return `<span class="blank-placeholder">Blanc #${index}</span>`;
    });
}

function submitBlankAnswers() {
    saveAnswer({blanks: [...blankAnswers.value]});
}

function isAnswerCorrect(question) {
    const userAnswer = userAnswers.value[question.id];
    if (!userAnswer || !question.answer) return false;

    try {
        switch (question.type) {
            case 'multiple_choice':
                // La structure dans QuestionResource utilise answer.correct_options
                if (!userAnswer.selected || !question.answer.correct_options) return false;

                // Normaliser les tableaux
                const selected = Array.isArray(userAnswer.selected) ? userAnswer.selected : [userAnswer.selected];
                const correct = Array.isArray(question.answer.correct_options)
                    ? question.answer.correct_options
                    : [question.answer.correct_options];

                return arraysEqual(selected, correct);

            case 'true_false':
                // Vérifier si les booléens correspondent
                return userAnswer.correct === question.answer.correct;

            case 'short_answer':
            case 'long_answer':
                // Dans QuestionResource, c'est answer.correct_answer
                if (!userAnswer.answer || !question.answer.correct_answer) return false;

                const userText = userAnswer.answer.trim().toLowerCase();
                const correctText = question.answer.correct_answer.trim().toLowerCase();

                return userText === correctText;

            case 'fill_in_the_blank':
                // Dans QuestionResource, les blancs sont dans un tableau d'objets avec une propriété 'value'
                if (!userAnswer.blanks || !question.answer.blanks) return false;
                if (userAnswer.blanks.length !== question.answer.blanks.length) return false;

                return userAnswer.blanks.every((answer, index) => {
                    const userBlank = answer.trim().toLowerCase();
                    // Extraire la valeur de l'objet si c'est un objet
                    const correctBlank = typeof question.answer.blanks[index] === 'object'
                        ? question.answer.blanks[index].value.trim().toLowerCase()
                        : question.answer.blanks[index].trim().toLowerCase();

                    return userBlank === correctBlank;
                });

            default:
                return false;
        }
    } catch (error) {
        console.error("Erreur lors de la vérification de la réponse:", error, question);
        return false;
    }
}

// Fonction utilitaire pour comparer deux tableaux - CORRIGÉE
function arraysEqual(a, b) {
    // S'assurer que a et b sont des tableaux
    if (!Array.isArray(a) || !Array.isArray(b)) return false;

    // Si les longueurs sont différentes, les tableaux ne sont pas égaux
    if (a.length !== b.length) return false;

    // Convertir les éléments en chaînes pour comparaison
    const normalizeArray = arr => arr.map(item => String(item).trim());

    // Trier les tableaux normalisés
    const sortedA = [...normalizeArray(a)].sort();
    const sortedB = [...normalizeArray(b)].sort();

    // Comparer chaque élément
    return sortedA.every((val, idx) => val === sortedB[idx]);
}

// Fonction d'aide pour formater l'affichage des réponses dans les résultats
function formatAnswerDisplay(question) {
    const userAnswer = userAnswers.value[question.id];
    if (!userAnswer) return "Non répondu";

    switch (question.type) {
        case 'multiple_choice':
            return userAnswer.selected
                ? (Array.isArray(userAnswer.selected) ? userAnswer.selected : [userAnswer.selected])
                    .map(idx => question.content.options[idx]?.text || 'Option invalide').join(', ')
                : "Non sélectionné";
        case 'true_false':
            return userAnswer.correct === true ? "Vrai" : "Faux";
        case 'short_answer':
        case 'long_answer':
            return userAnswer.answer || "Sans réponse";
        case 'fill_in_the_blank':
            return userAnswer.blanks ? userAnswer.blanks.join(' | ') : "Sans réponse";
        default:
            return "Format non reconnu";
    }
}

// Fonction pour formater l'affichage des bonnes réponses
function formatCorrectAnswerDisplay(question) {
    if (!question.answer) return "Pas de réponse correcte définie";

    switch (question.type) {
        case 'multiple_choice':
            return question.answer.correct_options
                ? (Array.isArray(question.answer.correct_options) ? question.answer.correct_options : [question.answer.correct_options])
                    .map(idx => question.content.options[idx]?.text || 'Option invalide').join(', ')
                : "Non défini";
        case 'true_false':
            return question.answer.correct === true ? "Vrai" : "Faux";
        case 'short_answer':
        case 'long_answer':
            // Utiliser correct_answer au lieu de answer
            return question.answer.correct_answer || "Non défini";
        case 'fill_in_the_blank':
            // Gérer les objets avec propriété value
            return question.answer.blanks
                ? question.answer.blanks.map(blank =>
                    typeof blank === 'object' ? blank.value : blank
                ).join(' | ')
                : "Non défini";
        default:
            return "Format non reconnu";
    }
}

function getScoreClass(percentage) {
    if (percentage >= 90) return 'score-excellent';
    if (percentage >= 70) return 'score-good';
    if (percentage >= 50) return 'score-average';
    return 'score-poor';
}
</script>

<template>
    <div class="quiz-container">
        <!-- En-tête du quiz -->
        <div class="quiz-header">
            <h1 class="quiz-title">{{ quizz.title }}</h1>
            <div class="quiz-info">
                <span class="subject">{{ quizz.subject }}</span>
                <div class="progress-container">
                    <div class="progress-bar" :style="`width: ${progress}%`"></div>
                </div>
                <span class="progress-text">{{ questionsAnswered }} / {{ totalQuestions }}</span>
            </div>
        </div>

        <!-- Contenu du quiz -->
        <div v-if="!quizFinished && !showResults" class="quiz-content">
            <div v-if="currentBloc && currentQuestion" class="question-container">
                <!-- Le contenu existant pour les questions -->
                <div class="bloc-info">
                    <h2 class="bloc-name">{{ currentBloc.name }}</h2>
                </div>

                <div class="question">
                    <h3 class="question-title">{{ currentQuestion.title }}</h3>

                    <!-- MULTIPLE_CHOICE -->
                    <div v-if="currentQuestion.type === 'multiple_choice'" class="question-options">
                        <div
                            v-for="(option, index) in currentQuestion.content.options"
                            :key="index"
                            class="option"
                            @click="saveAnswer({ selected: [index] })"
                        >
                            {{ option.text }}
                        </div>
                    </div>

                    <!-- TRUE_FALSE -->
                    <div v-else-if="currentQuestion.type === 'true_false'" class="question-true-false">
                        <button @click="saveAnswer({ correct: true })" class="true-button">Vrai</button>
                        <button @click="saveAnswer({ correct: false })" class="false-button">Faux</button>
                    </div>

                    <!-- FILL_IN_THE_BLANK -->
                    <div v-else-if="currentQuestion.type === 'fill_in_the_blank'" class="question-fill-blank">
                        <div class="question-text" v-html="formatBlankText(currentQuestion.content.text)"></div>
                        <div class="blanks-container">
                            <div v-for="(_, index) in extractBlanks(currentQuestion.content.text)" :key="index"
                                 class="blank-input">
                                <label>Blanc #{{ index + 1 }}</label>
                                <input
                                    v-model="blankAnswers[index]"
                                    type="text"
                                    :placeholder="`Réponse ${index + 1}...`"
                                />
                            </div>
                            <button @click="submitBlankAnswers" class="next-button">Valider</button>
                        </div>
                    </div>

                    <!-- SHORT_ANSWER -->
                    <div v-else-if="currentQuestion.type === 'short_answer'" class="question-short-answer">
                        <div class="question-text">{{ currentQuestion.content.question }}</div>
                        <input
                            v-model="shortAnswer"
                            type="text"
                            placeholder="Votre réponse courte..."
                            class="short-answer-input"
                        />
                        <button @click="saveAnswer({ answer: shortAnswer })" class="next-button">Valider</button>
                    </div>

                    <!-- LONG_ANSWER -->
                    <div v-else-if="currentQuestion.type === 'long_answer'" class="question-free-text">
                        <div class="question-text">{{ currentQuestion.content.question }}</div>
                        <textarea
                            v-model="longAnswer"
                            placeholder="Votre réponse détaillée..."
                            class="text-answer"
                        ></textarea>
                        <button @click="saveAnswer({ answer: longAnswer })" class="next-button">Valider</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Écran de fin de quiz -->
        <div v-else-if="quizFinished && !showResults" class="quiz-completed">
            <div class="completion-card">
                <div class="completion-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2>Félicitations !</h2>
                <p>Vous avez terminé le quiz "<strong>{{ quizz.title }}</strong>"</p>

                <!-- Score total ajouté -->
                <div class="score-display">
                    <div class="score-circle" :class="getScoreClass(totalScore.percentage)">
                        <span class="score-percentage">{{ totalScore.percentage }}%</span>
                    </div>
                    <div class="score-details">
                        <div class="score-count">{{ totalScore.correct }} / {{ totalScore.total }}</div>
                        <div class="score-label">réponses correctes</div>
                    </div>
                </div>

                <div class="completion-stats">
                    <div class="stat-item">
                        <span class="stat-value">{{ totalQuestions }}</span>
                        <span class="stat-label">Questions</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ props.quizz.blocs.length }}</span>
                        <span class="stat-label">Blocs</span>
                    </div>
                </div>
                <div class="actions">
                    <button @click="viewResults" class="result-button">Voir mes réponses</button>
                    <button @click="retakeQuiz" class="retake-button">Refaire le quiz</button>
                    <a href="/quiz" class="back-button">Retour aux quiz</a>
                </div>
            </div>
        </div>

        <!-- Page de résultats -->
        <div v-else-if="showResults" class="quiz-results">
            <div class="results-header">
                <h2>Résultats du quiz</h2>
                <p>Consultez vos réponses pour "<strong>{{ quizz.title }}</strong>"</p>

                <!-- Score total ajouté à la page de résultats -->
                <div class="results-score-display">
                    <div class="score-circle" :class="getScoreClass(totalScore.percentage)">
                        <span class="score-percentage">{{ totalScore.percentage }}%</span>
                    </div>
                    <div class="score-details">
                        <div class="score-count">{{ totalScore.correct }} / {{ totalScore.total }}</div>
                        <div class="score-label">réponses correctes</div>
                    </div>
                </div>
            </div>

            <div class="results-summary">
                <div class="summary-item">
                    <span class="summary-label">Questions</span>
                    <span class="summary-value">{{ totalQuestions }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Réponses</span>
                    <span class="summary-value">{{ questionsAnswered }}</span>
                </div>
            </div>

            <div class="results-details">
                <div v-for="bloc in resultsByBloc" :key="bloc.id" class="result-bloc">
                    <h3 class="bloc-title">{{ bloc.name }}</h3>

                    <div v-for="question in bloc.questions" :key="question.id" class="result-question">
                        <div class="question-header">
                            <span class="question-number">#{{ question.id }}</span>
                            <h4>{{ question.title }}</h4>
                            <span class="question-status" :class="question.isCorrect ? 'correct' : 'incorrect'">
                                {{ question.isCorrect ? 'Correcte' : 'Incorrecte' }}
                            </span>
                        </div>

                        <div class="question-content">
                            <div v-if="question.type === 'multiple_choice'">
                                <div class="content-label">Question :</div>
                                <div class="content-text">{{ question.content.question }}</div>
                            </div>
                            <div v-else-if="question.type === 'true_false'">
                                <div class="content-label">Question :</div>
                                <div class="content-text">{{ question.content.question }}</div>
                            </div>
                            <div v-else-if="question.type === 'fill_in_the_blank'">
                                <div class="content-label">Texte :</div>
                                <div class="content-text" v-html="formatBlankText(question.content.text)"></div>
                            </div>
                            <div v-else>
                                <div class="content-label">Question :</div>
                                <div class="content-text">{{ question.content.question }}</div>
                            </div>
                        </div>

                        <div class="question-answer">
                            <div class="answer-label">Votre réponse :</div>
                            <div class="answer-content" :class="{
                                'not-answered': !userAnswers[question.id],
                                'correct-answer': question.isCorrect,
                                'incorrect-answer': userAnswers[question.id] && !question.isCorrect
                            }">
                                {{ formatAnswerDisplay(question) }}
                            </div>

                            <!-- Affichage de la bonne réponse -->
                            <div class="answer-label correct-label">Réponse correcte :</div>
                            <div class="answer-content correct-answer-display">
                                {{ formatCorrectAnswerDisplay(question) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="results-actions">
                <button @click="retakeQuiz" class="retake-button">Refaire le quiz</button>
                <a href="/quiz" class="back-button">Retour aux quiz</a>
            </div>
        </div>
    </div>
</template>

<style scoped>
.quiz-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.quiz-header {
    margin-bottom: 2rem;
}

.quiz-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
}

.quiz-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.subject {
    color: #4f46e5;
    font-weight: 600;
    font-size: 1.1rem;
}

.progress-container {
    width: 100%;
    height: 8px;
    background-color: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin: 0.5rem 0;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 0.9rem;
    color: #6b7280;
}

.bloc-info {
    margin-bottom: 1.5rem;
}

.bloc-name {
    font-size: 1.5rem;
    font-weight: 600;
    color: #374151;
}

.question-container {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.question-title {
    font-size: 1.3rem;
    margin-bottom: 1.5rem;
    color: #111827;
}

.question-options {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.option {
    padding: 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.option:hover {
    border-color: #4f46e5;
    background-color: #f3f4f6;
}

.question-free-text {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.text-answer {
    width: 100%;
    min-height: 150px;
    padding: 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    resize: vertical;
    font-family: inherit;
}

.question-true-false {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.question-fill-blank {
    margin-top: 1.5rem;
}

.question-text {
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

.blank-placeholder {
    background-color: #e5e7eb;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    margin: 0 0.2rem;
    font-weight: 500;
    color: #4f46e5;
}

.blanks-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1.5rem;
}

.blank-input {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.blank-input label {
    font-size: 0.9rem;
    color: #4b5563;
    font-weight: 500;
}

.blank-input input, .short-answer-input {
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-family: inherit;
}

.question-short-answer {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.short-answer-input {
    width: 100%;
}


.true-button, .false-button, .next-button {
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.true-button {
    background-color: #10b981;
    color: white;
}

.false-button {
    background-color: #ef4444;
    color: white;
}

.next-button {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
    align-self: flex-end;
}

.true-button:hover, .false-button:hover, .next-button:hover {
    opacity: 0.9;
}

.quiz-results {
    text-align: center;
    padding: 3rem 1rem;
}

.quiz-results h2 {
    font-size: 2rem;
    color: #111827;
    margin-bottom: 1rem;
}

.actions {
    margin-top: 2rem;
}

.back-button {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.back-button:hover {
    opacity: 0.9;
}

@media (max-width: 768px) {
    .question-true-false {
        flex-direction: column;
    }
}

.quiz-completed, .quiz-results {
    padding: 2rem 0;
}

.completion-card {
    background-color: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 3rem 2rem;
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.completion-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 2rem;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.completion-icon svg {
    width: 40px;
    height: 40px;
}

.completion-card h2 {
    font-size: 2rem;
    color: #111827;
    margin-bottom: 1rem;
}

.completion-card p {
    color: #6b7280;
    margin-bottom: 2rem;
}

.completion-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-bottom: 2rem;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #4f46e5;
}

.stat-label {
    color: #6b7280;
    font-size: 0.9rem;
}

.actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    width: 100%;
    max-width: 300px;
    margin: 0 auto;
}

.result-button {
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.retake-button {
    padding: 0.75rem 1.5rem;
    background: transparent;
    color: #4f46e5;
    border: 2px solid #4f46e5;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.back-button {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: #f3f4f6;
    color: #4b5563;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
    transition: all 0.2s ease;
}

.result-button:hover, .retake-button:hover, .back-button:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

/* Styles pour l'affichage du score */
.score-display {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 1.5rem 0;
}

.results-score-display {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    margin: 2rem 0;
}

.score-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
}

.score-percentage {
    font-size: 2.2rem;
}

.score-excellent {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 8px 15px rgba(16, 185, 129, 0.2);
}

.score-good {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    box-shadow: 0 8px 15px rgba(59, 130, 246, 0.2);
}

.score-average {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    box-shadow: 0 8px 15px rgba(245, 158, 11, 0.2);
}

.score-poor {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 8px 15px rgba(239, 68, 68, 0.2);
}

.score-details {
    text-align: center;
}

.score-count {
    font-size: 1.5rem;
    font-weight: 700;
    color: #4f46e5;
    margin-bottom: 0.25rem;
}

.score-label {
    font-size: 0.9rem;
    color: #6b7280;
}

/* Styles spécifiques à la page des résultats détaillés */
.results-header {
    text-align: center;
    margin-bottom: 3rem;
}

.results-header h2 {
    font-size: 2rem;
    color: #111827;
    margin-bottom: 0.5rem;
}

.results-header p {
    color: #6b7280;
}

.results-summary {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin-bottom: 3rem;
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    padding: 1.5rem;
}

.summary-item {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.summary-label {
    color: #6b7280;
    font-size: 0.9rem;
}

.summary-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #4f46e5;
}

.results-details {
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
    margin-bottom: 3rem;
}

.result-bloc {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    padding: 1.5rem;
}

.bloc-title {
    font-size: 1.3rem;
    color: #111827;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 1.5rem;
}

.result-question {
    padding: 1.5rem;
    margin-bottom: 1rem;
    border-left: 4px solid #4f46e5;
    background-color: #f9fafb;
    border-radius: 0 8px 8px 0;
}

.question-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.question-number {
    background-color: #4f46e5;
    color: white;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.question-header h4 {
    font-size: 1.1rem;
    color: #111827;
    margin: 0;
    flex-grow: 1;
}

.question-status {
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
}

.question-status.correct {
    background-color: #d1fae5;
    color: #065f46;
}

.question-status.incorrect {
    background-color: #fee2e2;
    color: #991b1b;
}

.question-content, .question-answer {
    margin-bottom: 1rem;
}

.content-label, .answer-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.content-text {
    color: #1f2937;
    line-height: 1.6;
}

.answer-content {
    padding: 0.75rem;
    border-radius: 6px;
    margin-bottom: 1rem;
}

.answer-content.not-answered {
    background-color: #fef2f2;
    color: #991b1b;
}

.answer-content.correct-answer {
    background-color: #ecfdf5;
    color: #065f46;
}

.answer-content.incorrect-answer {
    background-color: #fff7ed;
    color: #9a3412;
}

.correct-label {
    color: #065f46;
}

.correct-answer-display {
    background-color: #d1fae5;
    color: #065f46;
    font-weight: 500;
}

.results-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 3rem;
}

@media (max-width: 768px) {
    .completion-stats, .results-summary, .results-score-display {
        flex-direction: column;
        gap: 1rem;
        align-items: center;
    }

    .results-actions {
        flex-direction: column;
    }

    .question-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
