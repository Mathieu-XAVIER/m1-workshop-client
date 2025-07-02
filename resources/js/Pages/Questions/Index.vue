<template>
    <div>
        <h1>Questions</h1>

        <table>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Type</th>
                <th>Niveau</th>
                <th>Actions</th>
            </tr>
            <tr v-for="question in questions" :key="question.id">
                <td>{{ question.id }}</td>
                <td>{{ question.title }}</td>
                <td>{{ question.type }}</td>
                <td>{{ question.level }}</td>
                <td>
                    <a :href="`/questions/${question.id}`">Faire le quiz</a>
                </td>
            </tr>
        </table>

        <div v-if="questions.length === 0">
            Aucune question disponible
        </div>
    </div>
</template>

<script setup>
defineProps(['questions'])
</script>
