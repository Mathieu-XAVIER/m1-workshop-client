<template>
    <div class="quiz-header" :class="[`quiz-header--${category}`, { 'quiz-header--compact': compact }]">
        <!-- Bouton retour -->
        <button @click="$emit('back')" class="back-button" v-if="showBackButton">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span v-if="!compact">Retour</span>
        </button>

        <div class="header-content">
            <!-- Icône et titre -->
            <div class="title-section">
                <div class="quiz-icon" v-if="showIcon">
                    <component :is="iconComponent" />
                </div>

                <div class="title-content">
                    <h1 class="quiz-title">{{ title }}</h1>
                    <p class="quiz-description" v-if="description && !compact">{{ description }}</p>
                </div>
            </div>

            <!-- Métadonnées -->
            <div class="quiz-meta" v-if="!compact">
                <div class="meta-item" v-if="category">
                    <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span class="meta-text">{{ formatCategory(category) }}</span>
                </div>

                <div class="meta-item" v-if="difficulty">
                    <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span class="meta-text">{{ difficulty }}</span>
                </div>

                <div class="meta-item" v-if="estimatedTime">
                    <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="meta-text">{{ estimatedTime }}</span>
                </div>

                <div class="meta-item" v-if="questionCount">
                    <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="meta-text">{{ questionCount }} questions</span>
                </div>
            </div>
        </div>

        <!-- Actions additionnelles -->
        <div class="header-actions" v-if="$slots.actions">
            <slot name="actions"></slot>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: ''
    },
    category: {
        type: String,
        default: ''
    },
    difficulty: {
        type: String,
        default: ''
    },
    estimatedTime: {
        type: String,
        default: ''
    },
    questionCount: {
        type: Number,
        default: null
    },
    icon: {
        type: String,
        default: 'quiz'
    },
    showIcon: {
        type: Boolean,
        default: true
    },
    showBackButton: {
        type: Boolean,
        default: true
    },
    compact: {
        type: Boolean,
        default: false
    }
})

// Émissions
defineEmits(['back'])

// Composant d'icône dynamique
const iconComponent = computed(() => {
    const icons = {
        math: 'CalculatorIcon',
        francais: 'BookIcon',
        informatique: 'CodeIcon',
        quiz: 'QuizIcon'
    }

    return icons[props.icon] || 'QuizIcon'
})

// Formatage de la catégorie
const formatCategory = (category) => {
    const categoryNames = {
        math: 'Mathématiques',
        francais: 'Français',
        informatique: 'Informatique'
    }

    return categoryNames[category] || category
}
</script>

<style scoped>
.quiz-header {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow:
        0 10px 25px -5px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
    animation: slideInDown 0.6s ease-out;
}

.quiz-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4f46e5, #7c3aed, #10b981);
    border-radius: 20px 20px 0 0;
}

.quiz-header--compact {
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.quiz-header--math::before {
    background: linear-gradient(90deg, #3b82f6, #2563eb);
}

.quiz-header--francais::before {
    background: linear-gradient(90deg, #10b981, #059669);
}

.quiz-header--informatique::before {
    background: linear-gradient(90deg, #8b5cf6, #7c3aed);
}

@keyframes slideInDown {
    0% {
        opacity: 0;
        transform: translateY(-30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(107, 114, 128, 0.1);
    border: none;
    border-radius: 12px;
    color: #6b7280;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
}

.back-button:hover {
    background: rgba(107, 114, 128, 0.2);
    color: #374151;
    transform: translateX(-2px);
}

.back-button svg {
    width: 20px;
    height: 20px;
}

.header-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.title-section {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.quiz-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
}

.quiz-header--compact .quiz-icon {
    width: 48px;
    height: 48px;
}

.quiz-header--math .quiz-icon {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
}

.quiz-header--francais .quiz-icon {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
}

.quiz-header--informatique .quiz-icon {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.3);
}

.title-content {
    flex: 1;
    min-width: 0;
}

.quiz-title {
    font-size: clamp(1.75rem, 4vw, 2.5rem);
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
    line-height: 1.2;
}

.quiz-header--compact .quiz-title {
    font-size: 1.5rem;
    margin-bottom: 0;
}

.quiz-description {
    color: #6b7280;
    font-size: 1.1rem;
    line-height: 1.6;
    margin: 0;
}

.quiz-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    align-items: center;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(249, 250, 251, 0.8);
    border-radius: 12px;
    border: 1px solid rgba(229, 231, 235, 0.5);
    transition: all 0.3s ease;
}

.meta-item:hover {
    background: rgba(249, 250, 251, 1);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.meta-icon {
    width: 18px;
    height: 18px;
    color: #9ca3af;
    flex-shrink: 0;
}

.meta-text {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    white-space: nowrap;
}

.header-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    align-items: center;
}

/* Responsive */
@media (max-width: 768px) {
    .quiz-header {
        padding: 1.5rem;
    }

    .title-section {
        flex-direction: column;
        text-align: center;
    }

    .quiz-meta {
        justify-content: center;
        gap: 1rem;
    }

    .meta-item {
        flex: 1;
        min-width: 120px;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .quiz-meta {
        flex-direction: column;
        gap: 0.75rem;
    }

    .meta-item {
        width: 100%;
    }

    .back-button span {
        display: none;
    }
}
</style>
