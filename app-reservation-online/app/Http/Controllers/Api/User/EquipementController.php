<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Equipement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EquipementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Equipement::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $equipements = $query->get();

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
