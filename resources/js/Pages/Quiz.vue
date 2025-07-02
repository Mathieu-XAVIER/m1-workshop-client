<template>
    <div class="quiz-page">
        <!-- Header simple -->
        <div class="quiz-header">
            <button @click="goBack" class="back-button">
                ← Retour
            </button>
            <h1 class="quiz-title">{{ quizConfig.title }}</h1>
            <p class="quiz-description">{{ quizConfig.description }}</p>
            <div class="quiz-meta">
                <span class="meta-item">📚 {{ category }}</span>
                <span class="meta-item">⏱️ {{ quizConfig.estimatedTime }}</span>
                <span class="meta-item">❓ {{ quizConfig.questionCount }} questions</span>
                <span class="meta-item">📊 {{ quizConfig.difficulty }}</span>
            </div>
        </div>

        <!-- Progress Bar simple -->
        <div class="progress-container">
            <div class="progress-header">
                <span>Progression</span>
                <span>{{ Math.round(currentProgress) }}%</span>
            </div>
            <div class="progress-bar-wrapper">
                <div
                    class="progress-bar"
                    :style="{ width: currentProgress + '%' }"
                ></div>
            </div>
        </div>

        <!-- Container H5P simple -->
        <div class="h5p-container-wrapper">
            <div class="h5p-placeholder">
                <h3>🎯 Quiz H5P</h3>
                <p><strong>Catégorie:</strong> {{ category }}</p>
                <p><strong>ID H5P:</strong> {{ quizConfig.h5pContentId }}</p>
                <p><strong>URL de base:</strong> {{ h5pSettings.baseUrl }}</p>

                <!-- Simulation H5P -->
                <div class="quiz-simulation">
                    <h4>📝 Simulation de quiz</h4>
                    <p>Question {{ Math.floor(currentProgress / 10) + 1 }} / 10</p>
                    <button @click="simulateProgress" class="simulate-btn" v-if="currentProgress < 100">
                        Répondre à la question
                    </button>
                    <button @click="simulateComplete" class="complete-btn" v-else>
                        Terminer le quiz
                    </button>
                </div>
            </div>
        </div>

        <!-- Actions simples -->
        <div class="quiz-actions">
            <button @click="restartQuiz" class="action-btn restart-btn" v-if="showRestartButton">
                🔄 Recommencer
            </button>
            <button @click="previousQuiz" class="action-btn" v-if="showPrevButton">
                ⬅️ Précédent
            </button>
            <button @click="nextQuiz" class="action-btn" v-if="showNextButton">
                Suivant ➡️
            </button>
        </div>

        <!-- Modal de résultats simple -->
        <div v-if="showResults" class="results-modal-overlay" @click="closeResults">
            <div class="results-modal" @click.stop>
                <button @click="closeResults" class="close-btn">✖️</button>

                <h2>🎉 Quiz terminé !</h2>

                <div class="score-display">
                    <div class="score-circle">
                        <div class="score-percentage">{{ Math.round((quizResults.score / quizResults.maxScore) * 100) }}%</div>
                        <div class="score-fraction">{{ quizResults.score }}/{{ quizResults.maxScore }}</div>
                    </div>
                </div>

                <div class="results-stats">
                    <p><strong>⏱️ Temps:</strong> {{ quizResults.timeTaken }}</p>
                    <p><strong>✅ Bonnes réponses:</strong> {{ quizResults.score }}</p>
                    <p><strong>❌ Erreurs:</strong> {{ quizResults.maxScore - quizResults.score }}</p>
                </div>

                <div class="results-actions">
                    <button @click="restartQuiz" class="results-btn secondary">
                        🔄 Recommencer
                    </button>
                    <button @click="nextQuiz" class="results-btn primary">
                        ➡️ Quiz suivant
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

// Props reçues du contrôleur Laravel via Inertia
const props = defineProps({
    category: {
        type: String,
        required: true
    },
    quizConfig: {
        type: Object,
        required: true
    },
    h5pSettings: {
        type: Object,
        default: () => ({})
    },
    savedProgress: {
        type: Object,
        default: () => null
    },
    canRetry: {
        type: Boolean,
        default: true
    },
    previousAttempts: {
        type: Array,
        default: () => []
    }
})

// État réactif du quiz
const currentProgress = ref(0)
const showResults = ref(false)
const showRestartButton = ref(false)
const showNextButton = ref(false)
const showPrevButton = ref(false)

// Résultats du quiz
const quizResults = reactive({
    score: 0,
    maxScore: 10,
    timeTaken: '',
    feedback: '',
    detailed: []
})

// Méthodes de navigation
const goBack = () => {
    router.visit('/quiz')
}

const nextQuiz = () => {
    console.log('Passage au quiz suivant')
    // Exemple: redirection vers une autre catégorie
    const categories = ['math', 'francais', 'informatique']
    const currentIndex = categories.indexOf(props.category)
    const nextIndex = (currentIndex + 1) % categories.length
    router.visit(`/quiz/${categories[nextIndex]}`)
}

const previousQuiz = () => {
    console.log('Retour au quiz précédent')
    router.visit('/quiz')
}

const restartQuiz = () => {
    currentProgress.value = 0
    showResults.value = false
    showRestartButton.value = false
    showNextButton.value = false
    quizResults.score = 0
    console.log('Quiz redémarré')
}

// Simulation de progression
const simulateProgress = () => {
    currentProgress.value += 10

    if (currentProgress.value >= 30 && !showRestartButton.value) {
        showRestartButton.value = true
    }

    console.log('Progression simulée:', currentProgress.value + '%')
}

const simulateComplete = () => {
    // Simuler des résultats
    quizResults.score = Math.floor(Math.random() * 3) + 7 // Score entre 7-10
    quizResults.maxScore = 10
    quizResults.timeTaken = '5 min 23s'
    quizResults.feedback = 'Excellent travail !'

    showResults.value = true
    showNextButton.value = true

    console.log('Quiz simulé terminé:', quizResults)
}

const closeResults = () => {
    showResults.value = false
}

// Initialisation
onMounted(() => {
    if (props.savedProgress) {
        currentProgress.value = props.savedProgress.currentQuestion || 0
        console.log('Progression restaurée:', props.savedProgress)
    }

    console.log('Quiz initialisé pour la catégorie:', props.category)
    console.log('Configuration reçue:', props.quizConfig)
    console.log('Settings H5P:', props.h5pSettings)
})
</script>

<style scoped>
.quiz-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 50%, #fef3ff 100%);
    padding: 2rem 1rem;
    animation: fadeIn 0.6s ease-out;
    max-width: 1200px;
    margin: 0 auto;
}

@keyframes fadeIn {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

.quiz-header {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    animation: slideInDown 0.6s ease-out;
}

@keyframes slideInDown {
    0% { opacity: 0; transform: translateY(-30px); }
    100% { opacity: 1; transform: translateY(0); }
}

.back-button {
    background: rgba(107, 114, 128, 0.1);
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    color: #6b7280;
    cursor: pointer;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    font-weight: 500;
}

.back-button:hover {
    background: rgba(107, 114, 128, 0.2);
    transform: translateX(-2px);
}

.quiz-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.quiz-description {
    color: #6b7280;
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.quiz-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.meta-item {
    background: #f8fafc;
    padding: 0.5rem 1rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    border: 1px solid #e5e7eb;
}

.progress-container {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    animation: slideInLeft 0.6s ease-out 0.2s both;
}

@keyframes slideInLeft {
    0% { opacity: 0; transform: translateX(-30px); }
    100% { opacity: 1; transform: translateX(0); }
}

.progress-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    font-weight: 600;
    color: #1f2937;
}

.progress-bar-wrapper {
    height: 12px;
    background: #e5e7eb;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #4f46e5, #7c3aed);
    border-radius: 6px;
    transition: width 0.8s ease;
    position: relative;
}

.h5p-container-wrapper {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    animation: slideInUp 0.6s ease-out 0.4s both;
}

@keyframes slideInUp {
    0% { opacity: 0; transform: translateY(30px); }
    100% { opacity: 1; transform: translateY(0); }
}

.h5p-placeholder {
    text-align: center;
    padding: 2rem;
    border: 2px dashed #e5e7eb;
    border-radius: 12px;
}

.h5p-placeholder h3 {
    color: #1f2937;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.h5p-placeholder p {
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.quiz-simulation {
    background: #f0f9ff;
    padding: 2rem;
    border-radius: 12px;
    margin-top: 2rem;
    border: 1px solid #e0e7ff;
}

.quiz-simulation h4 {
    color: #4f46e5;
    margin-bottom: 1rem;
}

.simulate-btn, .complete-btn {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 1rem;
}

.simulate-btn:hover, .complete-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
}

.quiz-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.action-btn {
    padding: 1rem 2rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.restart-btn {
    background: linear-gradient(135deg, #f59e0b, #f97316);
    color: white;
}

.restart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
}

.action-btn:not(.restart-btn) {
    background: rgba(107, 114, 128, 0.1);
    color: #6b7280;
    border: 1px solid rgba(107, 114, 128, 0.2);
}

.action-btn:not(.restart-btn):hover {
    background: rgba(107, 114, 128, 0.2);
    color: #374151;
}

.results-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    animation: fadeIn 0.3s ease-out;
    padding: 1rem;
}

.results-modal {
    background: white;
    border-radius: 24px;
    padding: 3rem;
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    position: relative;
    animation: scaleIn 0.3s ease-out;
}

@keyframes scaleIn {
    0% { opacity: 0; transform: scale(0.9); }
    100% { opacity: 1; transform: scale(1); }
}

.close-btn {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(107, 114, 128, 0.1);
    border: none;
    border-radius: 8px;
    width: 40px;
    height: 40px;
    cursor: pointer;
    font-size: 1.2rem;
}

.results-modal h2 {
    text-align: center;
    color: #1f2937;
    margin-bottom: 2rem;
    font-size: 2rem;
}

.score-display {
    display: flex;
    justify-content: center;
    margin: 2rem 0;
}

.score-circle {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 10px 30px rgba(79, 70, 229, 0.3);
}

.score-percentage {
    font-size: 2rem;
    font-weight: 800;
}

.score-fraction {
    font-size: 1rem;
    opacity: 0.9;
}

.results-stats {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 12px;
    margin: 2rem 0;
}

.results-stats p {
    margin: 0.5rem 0;
    color: #374151;
}

.results-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.results-btn {
    padding: 1rem 2rem;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.results-btn.primary {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
}

.results-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
}

.results-btn.secondary {
    background: rgba(107, 114, 128, 0.1);
    color: #6b7280;
    border: 1px solid rgba(107, 114, 128, 0.2);
}

.results-btn.secondary:hover {
    background: rgba(107, 114, 128, 0.2);
    color: #374151;
}

/* Responsive */
@media (max-width: 768px) {
    .quiz-page {
        padding: 1rem 0.5rem;
    }

    .quiz-header {
        padding: 1.5rem;
    }

    .quiz-title {
        font-size: 2rem;
    }

    .quiz-meta {
        justify-content: center;
    }

    .quiz-actions {
        flex-direction: column;
        align-items: center;
    }

    .action-btn {
        width: 100%;
        max-width: 300px;
    }

    .results-modal {
        padding: 2rem 1.5rem;
        margin: 1rem;
    }

    .results-actions {
        flex-direction: column;
    }

    .results-btn {
        width: 100%;
    }
}
</style>
