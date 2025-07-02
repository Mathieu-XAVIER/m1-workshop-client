<template>
    <div class="quiz-container">
        <div class="main-wrapper">
            <!-- Header -->
            <div class="header-section">
                <h1 class="main-title">
                    Quiz<span class="title-accent">App</span>
                </h1>
                <p class="subtitle">
                    Testez vos connaissances avec nos quiz interactifs
                </p>
            </div>

            <!-- Categories Grid -->
            <div class="categories-grid">
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="category-card"
                    :class="category.id"
                    @click="goToCategory(category.id)"
                >
                    <!-- Icon Container -->
                    <div class="category-icon" :class="category.id">
                        <component :is="getIconComponent(category.icon)" />
                    </div>

                    <h3 class="category-title">{{ category.title }}</h3>
                    <p class="category-description">
                        {{ category.description }}
                    </p>

                    <!-- Stats -->
                    <div class="category-stats">
                        <span>{{ category.questionCount }} questions</span>
                        <span>{{ category.estimatedTime }}</span>
                    </div>

                    <button class="category-button" :class="category.id">
                        Commencer
                    </button>

                    <!-- Decorative element -->
                    <div class="decorative-element"></div>
                </div>
            </div>

            <!-- Stats globales -->
            <div class="global-stats" v-if="stats">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">{{ stats.totalQuizzes }}</div>
                        <div class="stat-label">Quiz disponibles</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ stats.totalAttempts || 0 }}</div>
                        <div class="stat-label">Tentatives totales</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ Math.round(stats.averageScore || 0) }}%</div>
                        <div class="stat-label">Score moyen</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ Math.round(stats.completionRate || 0) }}%</div>
                        <div class="stat-label">Taux de réussite</div>
                    </div>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="footer-section">
                <div class="footer-badge">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="footer-text">Quiz interactifs propulsés par H5P</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { onMounted, computed } from 'vue'

// Props reçues du contrôleur Laravel
const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => null
    }
})

// Navigation vers les catégories
const goToCategory = (categoryId) => {
    router.visit(`/quiz/${categoryId}`)
}

// Composants d'icônes (tu peux les remplacer par tes vrais composants d'icônes)
const getIconComponent = (iconName) => {
    const iconComponents = {
        calculator: 'CalculatorIcon',
        book: 'BookIcon',
        code: 'CodeIcon'
    }

    // Pour l'instant, on utilise des SVG simples
    return 'div' // Remplace par tes composants d'icônes
}

// Animation de chargement
onMounted(() => {
    setTimeout(() => {
        document.querySelector('.quiz-container')?.classList.add('loaded')
    }, 100)
})
</script>

<style scoped>
/* Utilise les mêmes styles que Home.vue */
.quiz-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);
    padding: 3rem 1rem;
    position: relative;
    overflow: hidden;
}

.quiz-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image:
        radial-gradient(circle at 20% 30%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
    animation: floating 6s ease-in-out infinite;
}

@keyframes floating {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(1deg); }
}

.main-wrapper {
    max-width: 1280px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.header-section {
    text-align: center;
    margin-bottom: 4rem;
    animation: slideInDown 0.8s ease-out;
}

@keyframes slideInDown {
    0% {
        opacity: 0;
        transform: translateY(-50px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.main-title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800;
    color: #111827;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #1f2937, #4f46e5);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.title-accent {
    color: #4f46e5;
    position: relative;
}

.subtitle {
    font-size: 1.25rem;
    color: #6b7280;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 4rem;
}

.category-card {
    position: relative;
    background: #ffffff;
    border-radius: 24px;
    padding: 2rem;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    cursor: pointer;
    animation: slideInUp 0.6s ease-out both;
}

.category-card:nth-child(1) { animation-delay: 0.1s; }
.category-card:nth-child(2) { animation-delay: 0.2s; }
.category-card:nth-child(3) { animation-delay: 0.3s; }

@keyframes slideInUp {
    0% {
        opacity: 0;
        transform: translateY(50px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.category-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.category-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.category-icon.math {
    background-color: #dbeafe;
}

.category-icon.francais {
    background-color: #d1fae5;
}

.category-icon.informatique {
    background-color: #e9d5ff;
}

.category-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    text-align: center;
    margin-bottom: 1rem;
}

.category-description {
    color: #6b7280;
    text-align: center;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.category-stats {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
    color: #9ca3af;
    padding: 0 0.5rem;
}

.category-button {
    width: 100%;
    padding: 1rem 1.5rem;
    border: none;
    border-radius: 16px;
    font-weight: 600;
    font-size: 1rem;
    color: white;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.category-button.math {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.category-button.francais {
    background: linear-gradient(135deg, #10b981, #059669);
}

.category-button.informatique {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.category-button:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.decorative-element {
    position: absolute;
    top: 0;
    right: 0;
    width: 80px;
    height: 80px;
    border-bottom-left-radius: 24px;
    opacity: 0.1;
}

.category-card.math .decorative-element {
    background: #2563eb;
}

.category-card.francais .decorative-element {
    background: #059669;
}

.category-card.informatique .decorative-element {
    background: #7c3aed;
}

/* Stats globales */
.global-stats {
    margin: 4rem 0;
    animation: fadeIn 1s ease-out 0.6s both;
}

@keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 2rem 1.5rem;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #4f46e5;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #6b7280;
    font-weight: 500;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Footer */
.footer-section {
    text-align: center;
    animation: fadeIn 1s ease-out 0.8s both;
}

.footer-badge {
    display: inline-flex;
    align-items: center;
    padding: 1rem 1.5rem;
    background: white;
    border-radius: 50px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.footer-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.footer-badge svg {
    width: 20px;
    height: 20px;
    color: #10b981;
    margin-right: 0.5rem;
}

.footer-text {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

/* Responsive */
@media (max-width: 768px) {
    .quiz-container {
        padding: 2rem 1rem;
    }

    .categories-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
