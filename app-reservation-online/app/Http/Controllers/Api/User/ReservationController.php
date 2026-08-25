<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Salle;
use App\Models\Equipement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reservations = Reservation::with(['salle', 'equipements'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reservations,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'salle_id' => ['required', 'integer', 'exists:salles,id'],

            'date_heure_debut' => [
                'required',
                'date',
                'after:now',
            ],

            'date_heure_fin' => [
                'required',
                'date',
                'after:date_heure_debut',
            ],

            'nombre_personnes' => [
                'required',
                'integer',
                'min:1',
            ],

            'equipements' => [
                'nullable',
                'array',
            ],

            'equipements.*.equipement_id' => [
                'required',
                'integer',
                'distinct',
                'exists:equipements,id',
            ],

            'equipements.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $salle = Salle::findOrFail($validated['salle_id']);

        // Vérification de la capacité de la salle
        if ($validated['nombre_personnes'] > $salle->capacite) {
            throw ValidationException::withMessages([
                'nombre_personnes' => [
                    "Cette salle a une capacité maximale de {$salle->capacite} personnes.",
                ],
            ]);
        }

        // Vérification de la disponibilité de la salle
        if (
            !$salle->estDisponible(
                $validated['date_heure_debut'],
                $validated['date_heure_fin']
            )
        ) {
            throw ValidationException::withMessages([
                'salle_id' => [
                    "La salle n'est pas disponible pour cette période.",
                ],
            ]);
        }

        $reservation = DB::transaction(function () use ($validated, $request) {

            $reservation = Reservation::create([
                'date_heure_debut' => $validated['date_heure_debut'],
                'date_heure_fin' => $validated['date_heure_fin'],
                'nombre_personnes' => $validated['nombre_personnes'],
                'status' => 'en_attente',
                'user_id' => $request->user()->id,
                'salle_id' => $validated['salle_id'],
            ]);

            if (!empty($validated['equipements'])) {
                $equipements = [];

                foreach ($validated['equipements'] as $equipementData) {
                    $equipement = Equipement::findOrFail(
                        $equipementData['equipement_id']
                    );

                    $disponible = $equipement->quantiteDisponible(
                        $validated['date_heure_debut'],
                        $validated['date_heure_fin']
                    );

                    if ($equipementData['quantity'] > $disponible) {
                        throw ValidationException::withMessages([
                            'equipements' => [
                                "Il ne reste que {$disponible} unité(s) disponible(s) pour {$equipement->nom} sur cette période.",
                            ],
                        ]);
                    }

                    $equipements[$equipement->id] = [
                        'quantity' => $equipementData['quantity'],
                    ];
                }

                $reservation->equipements()->attach($equipements);
            }

            return $reservation;
        });

        $reservation->load(['salle', 'equipements']);

        return response()->json([
            'success' => true,
            'message' => 'Réservation créée avec succès.',
            'data' => $reservation,
        ], 201);
    }

    public function destroy(Request $request, Reservation $reservation): JsonResponse
    {
        if ($reservation->user_id !== $request->user()->id) {
            abort(403, 'Vous ne pouvez annuler que vos propres réservations.');
        }

        if (!in_array($reservation->status, ['en_attente', 'confirmee'])) {
            throw ValidationException::withMessages([
                'status' => ["Cette réservation ne peut plus être annulée (statut actuel : {$reservation->status})."],
            ]);
        }

        $reservation->update(['status' => 'annulee']);

        return response()->json([
            'success' => true,
            'message' => 'Réservation annulée avec succès.',
        ]);
    }
}
