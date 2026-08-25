<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipement;
use Illuminate\Http\JsonResponse;

class EquipementController extends Controller
{
    public function index(): JsonResponse
    {
        $equipements = Equipement::where('status', 'disponible')->get();

        return response()->json($equipements);
    }
}
