<script setup>
    import { useForm, Link } from '@inertiajs/vue3';
    import { ref, watch, computed } from 'vue';

    const showPassword = ref(false);
    const showPasswordConfirmation = ref(false);

    const countries = ref([
    { name: 'FR (+33)', code: '+33', format: '## ## ## ## ##', length: 10 },
    { name: 'BE (+32)', code: '+32', format: '#### ## ## ##', length: 9 },
    { name: 'US (+1)', code: '+1', format: '###-###-####', length: 10 },
    { name: 'GB (+44)', code: '+44', format: '##### ######', length: 11 },
    { name: 'CH (+41)', code: '+41', format: '## ### ## ##', length: 9 },
    { name: 'LU (+352)', code: '+352', format: '### ### ###', length: 9 },
    { name: 'DE (+49)', code: '+49', format: '#### #######', length: 11 },
    { name: 'ES (+34)', code: '+34', format: '### ## ## ##', length: 9 },
    { name: 'IT (+39)', code: '+39', format: '### #######', length: 10 },
    ]);

    const form = useForm({
    firstName: '',
    lastName: '',
    email: '',
    countryCode: '+33',
    phone: '',
    gender: '',
    address_line_1: '',
    address_line_2: '',
    zip_code: '',
    city: '',
    password: '',
    password_confirmation: '',
    });

    const submit = () => {
      form.transform(data => ({
        ...data,
        phone: data.phone.replace(/[^0-9]/g, ''),
      })).post('/api/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
      });
    };

    const selectedCountry = computed(() => countries.value.find(c => c.code === form.countryCode));

    watch(() => form.phone, (newValue) => {
        const country = selectedCountry.value;
        if (!country || !country.format) return;

        let digits = newValue.replace(/\D/g, '');

        if (digits.length > country.length) {
            digits = digits.substring(0, country.length);
        }
        
        let formatted = '';
        let digitIndex = 0;
        for (const formatChar of country.format) {
            if (digitIndex >= digits.length) break;
            if (formatChar === '#') {
                formatted += digits[digitIndex];
                digitIndex++;
            } else {
                formatted += formatChar;
            }
        }
        
        if (form.phone !== formatted) {
            form.phone = formatted;
        }
    });

    watch(() => form.countryCode, () => {
        form.phone = '';
    });
</script>

<template>
  <div class="form-container">
    <h1>Inscription</h1>
    <form @submit.prevent="submit">
      <div>
        <label for="gender">Genre</label>
        <select id="gender" v-model="form.gender" required>
          <option value="" disabled>-- Veuillez sélectionner --</option>
          <option value="male">Homme</option>
          <option value="female">Femme</option>
          <option value="other">Autre</option>
        </select>
        <div v-if="form.errors.gender" class="error-message">{{ form.errors.gender }}</div>
      </div>
      <div class="name-grid">
        <div>
          <label for="firstName">Prénom</label>
          <input id="firstName" v-model="form.firstName" type="text" required>
          <div v-if="form.errors.firstName" class="error-message">{{ form.errors.firstName }}</div>
        </div>
        <div>
          <label for="lastName">Nom</label>
          <input id="lastName" v-model="form.lastName" type="text" required>
          <div v-if="form.errors.lastName" class="error-message">{{ form.errors.lastName }}</div>
        </div>
      </div>
      <div>
        <label for="email">Email</label>
        <input id="email" v-model="form.email" type="email" required>
        <div v-if="form.errors.email" class="error-message">{{ form.errors.email }}</div>
      </div>
      <div>
        <label for="phone">Téléphone</label>
        <div class="phone-input-wrapper">
          <select v-model="form.countryCode">
            <option v-for="country in countries" :key="country.code" :value="country.code">
              {{ country.name }}
            </option>
          </select>
          <input id="phone" v-model="form.phone" type="tel" inputmode="numeric" :placeholder="selectedCountry?.format" required>
        </div>
        <div v-if="form.errors.phone" class="error-message">{{ form.errors.phone }}</div>
      </div>
      <div class="address-grid">
        <div>
          <label for="address_line_1">Adresse (Ligne 1)</label>
          <input id="address_line_1" v-model="form.address_line_1" type="text" required>
          <div v-if="form.errors.address_line_1" class="error-message">{{ form.errors.address_line_1 }}</div>
        </div>
        <div>
          <label for="address_line_2">Adresse (Ligne 2)</label>
          <input id="address_line_2" v-model="form.address_line_2" type="text">
          <div v-if="form.errors.address_line_2" class="error-message">{{ form.errors.address_line_2 }}</div>
        </div>
        <div>
          <label for="zip_code">Code Postal</label>
          <input id="zip_code" v-model="form.zip_code" type="text" required>
          <div v-if="form.errors.zip_code" class="error-message">{{ form.errors.zip_code }}</div>
        </div>
        <div>
          <label for="city">Ville</label>
          <input id="city" v-model="form.city" type="text" required>
          <div v-if="form.errors.city" class="error-message">{{ form.errors.city }}</div>
        </div>
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
      <div class="password-wrapper">
        <label for="password_confirmation">Confirmation du mot de passe</label>
        <input id="password_confirmation" v-model="form.password_confirmation" :type="showPasswordConfirmation ? 'text' : 'password'" required>
        <span class="toggle-password" @click="showPasswordConfirmation = !showPasswordConfirmation">
          <svg v-if="!showPasswordConfirmation" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 0-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588M5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
            <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>
          </svg>
        </span>
      </div>
      <div>
        <button type="submit" :disabled="form.processing">S'inscrire</button>
      </div>
    </form>
    <div class="text-center">
      <p>Déjà un compte ? <Link href="/login">Se connecter</Link></p>
    </div>
  </div>
</template>

<style scoped>
.form-container {
  max-width: 50%;
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
input, textarea, select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  background-color: #fff;
  font-family: inherit;
  font-size: 1em;
}
textarea {
  resize: vertical;
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
  padding-right: 40px; /* Espace pour l'icône */
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
.phone-input-wrapper {
  display: flex;
}
.phone-input-wrapper select {
  width: 25%;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-right: none;
  border-radius: 4px 0 0 4px;
  background-color: #f8f9fa;
}
.phone-input-wrapper input {
  width: 100%;
  border-radius: 0 4px 4px 0;
}
.address-grid, .name-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1rem;
}
.address-grid > div, .name-grid > div {
  margin-bottom: 0;
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