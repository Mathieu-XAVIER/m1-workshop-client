<template>
    <div class="quiz-container-wrapper">
        <!-- Mode Embed H5P -->
        <div v-if="embedMode" class="h5p-embed-container">
            <iframe
                ref="h5pFrame"
                :src="h5pEmbedUrl"
                class="h5p-iframe"
                :style="containerStyles"
                frameborder="0"
                allowfullscreen
                @load="onFrameLoad"
            ></iframe>
        </div>

        <!-- Mode API H5P Direct -->
        <div v-else class="h5p-direct-container">
            <div
                ref="h5pContainer"
                class="h5p-container"
                :style="containerStyles"
                :data-content-id="h5pContentId"
            ></div>
        </div>

        <!-- Loading state personnalisable -->
        <div v-if="isLoading" class="loading-overlay">
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p class="loading-text">{{ loadingText }}</p>
            </div>
        </div>

        <!-- Error state personnalisable -->
        <div v-if="hasError" class="error-container">
            <div class="error-content">
                <svg class="error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h3 class="error-title">Erreur de chargement</h3>
                <p class="error-message">{{ errorMessage }}</p>
                <button @click="retryLoad" class="retry-button">
                    Réessayer
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

// Props personnalisables
const props = defineProps({
    h5pContentId: {
        type: String,
        required: true
    },
    h5pSettings: {
        type: Object,
        default: () => ({})
    },
    customStyles: {
        type: Object,
        default: () => ({})
    },
    embedMode: {
        type: Boolean,
        default: true // Par défaut en mode embed pour plus de simplicité
    },
    h5pBaseUrl: {
        type: String,
        default: 'https://h5p.org' // Remplacez par votre URL H5P
    },
    loadingText: {
        type: String,
        default: 'Chargement du quiz...'
    },
    autoResize: {
        type: Boolean,
        default: true
    }
})

// Émissions d'événements
const emit = defineEmits(['quiz-started', 'quiz-completed', 'quiz-progress', 'error', 'loaded'])

// État réactif
const isLoading = ref(true)
const hasError = ref(false)
const errorMessage = ref('')
const h5pFrame = ref(null)
const h5pContainer = ref(null)

// URL d'embed calculée
const h5pEmbedUrl = computed(() => {
    if (!props.h5pContentId) return ''

    const baseUrl = props.h5pBaseUrl.endsWith('/')
        ? props.h5pBaseUrl.slice(0, -1)
        : props.h5pBaseUrl

    return `${baseUrl}/embed/${props.h5pContentId}`
})

// Styles du container personnalisables
const containerStyles = computed(() => {
    return {
        width: '100%',
        minHeight: '500px',
        borderRadius: '16px',
        boxShadow: '0 10px 25px -5px rgba(0, 0, 0, 0.1)',
        overflow: 'hidden',
        background: '#ffffff',
        ...props.customStyles?.containerStyles
    }
})

// Chargement du contenu H5P
const loadH5PContent = async () => {
    try {
        isLoading.value = true
        hasError.value = false

        if (props.embedMode) {
            // Mode embed - plus simple et compatible
            await loadEmbedContent()
        } else {
            // Mode API direct - plus de contrôle mais plus complexe
            await loadDirectContent()
        }
    } catch (error) {
        console.error('Erreur lors du chargement H5P:', error)
        showError('Impossible de charger le contenu du quiz')
    }
}

// Chargement en mode embed
const loadEmbedContent = async () => {
    if (!h5pEmbedUrl.value) {
        throw new Error('URL H5P manquante')
    }

    // L'iframe se charge automatiquement
    // Le loading sera géré par onFrameLoad
}

// Chargement en mode API direct
const loadDirectContent = async () => {
    if (!window.H5P) {
        // Charger la bibliothèque H5P si nécessaire
        await loadH5PLibrary()
    }

    // Créer le contenu H5P
    const contentData = await fetchH5PContent()
    const h5pInstance = await createH5PInstance(contentData)

    // Attacher les événements
    attachH5PEvents(h5pInstance)

    isLoading.value = false
    emit('loaded')
}

// Charger la bibliothèque H5P
const loadH5PLibrary = () => {
    return new Promise((resolve, reject) => {
        if (window.H5P) {
            resolve()
            return
        }

        const script = document.createElement('script')
        script.src = `${props.h5pBaseUrl}/js/h5p-standalone-1.3.x.min.js`
        script.onload = resolve
        script.onerror = reject
        document.head.appendChild(script)
    })
}

// Récupérer les données du contenu H5P
const fetchH5PContent = async () => {
    const response = await fetch(`${props.h5pBaseUrl}/api/content/${props.h5pContentId}`)
    if (!response.ok) {
        throw new Error('Contenu H5P introuvable')
    }
    return await response.json()
}

// Créer une instance H5P
const createH5PInstance = async (contentData) => {
    const options = {
        h5pJsonPath: `${props.h5pBaseUrl}/content/${props.h5pContentId}`,
        frameJs: `${props.h5pBaseUrl}/js/h5p-standalone-1.3.x.min.js`,
        frameCss: `${props.h5pBaseUrl}/css/h5p-standalone-1.3.x.min.css`,
        ...props.h5pSettings
    }

    return new Promise((resolve) => {
        window.H5P.init(h5pContainer.value, contentData, options).then(resolve)
    })
}

// Attacher les événements H5P
const attachH5PEvents = (h5pInstance) => {
    // Événement de démarrage
    h5pInstance.on('started', () => {
        emit('quiz-started', { instance: h5pInstance })
    })

    // Événement de progression
    h5pInstance.on('progress', (event) => {
        emit('quiz-progress', {
            progress: event.data,
            instance: h5pInstance
        })
    })

    // Événement de completion
    h5pInstance.on('completed', (event) => {
        emit('quiz-completed', {
            score: event.data.score,
            maxScore: event.data.maxScore,
            timeTaken: event.data.timeTaken,
            instance: h5pInstance
        })
    })

    // Autres événements utiles
    h5pInstance.on('resize', () => {
        if (props.autoResize) {
            resizeContainer()
        }
    })
}

// Gestionnaire de chargement de l'iframe
const onFrameLoad = () => {
    isLoading.value = false
    emit('loaded')

    // Écouter les messages de l'iframe H5P
    window.addEventListener('message', handleFrameMessage)

    // Auto-resize si activé
    if (props.autoResize) {
        setupAutoResize()
    }
}

// Gérer les messages de l'iframe H5P
const handleFrameMessage = (event) => {
    if (event.origin !== new URL(props.h5pBaseUrl).origin) return

    const data = event.data

    switch (data.type) {
        case 'h5p-started':
            emit('quiz-started', data.payload)
            break
        case 'h5p-progress':
            emit('quiz-progress', data.payload)
            break
        case 'h5p-completed':
            emit('quiz-completed', data.payload)
            break
        case 'h5p-resize':
            if (props.autoResize) {
                resizeContainer(data.payload.height)
            }
            break
    }
}

// Redimensionner le container
const resizeContainer = (height = null) => {
    if (props.embedMode && h5pFrame.value) {
        if (height) {
            h5pFrame.value.style.height = `${height}px`
        }
    }
}

// Configuration de l'auto-resize
const setupAutoResize = () => {
    if (!props.embedMode || !h5pFrame.value) return

    const observer = new ResizeObserver(() => {
        // Observer les changements de taille
    })

    observer.observe(h5pFrame.value)
}

// Gestion des erreurs
const showError = (message) => {
    hasError.value = true
    errorMessage.value = message
    isLoading.value = false
    emit('error', { message })
}

// Réessayer le chargement
const retryLoad = () => {
    hasError.value = false
    loadH5PContent()
}

// Méthodes publiques exposées
const resetQuiz = () => {
    if (props.embedMode && h5pFrame.value) {
        // Recharger l'iframe
        h5pFrame.value.src = h5pFrame.value.src
    } else if (window.H5P?.instances) {
        // Redémarrer l'instance H5P
        window.H5P.instances.forEach(instance => {
            if (instance.resetTask) {
                instance.resetTask()
            }
        })
    }
}

// Exposer les méthodes publiques
defineExpose({
    resetQuiz,
    resizeContainer
})

// Watchers
watch(() => props.h5pContentId, () => {
    if (props.h5pContentId) {
        loadH5PContent()
    }
})

// Lifecycle
onMounted(() => {
    if (props.h5pContentId) {
        loadH5PContent()
    }
})

onUnmounted(() => {
    window.removeEventListener('message', handleFrameMessage)
})
</script>

<style scoped>
.quiz-container-wrapper {
    position: relative;
    width: 100%;
    margin: 2rem 0;
}

.h5p-embed-container,
.h5p-direct-container {
    position: relative;
    width: 100%;
}

.h5p-iframe {
    width: 100%;
    min-height: 500px;
    border: none;
    transition: height 0.3s ease;
}

.h5p-container {
    width: 100%;
    min-height: 500px;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    backdrop-filter: blur(5px);
    z-index: 10;
}

.loading-spinner {
    text-align: center;
}

.spinner {
    width: 48px;
    height: 48px;
    border: 4px solid #e5e7eb;
    border-top: 4px solid #4f46e5;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    color: #6b7280;
    font-weight: 500;
    margin: 0;
}

.error-container {
    background: #ffffff;
    border-radius: 16px;
    padding: 3rem;
    text-align: center;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    min-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.error-content {
    max-width: 400px;
}

.error-icon {
    width: 64px;
    height: 64px;
    color: #ef4444;
    margin: 0 auto 1.5rem;
}

.error-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
}

.error-message {
    color: #6b7280;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.retry-button {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.retry-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .h5p-iframe {
        min-height: 400px;
    }

    .error-container {
        padding: 2rem;
        min-height: 300px;
    }
}
</style>
