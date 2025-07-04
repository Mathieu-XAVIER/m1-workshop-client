<script setup lang="ts">
import {computed} from 'vue';
import {Link} from '@inertiajs/vue3';

interface User {
    id: number;
    name: string;
    email: string;
}

interface HeaderProps {
    user?: User;
    title?: string;
}

const props = withDefaults(defineProps<HeaderProps>(), {
    user: undefined,
    title: 'QuizApp'
});

const isAuthenticated = computed(() => !!props.user);
</script>

<template>
    <header class="app-header">
        <div class="header-container">
            <div class="logo-section">
                <Link href="/" class="logo">
                    <span class="logo-icon">Q</span>
                    <span class="logo-text">{{ title }}</span>
                </Link>
            </div>

            <nav class="main-nav">
                <Link href="/quiz" class="nav-link">Quiz</Link>
                <Link href="/categories" class="nav-link">Catégories</Link>
                <Link href="/about" class="nav-link">À propos</Link>
            </nav>

            <div class="user-section">
                <template v-if="isAuthenticated">
                    <div class="user-menu">
                        <span class="user-name">{{ user?.name }}</span>
                        <div class="dropdown-menu">
                            <Link href="/profile" class="dropdown-item">Profil</Link>
                            <Link href="/dashboard" class="dropdown-item">Tableau de bord</Link>
                            <Link href="/logout" method="post" as="button" class="dropdown-item logout">Déconnexion
                            </Link>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="auth-buttons">
                        <Link href="/login" class="login-button">Connexion</Link>
                        <Link href="/register" class="register-button">S'inscrire</Link>
                    </div>
                </template>
            </div>
        </div>
    </header>
</template>

<style scoped>
.app-header {
    background-color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.logo-section {
    display: flex;
    align-items: center;
}

.logo {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #111827;
    font-weight: 700;
    gap: 0.5rem;
}

.logo-icon {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
    width: 2rem;
    height: 2rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.logo-text {
    font-size: 1.2rem;
}

.main-nav {
    display: flex;
    gap: 2rem;
}

.nav-link {
    text-decoration: none;
    color: #4b5563;
    font-weight: 500;
    padding: 0.5rem 0;
    transition: color 0.2s ease;
    position: relative;
}

.nav-link:hover {
    color: #4f46e5;
}

.nav-link::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: 0;
    left: 0;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    transition: width 0.3s ease;
}

.nav-link:hover::after {
    width: 100%;
}

.user-section {
    display: flex;
    align-items: center;
}

.user-menu {
    position: relative;
    cursor: pointer;
}

.user-name {
    font-weight: 500;
    color: #111827;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.user-name:hover {
    background-color: #f3f4f6;
}

.user-name::after {
    content: '▼';
    font-size: 0.7rem;
    color: #6b7280;
}

.dropdown-menu {
    position: absolute;
    right: 0;
    top: 100%;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    width: 200px;
    padding: 0.5rem 0;
    margin-top: 0.5rem;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.2s ease;
}

.user-menu:hover .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    display: block;
    padding: 0.75rem 1rem;
    color: #374151;
    text-decoration: none;
    transition: background-color 0.2s ease;
    text-align: left;
    width: 100%;
    border: none;
    background: none;
    font-size: 1rem;
    cursor: pointer;
}

.dropdown-item:hover {
    background-color: #f3f4f6;
}

.logout {
    color: #ef4444;
}

.auth-buttons {
    display: flex;
    gap: 1rem;
}

.login-button, .register-button {
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.login-button {
    color: #4f46e5;
    border: 2px solid #4f46e5;
}

.register-button {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
}

.login-button:hover, .register-button:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .header-container {
        flex-direction: column;
        gap: 1rem;
    }

    .main-nav {
        width: 100%;
        justify-content: center;
        gap: 1.5rem;
    }

    .user-section {
        width: 100%;
        justify-content: center;
    }

    .auth-buttons {
        width: 100%;
        justify-content: center;
    }
}
</style>
