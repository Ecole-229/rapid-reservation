<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Liste tous les utilisateurs avec filtres et recherche optionnels.
     */
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try {
            $query = User::query()->latest();

            // Filtre par rôle (supporte: ?role=admin, ?role=admin,responsable ou ?roles[]=admin)
            if ($request->filled('role')) {
                $query->role($request->role);
            } elseif ($request->filled('roles')) {
                $query->role($request->roles);
            }

            // Recherche par nom, email ou téléphone
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            if ($request->get('all') === 'true' || $request->get('paginate') === 'false') {
                $users = $query->get();
                return response()->json([
                    'message' => 'Liste des utilisateurs récupérée avec succès.',
                    'data' => UserResource::collection($users),
                ], 200);
            }

            $perPage = (int) $request->get('per_page', 15);
            $users = $query->paginate($perPage);

            return response()->json([
                'message' => 'Liste des utilisateurs récupérée avec succès.',
                'data' => UserResource::collection($users),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération des utilisateurs.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Liste les utilisateurs filtrés par un rôle spécifique.
     */
    public function byRole(Request $request, string $role): JsonResponse
    {
        $allowedRoles = ['admin', 'responsable', 'user'];

        if (!in_array($role, $allowedRoles, true)) {
            return response()->json([
                'message' => "Rôle invalide. Les rôles autorisés sont : " . implode(', ', $allowedRoles),
                'data' => null,
            ], 400);
        }

        try {
            $query = User::query()->role($role)->latest();

            if ($request->filled('search')) {
                $query->search($request->search);
            }

            if ($request->get('all') === 'true' || $request->get('paginate') === 'false') {
                $users = $query->get();
                return response()->json([
                    'message' => "Liste des utilisateurs avec le rôle '{$role}' récupérée avec succès.",
                    'data' => UserResource::collection($users),
                ], 200);
            }

            $perPage = (int) $request->get('per_page', 15);
            $users = $query->paginate($perPage);

            return response()->json([
                'message' => "Liste des utilisateurs avec le rôle '{$role}' récupérée avec succès.",
                'data' => UserResource::collection($users),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération des utilisateurs par rôle.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Enregistre un nouvel utilisateur (admin, responsable ou user).
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $password = $validated['password'] ?? $validated['mot_de_passe'] ?? null;

        DB::beginTransaction();
        try {
            $user = new User();
            $user->nom = $validated['nom'];
            $user->email = $validated['email'];
            $user->telephone = $validated['telephone'] ?? null;
            $user->role = $validated['role'];
            $user->mot_de_passe = Hash::make($password);
            $user->save();

            DB::commit();

            return response()->json([
                'message' => 'Utilisateur créé avec succès.',
                'data' => new UserResource($user),
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Une erreur est survenue lors de la création de l\'utilisateur.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Affiche les détails d'un utilisateur spécifique.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => 'Utilisateur non trouvé.',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'message' => 'Détails de l\'utilisateur récupérés avec succès.',
                'data' => new UserResource($user),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la récupération de l\'utilisateur.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Met à jour les informations d'un utilisateur.
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé.',
                'data' => null,
            ], 404);
        }

        $validated = $request->validated();

        DB::beginTransaction();
        try {
            if (isset($validated['nom'])) {
                $user->nom = $validated['nom'];
            }

            if (isset($validated['email'])) {
                $user->email = $validated['email'];
            }

            if (array_key_exists('telephone', $validated)) {
                $user->telephone = $validated['telephone'];
            }

            if (isset($validated['role'])) {
                $user->role = $validated['role'];
            }

            $password = $validated['password'] ?? $validated['mot_de_passe'] ?? null;
            if (!empty($password)) {
                $user->mot_de_passe = Hash::make($password);
            }

            $user->save();

            DB::commit();

            return response()->json([
                'message' => 'Utilisateur mis à jour avec succès.',
                'data' => new UserResource($user),
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour de l\'utilisateur.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'message' => 'Utilisateur non trouvé.',
                    'data' => null,
                ], 404);
            }

            // Sécurité : l'administrateur connecté ne peut pas supprimer son propre compte
            if ($request->user() && $request->user()->id === $user->id) {
                return response()->json([
                    'message' => 'Action impossible : vous ne pouvez pas supprimer votre propre compte connecté.',
                    'data' => null,
                ], 403);
            }

            $user->delete();

            return response()->json([
                'message' => 'Utilisateur supprimé avec succès.',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la suppression de l\'utilisateur.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
