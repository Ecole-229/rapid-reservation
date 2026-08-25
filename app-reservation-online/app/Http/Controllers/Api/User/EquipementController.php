<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Equipement;
use Illuminate\Http\JsonResponse;

class EquipementController extends Controller
{
    public function index(): JsonResponse
    {
        $equipements = Equipement::where('status', 'disponible')->get();

        return response()->json([
            'success' => true,
            'data' => $equipements,
        ]);
    }

    public function show(Equipement $equipement): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $equipement,
        ]);
    }
}
