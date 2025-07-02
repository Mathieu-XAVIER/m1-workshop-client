<template>
    <div>
        <h1>{{ question.title }}</h1>
        <p>Type: {{ question.type }}</p>
        <a href="/questions">← Retour</a>

        <!-- Résultat si disponible -->
        <div v-if="quizResult">
            <h2>{{ quizResult.is_correct ? 'Correct!' : 'Incorrect' }}</h2>
            <p>{{ quizResult.explanation }}</p>
            <p>Temps: {{ quizResult.timer }}s</p>
            <a :href="`/questions/${question.id}`">Recommencer</a>
        </div>

        <!-- Formulaire -->
        <form v-else :action="`/questions/${question.id}/check-answer`" method="POST">
            <input type="hidden" name="_token" :value="$page.props.csrf_token">

            <h2>{{ question.content.question }}</h2>

            <!-- QCM -->
            <div v-if="question.type === 'multiple_choice'">
                <div v-for="(option, index) in question.content.options" :key="index">
                    <input type="radio" :value="index" name="user_answer" required>
                    {{ option }}
                </div>
            </div>

            <!-- Vrai/Faux -->
            <div v-else-if="question.type === 'true_false'">
                <input type="radio" value="1" name="user_answer" required> Vrai<br>
                <input type="radio" value="0" name="user_answer" required> Faux
            </div>

            <!-- Texte -->
            <div v-else>
                <input type="text" name="user_answer" required>
            </div>

            <input type="hidden" name="timer" value="0">
            <button type="submit">Valider</button>
        </form>
    </div>
</template>

<script setup>
import { onMounted, watch } from 'vue'

const props = defineProps(['question', 'quizResult'])

// Console log au montage du composant
onMounted(() => {
    if (props.quizResult) {
        if (props.quizResult.is_correct) {
            console.log('✅ RÉPONSE CORRECTE !', props.quizResult)
        } else {
            console.log('❌ RÉPONSE INCORRECTE !', props.quizResult)
        }
    }
})

// Console log quand quizResult change
watch(() => props.quizResult, (newResult) => {
    if (newResult) {
        if (newResult.is_correct) {
            console.log('✅ RÉPONSE CORRECTE !', newResult)
        } else {
            console.log('❌ RÉPONSE INCORRECTE !', newResult)
        }
    }
})
</script>
