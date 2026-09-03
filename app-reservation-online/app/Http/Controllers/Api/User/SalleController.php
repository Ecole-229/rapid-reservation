<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Salle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Salle::with('images')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $salles = $query->get();

        return response()->json([
            'success' => true,
            'data' => $salles,
        ]);
    }

    public function show(Salle $salle): JsonResponse
    {
        $salle->load('images');

        return response()->json([
            'success' => true,
            'data' => $salle,
        ]);
    }

    public function disponibilites(
        Salle $salle,
        Request $request
    ): JsonResponse {
        $validated = $request->validate([
            'debut' => ['required', 'date'],
            'fin' => ['required', 'date', 'after:debut'],
        ]);

        $debut = new \DateTime($validated['debut']);
        $fin = new \DateTime($validated['fin']);

        return response()->json([
            'success' => true,
            'data' => [
                'salle_id' => $salle->id,
                'disponible' => $salle->estDisponible($debut, $fin),
                'debut' => $debut->format('Y-m-d H:i:s'),
                'fin' => $fin->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}
