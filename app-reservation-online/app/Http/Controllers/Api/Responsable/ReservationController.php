<?php

namespace App\Http\Controllers\Api\Responsable;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reservations = Reservation::with(['user', 'salle', 'equipements'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reservations,
        ]);
    }

    public function confirmer(Reservation $reservation): JsonResponse
    {
        $this->verifierStatutModifiable($reservation);

        $reservation->update(['status' => 'confirmee']);

        return response()->json([
            'success' => true,
            'message' => 'Réservation confirmée.',
            'data' => $reservation->load(['user', 'salle', 'equipements']),
        ]);
    }

    public function rejeter(Reservation $reservation): JsonResponse
    {
        $this->verifierStatutModifiable($reservation);

        $reservation->update(['status' => 'rejetee']);

        return response()->json([
            'success' => true,
            'message' => 'Réservation rejetée.',
            'data' => $reservation->load(['user', 'salle', 'equipements']),
        ]);
    }

    private function verifierStatutModifiable(Reservation $reservation): void
    {
        if ($reservation->status !== 'en_attente') {
            throw ValidationException::withMessages([
                'status' => ["Cette réservation a déjà le statut '{$reservation->status}', elle ne peut plus être modifiée."],
            ]);
        }
    }
}
