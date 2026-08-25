<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Salle;
use Illuminate\Http\JsonResponse;

class SalleController extends Controller
{
    public function index(): JsonResponse
    {
        $salles = Salle::with('images')
            ->where('status', 'disponible')
            ->get();

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
}
