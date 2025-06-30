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
                <!-- Mathématiques -->
                <div class="category-card math" @click="goToCategory('math')">
                    <!-- Icon Container -->
                    <div class="category-icon math">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>

                    <h3 class="category-title">Mathématiques</h3>
                    <p class="category-description">
                        Algèbre, géométrie, calculs et bien plus encore
                    </p>

                    <!-- Stats -->
                    <div class="category-stats">
                        <span>12 Quiz</span>
                        <span>~15 min</span>
                    </div>

                    <button class="category-button math">
                        Commencer
                    </button>

                    <!-- Decorative element -->
                    <div class="decorative-element"></div>
                </div>

                <!-- Français -->
                <div class="category-card french" @click="goToCategory('francais')">
                    <!-- Icon Container -->
                    <div class="category-icon french">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>

                    <h3 class="category-title">Français</h3>
                    <p class="category-description">
                        Grammaire, orthographe, conjugaison et littérature
                    </p>

                    <!-- Stats -->
                    <div class="category-stats">
                        <span>8 Quiz</span>
                        <span>~12 min</span>
                    </div>

                    <button class="category-button french">
                        Commencer
                    </button>

                    <!-- Decorative element -->
                    <div class="decorative-element"></div>
                </div>

                <!-- Informatique -->
                <div class="category-card computer" @click="goToCategory('informatique')">
                    <!-- Icon Container -->
                    <div class="category-icon computer">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                        </svg>
                    </div>

                    <h3 class="category-title">Informatique</h3>
                    <p class="category-description">
                        Programmation, algorithmes et technologies web
                    </p>

                    <!-- Stats -->
                    <div class="category-stats">
                        <span>15 Quiz</span>
                        <span>~20 min</span>
                    </div>

                    <button class="category-button computer">
                        Commencer
                    </button>

                    <!-- Decorative element -->
                    <div class="decorative-element"></div>
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
import { onMounted } from 'vue'

// Navigation vers les catégories
const goToCategory = (category) => {
    console.log(`Navigation vers la catégorie: ${category}`)
    // router.visit(`/quiz/${category}`)
}

// Animation de chargement
onMounted(() => {
    // Ajouter la classe 'loaded' après un petit délai pour déclencher les animations
    setTimeout(() => {
        document.querySelector('.quiz-container')?.classList.add('loaded')
    }, 100)
})
</script>

<style scoped>
@import './Home.css';
</style>
