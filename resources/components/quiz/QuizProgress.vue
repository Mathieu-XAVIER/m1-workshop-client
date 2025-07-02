<!-- QuizProgress.vue -->
<template name="QuizProgress">
    <div class="quiz-progress-container" :class="{ 'progress-compact': compact }">
        <div class="progress-header" v-if="!hideLabels">
            <div class="progress-info">
                <span class="progress-label">{{ progressLabel }}</span>
                <span class="progress-details" v-if="showDetails">
          {{ current }}/{{ total }} {{ unit }}
        </span>
            </div>

            <div class="progress-percentage" v-if="showPercentage">
                {{ Math.round(percentage) }}%
            </div>
        </div>

        <div class="progress-bar-wrapper">
            <div
                class="progress-bar-background"
                :style="backgroundStyles"
            >
                <div
                    class="progress-bar"
                    :style="progressBarStyles"
                    :class="{ 'progress-animated': animated }"
                >
                    <!-- Effet de brillance -->
                    <div class="progress-shine" v-if="animated"></div>
                </div>
            </div>

            <!-- Indicateurs de segments -->
            <div class="progress-segments" v-if="segments > 1">
                <div
                    v-for="segment in segments - 1"
                    :key="segment"
                    class="segment-marker"
                    :style="{ left: `${(segment / segments) * 100}%` }"
                ></div>
            </div>
        </div>

        <!-- Étiquettes de début/fin -->
        <div class="progress-labels" v-if="showLabels && (startLabel || endLabel)">
            <span class="start-label">{{ startLabel }}</span>
            <span class="end-label">{{ endLabel }}</span>
        </div>
    </div>
</template>

<script setup name="QuizProgress">
import { computed } from 'vue'

const props = defineProps({
    current: {
        type: Number,
        required: true
    },
    total: {
        type: Number,
        required: true
    },
    unit: {
        type: String,
        default: 'questions'
    },
    progressLabel: {
        type: String,
        default: 'Progression'
    },
    showPercentage: {
        type: Boolean,
        default: true
    },
    showDetails: {
        type: Boolean,
        default: true
    },
    showLabels: {
        type: Boolean,
        default: false
    },
    hideLabels: {
        type: Boolean,
        default: false
    },
    startLabel: {
        type: String,
        default: 'Début'
    },
    endLabel: {
        type: String,
        default: 'Fin'
    },
    animated: {
        type: Boolean,
        default: true
    },
    compact: {
        type: Boolean,
        default: false
    },
    segments: {
        type: Number,
        default: 1
    },
    color: {
        type: String,
        default: '#4f46e5'
    },
    backgroundColor: {
        type: String,
        default: '#e5e7eb'
    },
    height: {
        type: String,
        default: '12px'
    }
})

const percentage = computed(() => {
    return Math.min((props.current / props.total) * 100, 100)
})

const progressBarStyles = computed(() => ({
    width: `${percentage.value}%`,
    background: `linear-gradient(90deg, ${props.color}, ${adjustColor(props.color, 20)})`,
    height: props.height
}))

const backgroundStyles = computed(() => ({
    backgroundColor: props.backgroundColor,
    height: props.height
}))

const adjustColor = (color, amount) => {
    const usePound = color[0] === '#'
    const col = usePound ? color.slice(1) : color
    const num = parseInt(col, 16)
    let r = (num >> 16) + amount
    let g = (num >> 8 & 0x00FF) + amount
    let b = (num & 0x0000FF) + amount
    r = r > 255 ? 255 : r < 0 ? 0 : r
    g = g > 255 ? 255 : g < 0 ? 0 : g
    b = b > 255 ? 255 : b < 0 ? 0 : b
    return (usePound ? '#' : '') + (r << 16 | g << 8 | b).toString(16).padStart(6, '0')
}
</script>

<!-- QuizActions.vue -->
<template name="QuizActions">
    <div class="quiz-actions" :class="{ 'actions-compact': compact }">
        <div class="actions-group actions-left">
            <button
                v-if="showPrev"
                @click="$emit('previous')"
                :disabled="disablePrev"
                class="action-button action-secondary"
            >
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7"></path>
                </svg>
                <span v-if="!compact">{{ prevLabel }}</span>
            </button>

            <button
                v-if="showRestart"
                @click="confirmRestart"
                :disabled="disableRestart"
                class="action-button action-warning"
            >
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span v-if="!compact">{{ restartLabel }}</span>
            </button>
        </div>

        <div class="actions-group actions-center" v-if="$slots.default">
            <slot></slot>
        </div>

        <div class="actions-group actions-right">
            <button
                v-if="showNext"
                @click="$emit('next')"
                :disabled="disableNext"
                class="action-button action-primary"
            >
                <span v-if="!compact">{{ nextLabel }}</span>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <button
                v-if="showFinish"
                @click="$emit('finish')"
                :disabled="disableFinish"
                class="action-button action-success"
            >
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7"></path>
                </svg>
                <span v-if="!compact">{{ finishLabel }}</span>
            </button>
        </div>

        <!-- Modal de confirmation -->
        <div v-if="showConfirmModal" class="modal-overlay" @click="cancelRestart">
            <div class="modal-content" @click.stop>
                <h3>{{ confirmTitle }}</h3>
                <p>{{ confirmMessage }}</p>
                <div class="modal-actions">
                    <button @click="cancelRestart" class="modal-button modal-cancel">
                        Annuler
                    </button>
                    <button @click="confirmRestartAction" class="modal-button modal-confirm">
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup name="QuizActions">
import { ref } from 'vue'

const props = defineProps({
    showPrev: Boolean,
    showNext: Boolean,
    showRestart: Boolean,
    showFinish: Boolean,
    disablePrev: Boolean,
    disableNext: Boolean,
    disableRestart: Boolean,
    disableFinish: Boolean,
    prevLabel: {
        type: String,
        default: 'Précédent'
    },
    nextLabel: {
        type: String,
        default: 'Suivant'
    },
    restartLabel: {
        type: String,
        default: 'Recommencer'
    },
    finishLabel: {
        type: String,
        default: 'Terminer'
    },
    compact: Boolean,
    confirmRestart: {
        type: Boolean,
        default: true
    },
    confirmTitle: {
        type: String,
        default: 'Recommencer le quiz ?'
    },
    confirmMessage: {
        type: String,
        default: 'Votre progression actuelle sera perdue.'
    }
})

const emit = defineEmits(['previous', 'next', 'restart', 'finish'])

const showConfirmModal = ref(false)

const confirmRestart = () => {
    if (props.confirmRestart) {
        showConfirmModal.value = true
    } else {
        emit('restart')
    }
}

const confirmRestartAction = () => {
    showConfirmModal.value = false
    emit('restart')
}

const cancelRestart = () => {
    showConfirmModal.value = false
}
</script>

<!-- QuizResultsModal.vue -->
<template name="QuizResultsModal">
    <div class="modal-overlay" @click="$emit('close')">
        <div class="results-modal" @click.stop>
            <!-- Header -->
            <div class="results-header">
                <div class="results-icon" :class="scoreClass">
                    <component :is="scoreIcon" />
                </div>
                <h2 class="results-title">{{ resultTitle }}</h2>
                <p class="results-subtitle">{{ resultSubtitle }}</p>
            </div>

            <!-- Score principal -->
            <div class="score-display">
                <div class="score-circle" :class="scoreClass">
                    <div class="score-percentage">{{ scorePercentage }}%</div>
                    <div class="score-fraction">{{ score }}/{{ maxScore }}</div>
                </div>
            </div>

            <!-- Statistiques détaillées -->
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ timeTaken }}</div>
                        <div class="stat-label">Temps</div>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ correctAnswers }}</div>
                        <div class="stat-label">Bonnes réponses</div>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ incorrectAnswers }}</div>
                        <div class="stat-label">Erreurs</div>
                    </div>
                </div>
            </div>

            <!-- Feedback -->
            <div class="feedback-section" v-if="feedback">
                <h3 class="feedback-title">Commentaire</h3>
                <p class="feedback-text">{{ feedback }}</p>
            </div>

            <!-- Actions -->
            <div class="results-actions">
                <button @click="$emit('restart')" class="results-button button-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Recommencer
                </button>

                <button @click="$emit('next-quiz')" class="results-button button-primary">
                    Quiz suivant
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>
            </div>

            <!-- Bouton fermer -->
            <button @click="$emit('close')" class="close-button">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup name="QuizResultsModal">
import { computed } from 'vue'

const props = defineProps({
    score: {
        type: Number,
        required: true
    },
    maxScore: {
        type: Number,
        required: true
    },
    timeTaken: {
        type: String,
        default: '0 min'
    },
    feedback: {
        type: String,
        default: ''
    }
})

defineEmits(['close', 'restart', 'next-quiz'])

const scorePercentage = computed(() => {
    return Math.round((props.score / props.maxScore) * 100)
})

const correctAnswers = computed(() => props.score)
const incorrectAnswers = computed(() => props.maxScore - props.score)

const scoreClass = computed(() => {
    const percentage = scorePercentage.value
    if (percentage >= 80) return 'score-excellent'
    if (percentage >= 60) return 'score-good'
    if (percentage >= 40) return 'score-average'
    return 'score-poor'
})

const scoreIcon = computed(() => {
    const percentage = scorePercentage.value
    if (percentage >= 80) return 'TrophyIcon'
    if (percentage >= 60) return 'StarIcon'
    return 'InfoIcon'
})

const resultTitle = computed(() => {
    const percentage = scorePercentage.value
    if (percentage >= 80) return 'Excellent !'
    if (percentage >= 60) return 'Bien joué !'
    if (percentage >= 40) return 'Pas mal'
    return 'À améliorer'
})

const resultSubtitle = computed(() => {
    const percentage = scorePercentage.value
    if (percentage >= 80) return 'Vous maîtrisez parfaitement le sujet'
    if (percentage >= 60) return 'Bonne compréhension du sujet'
    if (percentage >= 40) return 'Quelques points à revoir'
    return 'Continuez à vous entraîner'
})
</script>

<style scoped>
/* Styles pour tous les composants */
/* [Styles CSS complets pour QuizProgress, QuizActions et QuizResultsModal] */
</style>
