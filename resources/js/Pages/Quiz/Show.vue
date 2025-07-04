<script setup lang="ts">
import {computed, ref} from 'vue';
import '../../../css/quiz/show.css';

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

function saveAnswer(answer) {
    if (!currentQuestion.value) return;

    userAnswers.value[currentQuestion.value.id] = answer;

    console.log(`Réponse enregistrée pour la question ${currentQuestion.value.id}:`, answer);

    shortAnswer.value = '';
    longAnswer.value = '';
    blankAnswers.value = [];

    goToNextQuestion();
}

function goToNextQuestion() {
    console.log("Passage à la question suivante...");
    if (isANextQuestionAvailable() === false) {
        finishQuiz()
    }
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

function isANextQuestionAvailable() {
    const currentBlocQuestions = currentBloc.value?.questions || [];
    return currentQuestionIndex.value < currentBlocQuestions.length - 1;


}

function finishQuiz() {
    console.log("Quiz terminé. Réponses utilisateur:", userAnswers.value);
    quizFinished.value = true;
}

function viewResults() {
    showResults.value = true;
}

function retakeQuiz() {
    currentBlocIndex.value = 0;
    currentQuestionIndex.value = 0;
    userAnswers.value = {};
    quizFinished.value = false;
    showResults.value = false;
}

function extractBlanks(text) {
    return text.match(/\[blank\]/g) || [];
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
                if (!userAnswer.selected || !question.answer.options) return false;

                const selectedIndices = Array.isArray(userAnswer.selected)
                    ? userAnswer.selected
                    : [userAnswer.selected];

                let correctSelections = 0;
                let totalCorrectOptions = 0;

                question.answer.options.forEach((option, index) => {
                    if (option.correct === true) {
                        totalCorrectOptions++;
                        if (selectedIndices.includes(index)) {
                            correctSelections++;
                        }
                    } else if (selectedIndices.includes(index)) {
                        correctSelections--;
                    }
                });

                return correctSelections === totalCorrectOptions && correctSelections >= 0;
            case 'true_false':
                const userBool = userAnswer.correct === true || userAnswer.correct === "1" || userAnswer.correct === 1;
                const correctBool = question.answer.correct === true || question.answer.correct === "1" || question.answer.correct === 1;

                return userBool === correctBool;

            case 'short_answer':
            case 'long_answer':
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

function arraysEqual(a, b) {
    if (!Array.isArray(a) || !Array.isArray(b)) return false;

    if (a.length !== b.length) return false;

    const normalizeArray = arr => arr.map(item => String(item).trim());

    const sortedA = [...normalizeArray(a)].sort();
    const sortedB = [...normalizeArray(b)].sort();

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
            return userAnswer.correct === true || userAnswer.correct === "1" || userAnswer.correct === 1 ? "Vrai" : "Faux";
        case 'short_answer':
        case 'long_answer':
            return userAnswer.answer || "Sans réponse";
        case 'fill_in_the_blank':
            return userAnswer.blanks ? userAnswer.blanks.join(' | ') : "Sans réponse";
        default:
            return "Format non reconnu";
    }
}

function formatCorrectAnswerDisplay(question) {
    if (!question.answer) return "Pas de réponse correcte définie";

    switch (question.type) {
        case 'multiple_choice':
            if (!question.answer.options) return "Non défini";

            return question.answer.options
                .filter(option => option.correct)
                .map(option => option.text)
                .join(', ');
        case 'true_false':
            return question.answer.correct === true || question.answer.correct === "1" || question.answer.correct === 1 ? "Vrai" : "Faux";
        case 'short_answer':
        case 'long_answer':
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
