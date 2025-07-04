<script setup lang="ts">
const props = defineProps<{
    quizzes: any[],
    currentSubject?: string
}>();
</script>
<template>
    <div class="quiz-listing-container">
        <div class="header">
            <h1 class="title">{{ currentSubject ? `Quiz sur ${currentSubject}` : 'Tous les quiz' }}</h1>
            <p class="subtitle">{{ quizzes.length }} quiz disponibles</p>
        </div>

        <div class="quiz-grid">
            <div v-for="quiz in quizzes" :key="quiz.id" class="quiz-card">
                <div class="quiz-content">
                    <h2 class="quiz-title">{{ quiz.title }}</h2>
                    <p class="quiz-subject">{{ quiz.subject }}</p>
                    <div class="quiz-stats">
                        <span>{{ quiz.blocs?.length || 0 }} blocs</span>
                    </div>
                </div>
                <a :href="`/quiz/${quiz.id}`" class="quiz-link">Démarrer</a>
            </div>
        </div>

        <div v-if="quizzes.length === 0" class="no-results">
            <p>Aucun quiz disponible dans cette catégorie pour le moment.</p>
            <a href="/" class="back-link">Retour à l'accueil</a>
        </div>
    </div>
</template>

<style scoped>
.quiz-listing-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.header {
    text-align: center;
    margin-bottom: 3rem;
}

.title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.subtitle {
    color: #6b7280;
    font-size: 1.1rem;
}

.quiz-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.quiz-card {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    padding: 1.5rem;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.quiz-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.quiz-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #111827;
}

.quiz-subject {
    color: #4f46e5;
    font-weight: 500;
    margin-bottom: 1rem;
}

.quiz-stats {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.quiz-link {
    display: inline-block;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s ease;
}

.quiz-link:hover {
    opacity: 0.9;
    transform: scale(1.02);
}

.no-results {
    text-align: center;
    padding: 3rem 0;
    color: #6b7280;
}

.back-link {
    display: inline-block;
    margin-top: 1rem;
    color: #4f46e5;
    font-weight: 500;
    text-decoration: none;
}

@media (max-width: 768px) {
    .quiz-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }
}
</style>
