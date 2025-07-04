<script setup>
    import {onMounted, defineProps, computed} from 'vue'
    import Header from '../Components/Header.vue'

    const props = defineProps({
        quizzes: Array,
        auth: Object
    })

    // Extraire les sujets uniques des quizz
    const uniqueSubjects = computed(() => {
        return [...new Set(props.quizzes.map(quizz => quizz.subject))]
    })

    const getCategoryDescription = (subject) => {
        return `Découvrez tous nos quiz sur ${subject}`
    }

    const getQuizCountBySubject = (subject) => {
        return props.quizzes.filter(quizz => quizz.subject === subject).length
    }

    const getQuestionsCountBySubject = (subject) => {
        const quizzesInSubject = props.quizzes.filter(quizz => quizz.subject === subject)
        return quizzesInSubject.reduce((total, quizz) => {
            return total + (quizz.blocs_count || quizz.blocs?.length || 0)
        }, 0)
    }

    const goToQuizz = (subject) => {
        window.location.href = `/quiz?subject=${encodeURIComponent(subject)}`;
    }

    onMounted(() => {
        setTimeout(() => {
            document.querySelector('.quiz-container')?.classList.add('loaded')
        }, 100)
    })
    </script>

    <template>
        <Header :user="props.auth?.user" title="QuizMaster"/>

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

                <!-- Quiz Grid -->
                <div class="categories-grid">
                    <!-- Affichage des catégories uniques -->
                    <div v-for="(subject, index) in uniqueSubjects" :key="subject"
                         class="category-card"
                         @click="goToQuizz(subject)">
                        <!-- Icon Container -->
                        <div class="category-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>

                        <h3 class="category-title">{{ subject }}</h3>
                        <p class="category-description">
                            {{ getCategoryDescription(subject) }}
                        </p>

                        <!-- Stats -->
                        <div class="category-stats">
                            <span>{{ getQuizCountBySubject(subject) }} Quiz</span>
                            <span>{{ getQuestionsCountBySubject(subject) }} Questions</span>
                        </div>

                        <button class="category-button">
                            Explorer cette catégorie
                        </button>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="footer-section">
                    <div class="footer-badge">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <span class="footer-text">Quiz interactifs propulsés par H5P</span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <style scoped>
    @import '../../css/Home.css';
    </style>
