<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterStoreRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Connexion de l'utilisateur.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'L\'adresse e-mail est requise.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'password.required' => 'Le mot de passe est requis.',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->mot_de_passe)) {
                return response()->json([
                    'message' => 'Identifiants invalides (e-mail ou mot de passe incorrect).',
                    'data' => null,
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Connexion réussie',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la connexion.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Informations de l'utilisateur actuellement connecté.
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Utilisateur non authentifié.',
                    'data' => null,
                ], 401);
            }

            return response()->json([
                'message' => 'Utilisateur connecté',
                'data' => new UserResource($user),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erreur',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Déconnexion (révocation du token Sanctum).
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if ($user && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }

            return response()->json([
                'message' => 'Déconnexion réussie',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Inscription d'un nouvel utilisateur.
     */
    public function register(RegisterStoreRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $user = new User();
            $user->nom = $data['nom'];
            $user->email = $data['email'];
            $user->telephone = $data['telephone'] ?? null;
            $user->mot_de_passe = Hash::make($data['password']);
            $user->role = 'user';
            $user->save();

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'message' => 'Compte créé avec succès',
                'data' => [
                    'token' => $token,
                    'user' => new UserResource($user),
                ],
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Une erreur est survenue lors de l\'inscription.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

