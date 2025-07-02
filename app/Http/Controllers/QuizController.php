<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class QuizController extends Controller
{
    /**
     * Catégories de quiz autorisées
     */
    private const ALLOWED_CATEGORIES = ['math', 'francais', 'informatique'];

    /**
     * Configuration des catégories
     */
    private function getCategoryConfig(): array
    {
        return [
            'math' => [
                'title' => 'Quiz de Mathématiques',
                'description' => 'Testez vos connaissances en algèbre et géométrie',
                'difficulty' => 'Intermédiaire',
                'estimatedTime' => '15 min',
                'questionCount' => 20,
                'h5pContentId' => env('H5P_MATH_CONTENT_ID', 'math-quiz-1'),
                'icon' => 'calculator',
                'color' => '#2563eb',
                'enabled' => env('QUIZ_MATH_ENABLED', true)
            ],
            'francais' => [
                'title' => 'Quiz de Français',
                'description' => 'Grammaire, orthographe, conjugaison et littérature',
                'difficulty' => 'Intermédiaire',
                'estimatedTime' => '12 min',
                'questionCount' => 15,
                'h5pContentId' => env('H5P_FRENCH_CONTENT_ID', 'french-quiz-1'),
                'icon' => 'book',
                'color' => '#059669',
                'enabled' => env('QUIZ_FRENCH_ENABLED', true)
            ],
            'informatique' => [
                'title' => 'Quiz d\'Informatique',
                'description' => 'Programmation, algorithmes et technologies web',
                'difficulty' => 'Avancé',
                'estimatedTime' => '20 min',
                'questionCount' => 25,
                'h5pContentId' => env('H5P_COMPUTER_CONTENT_ID', 'computer-quiz-1'),
                'icon' => 'code',
                'color' => '#7c3aed',
                'enabled' => env('QUIZ_COMPUTER_ENABLED', true)
            ]
        ];
    }

    /**
     * Configuration H5P globale
     */
    private function getH5PSettings(): array
    {
        return [
            'baseUrl' => env('H5P_BASE_URL', 'https://h5p.org'),
            'embedMode' => env('H5P_INTEGRATION_MODE', 'embed') === 'embed',
            'language' => env('H5P_LANGUAGE', 'fr'),
            'enableDownload' => env('H5P_ENABLE_DOWNLOAD', false),
            'enableEmbed' => env('H5P_ENABLE_EMBED', false),
            'enableCopyright' => env('H5P_ENABLE_COPYRIGHT', false),
            'enableFullscreen' => env('H5P_ENABLE_FULLSCREEN', true),
            'enableReuse' => env('H5P_ENABLE_REUSE', false),
            'autoResize' => true,
            'saveFreq' => 30,
            'l10n' => [
                'language' => env('H5P_LANGUAGE', 'fr'),
                'fullscreen' => 'Plein écran',
                'disableFullscreen' => 'Quitter le plein écran',
                'download' => 'Télécharger',
                'copyrights' => 'Droits d\'utilisation',
                'embed' => 'Intégrer'
            ]
        ];
    }

    /**
     * Page d'accueil des quiz (liste des catégories)
     */
    public function index()
    {
        $categories = collect($this->getCategoryConfig())
            ->filter(fn($config) => $config['enabled'])
            ->map(function($config, $key) {
                return [
                    'id' => $key,
                    'title' => $config['title'],
                    'description' => $config['description'],
                    'estimatedTime' => $config['estimatedTime'],
                    'questionCount' => $config['questionCount'],
                    'difficulty' => $config['difficulty'],
                    'icon' => $config['icon'],
                    'color' => $config['color'],
                    'url' => route('quiz.category', $key)
                ];
            })
            ->values();

        return Inertia::render('QuizList', [
            'categories' => $categories,
            'stats' => $this->getGlobalStats()
        ]);
    }

    /**
     * Page de quiz pour une catégorie spécifique
     */
    public function show(string $category)
    {
        // Validation de la catégorie
        if (!in_array($category, self::ALLOWED_CATEGORIES)) {
            abort(404, 'Catégorie de quiz non trouvée');
        }

        $categoryConfig = $this->getCategoryConfig()[$category];

        // Vérifier si la catégorie est activée
        if (!$categoryConfig['enabled']) {
            abort(503, 'Cette catégorie de quiz est temporairement indisponible');
        }

        // Récupérer la progression sauvegardée (si elle existe)
        $savedProgress = $this->getSavedProgress($category);

        return Inertia::render('Quiz', [
            'category' => $category,
            'quizConfig' => $categoryConfig,
            'h5pSettings' => $this->getH5PSettings(),
            'savedProgress' => $savedProgress,
            'canRetry' => $this->canRetryQuiz($category),
            'previousAttempts' => $this->getPreviousAttempts($category)
        ]);
    }

    /**
     * Sauvegarder la progression du quiz
     */
    public function saveProgress(Request $request, string $category)
    {
        $validated = $request->validate([
            'currentQuestion' => 'required|integer|min:0',
            'totalQuestions' => 'required|integer|min:1',
            'answers' => 'array',
            'timeSpent' => 'integer|min:0',
            'h5pState' => 'string|nullable' // État interne H5P
        ]);

        $progressKey = $this->getProgressKey($category);

        $progressData = [
            'category' => $category,
            'currentQuestion' => $validated['currentQuestion'],
            'totalQuestions' => $validated['totalQuestions'],
            'answers' => $validated['answers'] ?? [],
            'timeSpent' => $validated['timeSpent'] ?? 0,
            'h5pState' => $validated['h5pState'] ?? null,
            'savedAt' => Carbon::now(),
            'sessionId' => Session::getId()
        ];

        // Sauvegarder en session ET en cache (avec TTL)
        Session::put($progressKey, $progressData);
        Cache::put($progressKey, $progressData, Carbon::now()->addHours(24));

        Log::info("Progression sauvegardée pour {$category}", [
            'question' => $validated['currentQuestion'],
            'session' => Session::getId()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Progression sauvegardée',
            'savedAt' => $progressData['savedAt']->toISOString()
        ]);
    }

    /**
     * Récupérer la progression sauvegardée
     */
    public function getProgress(string $category)
    {
        $progress = $this->getSavedProgress($category);

        return response()->json([
            'progress' => $progress,
            'hasProgress' => !is_null($progress)
        ]);
    }

    /**
     * Soumettre les résultats du quiz
     */
    public function submitResults(Request $request, string $category)
    {
        $validated = $request->validate([
            'score' => 'required|integer|min:0',
            'maxScore' => 'required|integer|min:1',
            'timeTaken' => 'required|string',
            'timeInSeconds' => 'integer|min:0',
            'answers' => 'array',
            'h5pResults' => 'array|nullable'
        ]);

        // Calculer le pourcentage
        $percentage = round(($validated['score'] / $validated['maxScore']) * 100, 1);

        // Déterminer le niveau de performance
        $performance = $this->calculatePerformance($percentage);

        // Générer un badge si applicable
        $badge = $this->generateBadge($category, $percentage);

        // Enregistrer les résultats
        $resultData = [
            'category' => $category,
            'score' => $validated['score'],
            'maxScore' => $validated['maxScore'],
            'percentage' => $percentage,
            'timeTaken' => $validated['timeTaken'],
            'timeInSeconds' => $validated['timeInSeconds'] ?? 0,
            'performance' => $performance,
            'badge' => $badge,
            'answers' => $validated['answers'] ?? [],
            'h5pResults' => $validated['h5pResults'] ?? [],
            'completedAt' => Carbon::now(),
            'sessionId' => Session::getId()
        ];

        // Sauvegarder les résultats
        $this->saveQuizResults($category, $resultData);

        // Supprimer la progression sauvegardée
        $this->clearSavedProgress($category);

        // Calculer le classement (exemple)
        $ranking = $this->calculateRanking($category, $percentage);

        Log::info("Quiz {$category} terminé", [
            'score' => $validated['score'],
            'percentage' => $percentage,
            'performance' => $performance
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Résultats enregistrés avec succès',
            'results' => $resultData,
            'ranking' => $ranking,
            'canRetryAt' => $this->getNextRetryTime($category)
        ]);
    }

    /**
     * Redémarrer un quiz (supprimer la progression)
     */
    public function restart(string $category)
    {
        $this->clearSavedProgress($category);

        return response()->json([
            'success' => true,
            'message' => 'Quiz redémarré'
        ]);
    }

    /**
     * Obtenir les statistiques générales
     */
    public function getStats(string $category = null)
    {
        if ($category) {
            return response()->json($this->getCategoryStats($category));
        }

        return response()->json($this->getGlobalStats());
    }

    // ===============================================
    // Méthodes privées utilitaires
    // ===============================================

    private function getProgressKey(string $category): string
    {
        return "quiz_progress_{$category}_" . Session::getId();
    }

    private function getSavedProgress(string $category): ?array
    {
        $progressKey = $this->getProgressKey($category);

        // Essayer la session d'abord, puis le cache
        $progress = Session::get($progressKey) ?? Cache::get($progressKey);

        // Vérifier que la progression n'est pas trop ancienne (24h)
        if ($progress && isset($progress['savedAt'])) {
            $savedAt = Carbon::parse($progress['savedAt']);
            if ($savedAt->diffInHours(Carbon::now()) > 24) {
                $this->clearSavedProgress($category);
                return null;
            }
        }

        return $progress;
    }

    private function clearSavedProgress(string $category): void
    {
        $progressKey = $this->getProgressKey($category);
        Session::forget($progressKey);
        Cache::forget($progressKey);
    }

    private function calculatePerformance(float $percentage): array
    {
        if ($percentage >= env('QUIZ_EXCELLENT_SCORE', 80)) {
            return [
                'level' => 'excellent',
                'title' => 'Excellent !',
                'message' => 'Vous maîtrisez parfaitement le sujet',
                'color' => '#10b981'
            ];
        } elseif ($percentage >= env('QUIZ_PASSING_SCORE', 60)) {
            return [
                'level' => 'good',
                'title' => 'Bien joué !',
                'message' => 'Bonne compréhension du sujet',
                'color' => '#3b82f6'
            ];
        } elseif ($percentage >= 40) {
            return [
                'level' => 'average',
                'title' => 'Pas mal',
                'message' => 'Quelques points à revoir',
                'color' => '#f59e0b'
            ];
        } else {
            return [
                'level' => 'poor',
                'title' => 'À améliorer',
                'message' => 'Continuez à vous entraîner',
                'color' => '#ef4444'
            ];
        }
    }

    private function generateBadge(string $category, float $percentage): ?array
    {
        if ($percentage >= 95) {
            return [
                'name' => 'Perfectionniste',
                'description' => 'Score parfait ou quasi-parfait',
                'icon' => '🏆',
                'rarity' => 'legendary'
            ];
        } elseif ($percentage >= 85) {
            return [
                'name' => 'Expert en ' . ucfirst($category),
                'description' => 'Excellente maîtrise du sujet',
                'icon' => '⭐',
                'rarity' => 'rare'
            ];
        }

        return null;
    }

    private function canRetryQuiz(string $category): bool
    {
        // Logique pour vérifier si l'utilisateur peut refaire le quiz
        // (limites de tentatives, cooldown, etc.)
        $maxAttempts = env('QUIZ_MAX_ATTEMPTS', 3);
        $attempts = $this->getAttemptCount($category);

        return $attempts < $maxAttempts;
    }

    private function getAttemptCount(string $category): int
    {
        // Récupérer le nombre de tentatives depuis la session/cache
        $attemptsKey = "quiz_attempts_{$category}_" . Session::getId();
        return Session::get($attemptsKey, 0);
    }

    private function getPreviousAttempts(string $category): array
    {
        // Récupérer l'historique des tentatives
        $historyKey = "quiz_history_{$category}_" . Session::getId();
        return Session::get($historyKey, []);
    }

    private function saveQuizResults(string $category, array $resultData): void
    {
        // Sauvegarder dans l'historique de session
        $historyKey = "quiz_history_{$category}_" . Session::getId();
        $history = Session::get($historyKey, []);
        $history[] = $resultData;
        Session::put($historyKey, $history);

        // Incrémenter le compteur de tentatives
        $attemptsKey = "quiz_attempts_{$category}_" . Session::getId();
        $attempts = Session::get($attemptsKey, 0) + 1;
        Session::put($attemptsKey, $attempts);

        // Ici tu pourrais aussi sauvegarder en base de données
        // QuizResult::create($resultData);
    }

    private function calculateRanking(string $category, float $percentage): array
    {
        // Simulation de classement - à remplacer par une vraie logique
        if ($percentage >= 90) return ['percentile' => 'Top 5%', 'position' => rand(1, 5)];
        if ($percentage >= 80) return ['percentile' => 'Top 15%', 'position' => rand(6, 15)];
        if ($percentage >= 70) return ['percentile' => 'Top 35%', 'position' => rand(16, 35)];
        if ($percentage >= 60) return ['percentile' => 'Top 60%', 'position' => rand(36, 60)];

        return ['percentile' => 'Bottom 40%', 'position' => rand(61, 100)];
    }

    private function getNextRetryTime(string $category): ?string
    {
        // Si il y a un cooldown entre les tentatives
        return null; // Pas de restriction pour l'instant
    }

    private function getGlobalStats(): array
    {
        return [
            'totalQuizzes' => count(array_filter($this->getCategoryConfig(), fn($c) => $c['enabled'])),
            'totalAttempts' => 1250, // Exemple - à récupérer de la BDD
            'averageScore' => 76.5,  // Exemple
            'completionRate' => 85.2 // Exemple
        ];
    }

    private function getCategoryStats(string $category): array
    {
        return [
            'category' => $category,
            'attempts' => 350,      // Exemple
            'averageScore' => 78.3, // Exemple
            'completionRate' => 88.1, // Exemple
            'topScore' => 98        // Exemple
        ];
    }
}
