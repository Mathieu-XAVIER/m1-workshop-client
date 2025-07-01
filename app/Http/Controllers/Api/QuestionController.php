<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Http\Resources\QuestionResource;
use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    /**
     * Liste des questions
     */
    public function index(): JsonResponse
    {
        try {
            $questions = Question::all();
            return response()->json([
                'success' => true,
                'data' => QuestionResource::collection($questions)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des questions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher une question
     */
    public function show($id): JsonResponse
    {
        try {
            $question = Question::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new QuestionResource($question)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Question non trouvée',
                'error' => "Aucune question trouvée avec l'ID: {$id}"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la question',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une nouvelle question
     */
    public function store(Request $request): JsonResponse
    {
        $statusValues = array_map(fn($case) => $case->value, QuestionStatus::cases());
        $typeValues = array_map(fn($case) => $case->value, QuestionType::cases());

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'status' => ['required', 'string', Rule::in($statusValues)],
            'type' => ['required', 'string', Rule::in($typeValues)],
            'level' => 'required|integer|min:1|max:10',
            'locale' => 'required|string|size:2',
            'content' => 'required|array',
            'content.question' => 'required|string',
            'answer' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors(),
                'allowed_values' => [
                    'status' => $statusValues,
                    'type' => $typeValues,
                    'level' => 'Entre 1 et 10'
                ]
            ], 422);
        }

        try {
            $question = Question::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Question créée avec succès',
                'data' => new QuestionResource($question)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la question',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour une question
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $question = Question::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Question non trouvée',
                'error' => "Aucune question trouvée avec l'ID: {$id}"
            ], 404);
        }

        $statusValues = array_map(fn($case) => $case->value, QuestionStatus::cases());
        $typeValues = array_map(fn($case) => $case->value, QuestionType::cases());

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'status' => ['sometimes', 'string', Rule::in($statusValues)],
            'type' => ['sometimes', 'string', Rule::in($typeValues)],
            'level' => 'sometimes|integer|min:1|max:10',
            'locale' => 'sometimes|string|size:2',
            'content' => 'sometimes|array',
            'answer' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors(),
                'allowed_values' => [
                    'status' => $statusValues,
                    'type' => $typeValues
                ]
            ], 422);
        }

        try {
            $question->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Question mise à jour avec succès',
                'data' => new QuestionResource($question)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une question
     */
    public function destroy($id): JsonResponse
    {
        try {
            $question = Question::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Question non trouvée',
                'error' => "Aucune question trouvée avec l'ID: {$id}"
            ], 404);
        }

        try {
            $question->delete();

            return response()->json([
                'success' => true,
                'message' => 'Question supprimée avec succès',
                'data' => [
                    'deleted_question_id' => $id,
                    'deleted_at' => now()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getEnumValues(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'status' => array_map(fn($case) => $case->value, QuestionStatus::cases()),
                'type' => array_map(fn($case) => $case->value, QuestionType::cases()),
                'level' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
            ]
        ]);
    }
}
