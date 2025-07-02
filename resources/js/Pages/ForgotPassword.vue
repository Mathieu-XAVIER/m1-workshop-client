<template class="container">
  <div class="form-container">
    <h1>Mot de passe oublié</h1>
    <form @submit.prevent="submit">
      <div>
        <label for="email">Email</label>
        <input id="email" v-model="form.email" type="email" required>
        <div v-if="form.errors.email" class="error-message">{{ form.errors.email }}</div>
      </div>
      <div>
        <button type="submit" :disabled="form.processing">Envoyer le lien de réinitialisation</button>
      </div>
      <div v-if="status" class="success-message">{{ status }}</div>
    </form>
    <div class="text-center" style="margin-top: 1rem;">
      <Link href="/login">Retour à la connexion</Link>
    </div>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const status = ref('');
const form = useForm({ email: '' });

const submit = () => {
  form.post('/forgot-password', {
    onSuccess: () => {
      status.value = 'Si cet email existe, un lien de réinitialisation a été envoyé.';
    }
  });
};
</script>

<style scoped>
.form-container {
  max-width: 500px;
  margin: 50px auto;
  padding: 2rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  font-family: sans-serif;
}
h1 {
  text-align: center;
  margin-bottom: 1.5rem;
}
form div {
  margin-bottom: 1rem;
}
label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
}
input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}
button {
  width: 100%;
  padding: 0.75rem;
  border: none;
  border-radius: 4px;
  background-color: #2563eb;
  color: white;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.2s;
}
button:hover {
  background-color: #1d4ed8;
}
button:disabled {
  background-color: #aaa;
  cursor: not-allowed;
}
.password-wrapper {
  position: relative;
  margin-bottom: 1rem;
}
.password-wrapper input {
  padding-right: 40px;
}
.toggle-password {
  position: absolute;
  top: 38px;
  right: 10px;
  cursor: pointer;
  color: #6c757d;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.toggle-password svg {
  width: 16px;
  height: 16px;
}
.text-center {
  text-align: center;
  margin-top: 1.5rem;
}
.text-center a {
  color: #2563eb;
  text-decoration: none;
  font-weight: bold;
}
.text-center a:hover {
  text-decoration: underline;
}
.error-message {
  color: #dc3545;
  font-size: 0.875em;
  margin-top: 0.25rem;
}
</style> 