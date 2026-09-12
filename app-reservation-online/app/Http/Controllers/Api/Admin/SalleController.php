<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSalleRequest;
use App\Http\Requests\Admin\UpdateSalleRequest;
use App\Http\Resources\SalleResource;
use App\Models\Salle;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class SalleController extends Controller
{
    /**
     * Liste toutes les salles avec filtres et recherche optionnels.
     */
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try {
            $query = Salle::query()->with('images')->withCount('reservations')->latest();

            // Filtre par statut (disponible, indisponible)
            if ($request->filled('status')) {
                $query->status($request->status);
            }

            // Recherche par nom, localisation ou description
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            // Filtre par capacité minimale
            if ($request->filled('min_capacite')) {
                $query->where('capacite', '>=', (int) $request->min_capacite);
            }

            // Filtre par capacité maximale
            if ($request->filled('max_capacite')) {
                $query->where('capacite', '<=', (int) $request->max_capacite);
            }

            // Filtre par prix max
            if ($request->filled('max_prix')) {
                $query->where('prix', '<=', (float) $request->max_prix);
            }

            // Sans pagination si demandé
            if ($request->get('all') === 'true' || $request->get('paginate') === 'false') {
                $salles = $query->get();
                return response()->json([
                    'message' => 'Liste des salles récupérée avec succès.',
                    'data' => SalleResource::collection($salles),
                ], 200);
            }

            $perPage = (int) $request->get('per_page', 15);
            $salles = $query->paginate($perPage);

            return response()->json([
                'message' => 'Liste des salles récupérée avec succès.',
                'data' => SalleResource::collection($salles),
                'meta' => [
                    'current_page' => $salles->currentPage(),
                    'last_page' => $salles->lastPage(),
                    'per_page' => $salles->perPage(),
                    'total' => $salles->total(),
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération des salles.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Enregistre une nouvelle salle.
     */
    public function store(StoreSalleRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $salle = Salle::create([
                'nom' => $validated['nom'],
                'capacite' => $validated['capacite'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'] ?? 'disponible',
                'localisation' => $validated['localisation'],
                'prix' => $validated['prix'],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Salle créée avec succès.',
                'data' => new SalleResource($salle),
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Une erreur est survenue lors de la création de la salle.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Affiche les détails d'une salle spécifique.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $salle = Salle::with('images')->withCount('reservations')->find($id);

            if (!$salle) {
                return response()->json([
                    'message' => 'Salle non trouvée.',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'message' => 'Détails de la salle récupérés avec succès.',
                'data' => new SalleResource($salle),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération de la salle.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Met à jour les informations d'une salle.
     */
    public function update(UpdateSalleRequest $request, string $id): JsonResponse
    {
        $salle = Salle::find($id);

        if (!$salle) {
            return response()->json([
                'message' => 'Salle non trouvée.',
                'data' => null,
            ], 404);
        }

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $salle->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Salle mise à jour avec succès.',
                'data' => new SalleResource($salle->fresh(['images'])->loadCount('reservations')),
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour de la salle.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Supprime une salle.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $salle = Salle::find($id);

            if (!$salle) {
                return response()->json([
                    'message' => 'Salle non trouvée.',
                    'data' => null,
                ], 404);
            }

            $salle->delete();

            return response()->json([
                'message' => 'Salle supprimée avec succès.',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression de la salle.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
