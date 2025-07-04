<?php

namespace Database\Seeders;

use App\Enums\BlocQuestionSkill;
use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use App\Models\Bloc;
use App\Models\Question;
use App\Models\Quizz;
use Illuminate\Database\Seeder;

class FrancaisQuizzSeeder extends Seeder
{
    public function run(): void
    {
        // Création du quizz principal
        $quizz = Quizz::create([
            'title' => 'Quiz de Français',
            'subject' => 'Langue française',
        ]);

        // Création des blocs
        $blocGrammaire = Bloc::create([
            'name' => 'Grammaire',
            'nb_questions' => 5,
            'quizz_id' => $quizz->id,
        ]);

        $blocConjugaison = Bloc::create([
            'name' => 'Conjugaison',
            'nb_questions' => 5,
            'quizz_id' => $quizz->id,
        ]);

        $blocOrthographe = Bloc::create([
            'name' => 'Orthographe',
            'nb_questions' => 5,
            'quizz_id' => $quizz->id,
        ]);

        $blocLitterature = Bloc::create([
            'name' => 'Littérature',
            'nb_questions' => 5,
            'quizz_id' => $quizz->id,
        ]);

        // Création des questions pour le bloc Grammaire
        $questionsGrammaire = [
            [
                'title' => 'Accord du participe passé',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 2,
                'content' => [
                    'question' => 'Quelle est la règle d\'accord du participe passé avec l\'auxiliaire "avoir" ?',
                    'options' => [
                        'Il s\'accorde toujours avec le sujet',
                        'Il s\'accorde avec le COD si celui-ci est placé avant',
                        'Il ne s\'accorde jamais',
                        'Il s\'accorde uniquement avec les verbes du premier groupe'
                    ]
                ],
                'answer' => [
                    'correct' => 1,
                    'explanation' => 'Le participe passé employé avec l\'auxiliaire "avoir" s\'accorde avec le COD si celui-ci est placé avant le verbe.'
                ],
                'skill' => BlocQuestionSkill::WRITING
            ],
            [
                'title' => 'Fonction du pronom relatif',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 3,
                'content' => [
                    'question' => 'Dans la phrase "La personne à qui je parle", quelle est la fonction du pronom relatif "qui" ?',
                    'options' => [
                        'Sujet',
                        'Complément d\'objet direct',
                        'Complément d\'objet indirect',
                        'Complément circonstanciel'
                    ]
                ],
                'answer' => [
                    'correct' => 2,
                    'explanation' => 'Dans cette phrase, "qui" est un complément d\'objet indirect car on dit "parler à quelqu\'un".'
                ],
                'skill' => BlocQuestionSkill::READING
            ],
            [
                'title' => 'Nature des mots',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 1,
                'content' => [
                    'question' => 'Quelle est la nature du mot "rapidement" ?',
                    'options' => [
                        'Adjectif',
                        'Adverbe',
                        'Nom',
                        'Verbe'
                    ]
                ],
                'answer' => [
                    'correct' => 1,
                    'explanation' => '"Rapidement" est un adverbe qui modifie un verbe, un adjectif ou un autre adverbe.'
                ],
                'skill' => BlocQuestionSkill::READING
            ],
            [
                'title' => 'Proposition subordonnée',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 2,
                'content' => [
                    'question' => 'Dans la phrase "Je pense que tu as raison", quel type de proposition subordonnée est "que tu as raison" ?',
                    'options' => [
                        'Complétive',
                        'Relative',
                        'Circonstancielle',
                        'Interrogative indirecte'
                    ]
                ],
                'answer' => [
                    'correct' => 0,
                    'explanation' => 'Il s\'agit d\'une proposition subordonnée complétive car elle complète le verbe "penser".'
                ],
                'skill' => BlocQuestionSkill::WRITING
            ],
            [
                'title' => 'Voix passive',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 2,
                'content' => [
                    'question' => 'Quelle est la transformation à la voix passive de la phrase "Le chat mange la souris" ?',
                    'options' => [
                        'La souris est mangée par le chat',
                        'La souris mange le chat',
                        'Le chat est mangé par la souris',
                        'La souris a été mangée par le chat'
                    ]
                ],
                'answer' => [
                    'correct' => 0,
                    'explanation' => 'À la voix passive, le COD de la phrase active devient sujet et le sujet devient complément d\'agent.'
                ],
                'skill' => BlocQuestionSkill::WRITING
            ],
        ];

        // Création des questions pour le bloc Conjugaison
        $questionsConjugaison = [
            [
                'title' => 'Temps du subjonctif',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 3,
                'content' => [
                    'question' => 'Quelle est la conjugaison correcte du verbe "faire" au subjonctif présent à la 1ère personne du pluriel ?',
                    'options' => [
                        'que nous faisons',
                        'que nous fassions',
                        'que nous fesions',
                        'que nous fairions'
                    ]
                ],
                'answer' => [
                    'correct' => 1,
                    'explanation' => 'La forme correcte est "que nous fassions".'
                ],
                'skill' => BlocQuestionSkill::WRITING
            ],
            [
                'title' => 'Concordance des temps',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 2,
                'content' => [
                    'question' => 'Dans la phrase "Il a dit qu\'il ... demain", quel temps doit-on utiliser ?',
                    'options' => [
                        'viendrait',
                        'viendra',
                        'vient',
                        'venait'
                    ]
                ],
                'answer' => [
                    'correct' => 0,
                    'explanation' => 'Après un verbe principal au passé, on utilise le conditionnel présent pour exprimer une action future par rapport au moment du discours.'
                ],
                'skill' => BlocQuestionSkill::WRITING
            ],
            [
                'title' => 'Participe présent',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 2,
                'content' => [
                    'question' => 'Quel est le participe présent du verbe "prendre" ?',
                    'options' => [
                        'prenant',
                        'prennant',
                        'prenent',
                        'prendent'
                    ]
                ],
                'answer' => [
                    'correct' => 0,
                    'explanation' => 'Le participe présent de "prendre" est "prenant".'
                ],
                'skill' => BlocQuestionSkill::WRITING
            ],
            [
                'title' => 'Impératif',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 1,
                'content' => [
                    'question' => 'Quelle est la forme correcte de l\'impératif présent du verbe "aller" à la 2e personne du singulier ?',
                    'options' => [
                        'va',
                        'vas',
                        'aller',
                        'va-t'
                    ]
                ],
                'answer' => [
                    'correct' => 0,
                    'explanation' => 'L\'impératif présent à la 2e personne du singulier du verbe "aller" est "va".'
                ],
                'skill' => BlocQuestionSkill::READING
            ],
            [
                'title' => 'Passé simple',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 3,
                'content' => [
                    'question' => 'Quelle est la conjugaison correcte du verbe "voir" au passé simple à la 3e personne du pluriel ?',
                    'options' => [
                        'ils voyaient',
                        'ils virent',
                        'ils voirent',
                        'ils vèrent'
                    ]
                ],
                'answer' => [
                    'correct' => 1,
                    'explanation' => 'La forme correcte est "ils virent".'
                ],
                'skill' => BlocQuestionSkill::READING
            ],
        ];

        // Création des questions pour le bloc Orthographe
        $questionsOrthographe = [
            [
                'title' => 'Homophones',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 1,
                'content' => [
                    'question' => 'Quelle est l\'orthographe correcte dans la phrase "... est-ce que tu viens ?" ?',
                    'options' => [
                        'Quand',
                        'Quant',
                        'Qu\'en',
                        'Kant'
                    ]
                ],
                'answer' => [
                    'correct' => 0,
                    'explanation' => '"Quand" exprime le temps, "quant" signifie "en ce qui concerne", "qu\'en" est la contraction de "que en".'
                ],
                'skill' => BlocQuestionSkill::WRITING
            ],
            [
                'title' => 'Pluriel des noms composés',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 2,
                'content' => [
                    'question' => 'Quel est le pluriel correct de "arc-en-ciel" ?',
                    'options' => [
                        'arc-en-ciels',
                        'arcs-en-ciel',
                        'arcs-en-ciels',
                        'arc-en-ciel'
                    ]
                ],
                'answer' => [
                    'correct' => 1,
                    'explanation' => 'Le pluriel de "arc-en-ciel" est "arcs-en-ciel" car seul le nom "arc" prend la marque du pluriel.'
                ],
                'skill' => BlocQuestionSkill::WRITING
            ],
            [
                'title' => 'Accent circonflexe',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 2,
                'content' => [
                    'question' => 'Parmi ces mots, lequel prend un accent circonflexe ?',
                    'options' => [
                        'abime',
                        'gite',
                        'maitre',
                        'boite'
                    ]
                ],
                'answer' => [
                    'correct' => 2,
                    'explanation' => 'Depuis la réforme de l\'orthographe, l\'accent circonflexe est facultatif sur i et u sauf dans quelques cas, mais "maître" conserve son accent.'
                ],
                'skill' => BlocQuestionSkill::READING
            ],
            [
                'title' => 'Tout ou tous',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 1,
                'content' => [
                    'question' => 'Quelle est la forme correcte dans la phrase "Ils sont ... venus." ?',
                    'options' => [
                        'tout',
                        'tous',
                        'toute',
                        'toutes'
                    ]
                ],
                'answer' => [
                    'correct' => 1,
                    'explanation' => 'On écrit "tous" car il s\'agit d\'un pronom qui se rapporte à "ils".'
                ],
                'skill' => BlocQuestionSkill::READING
            ],
            [
                'title' => 'Terminaison verbale',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 2,
                'content' => [
                    'question' => 'Comment s\'écrit le verbe dans la phrase "Il faut que tu ... ton travail" ?',
                    'options' => [
                        'finis',
                        'finisses',
                        'finit',
                        'fini'
                    ]
                ],
                'answer' => [
                    'correct' => 1,
                    'explanation' => 'Après "il faut que", on utilise le subjonctif présent, donc "finisses".'
                ],
                'skill' => BlocQuestionSkill::WRITING
            ],
        ];

        $questionsLitterature = [
            [
                'title' => 'Les Misérables',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 1,
                'content' => [
                    'question' => 'Qui a écrit "Les Misérables" ?',
                    'options' => [
                        'Émile Zola',
                        'Victor Hugo',
                        'Gustave Flaubert',
                        'Alexandre Dumas'
                    ]
                ],
                'answer' => [
                    'correct' => 1,
                    'explanation' => '"Les Misérables" est un roman de Victor Hugo publié en 1862.'
                ],
                'skill' => BlocQuestionSkill::READING
            ],
            [
                'title' => 'Mouvement littéraire',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 2,
                'content' => [
                    'question' => 'À quel mouvement littéraire appartient Charles Baudelaire ?',
                    'options' => [
                        'Romantisme',
                        'Réalisme',
                        'Symbolisme',
                        'Naturalisme'
                    ]
                ],
                'answer' => [
                    'correct' => 2,
                    'explanation' => 'Charles Baudelaire est considéré comme un précurseur du symbolisme.'
                ],
                'skill' => BlocQuestionSkill::READING
            ],
            [
                'title' => 'Pièce de théâtre',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 2,
                'content' => [
                    'question' => 'Qui a écrit la pièce "Le Malade imaginaire" ?',
                    'options' => [
                        'Jean Racine',
                        'Pierre Corneille',
                        'Molière',
                        'Jean de La Fontaine'
                    ]
                ],
                'answer' => [
                    'correct' => 2,
                    'explanation' => '"Le Malade imaginaire" est une pièce de théâtre de Molière créée en 1673.'
                ],
                'skill' => BlocQuestionSkill::LISTENING
            ],
            [
                'title' => 'Prix Nobel',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 3,
                'content' => [
                    'question' => 'Lequel de ces écrivains français a reçu le Prix Nobel de littérature ?',
                    'options' => [
                        'Marcel Proust',
                        'Émile Zola',
                        'Victor Hugo',
                        'Albert Camus'
                    ]
                ],
                'answer' => [
                    'correct' => 3,
                    'explanation' => 'Albert Camus a reçu le Prix Nobel de littérature en 1957.'
                ],
                'skill' => BlocQuestionSkill::READING
            ],
            [
                'title' => 'Figure de style',
                'status' => QuestionStatus::APPROVED,
                'type' => QuestionType::MULTIPLE_CHOICE,
                'level' => 2,
                'content' => [
                    'question' => 'Quelle figure de style est utilisée dans la phrase "Cette femme est un volcan" ?',
                    'options' => [
                        'Métaphore',
                        'Comparaison',
                        'Hyperbole',
                        'Euphémisme'
                    ]
                ],
                'answer' => [
                    'correct' => 0,
                    'explanation' => 'C\'est une métaphore car elle établit une identification entre la femme et le volcan sans utiliser de mot de comparaison.'
                ],
                'skill' => BlocQuestionSkill::SPEAKING
            ],
        ];

        $createQuestionsForBloc = function (array $questionsData, Bloc $bloc) {
            foreach ($questionsData as $questionData) {
                $skill = $questionData['skill'];
                unset($questionData['skill']);

                $question = Question::create($questionData);
                $bloc->questions()->attach($question->id, ['skill' => $skill]);
            }
        };

        $createQuestionsForBloc($questionsGrammaire, $blocGrammaire);
        $createQuestionsForBloc($questionsConjugaison, $blocConjugaison);
        $createQuestionsForBloc($questionsOrthographe, $blocOrthographe);
        $createQuestionsForBloc($questionsLitterature, $blocLitterature);
    }
}
