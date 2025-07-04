    <script setup>
    import { Link, useForm } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const showPassword = ref(false);

    const form = useForm({
    email: '',
    password: '',
    });

    const submit = () => {
    form.post('/api/login');
    };
</script>

<template>
  <div class="form-container">
    <h1>Connexion</h1>
    <form @submit.prevent="submit">
      <div>
        <label for="email">Email</label>
        <input id="email" v-model="form.email" type="email" required>
        <div v-if="form.errors.email" class="error-message">{{ form.errors.email }}</div>
      </div>

      <div class="password-wrapper">
        <label for="password">Mot de passe</label>
        <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" required>
        <span class="toggle-password" @click="showPassword = !showPassword">
          <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 0-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588M5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
            <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>
          </svg>
        </span>
        <div v-if="form.errors.password" class="error-message">{{ form.errors.password }}</div>
      </div>

      <div>
        <button type="submit" :disabled="form.processing">Se connecter</button>
      </div>
    </form>
    <div class="text-center">
      <p>Pas encore de compte ? <Link href="/">S'inscrire</Link></p>
    </div>
    <div class="text-center" style="margin-top: 1rem;">
      <Link href="/forgot-password">Mot de passe oublié ?</Link>
    </div>
  </div>
</template>

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
