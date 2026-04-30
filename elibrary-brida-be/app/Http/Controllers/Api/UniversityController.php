<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\JsonResponse;

class UniversityController extends Controller
{
    /**
     * Get all universities
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $universities = University::orderBy('university_name', 'ASC')->get();

            return response()->json([
                'success' => true,
                'data' => $universities->map(function ($uni) {
                    return [
                        'id' => $uni->id,
                        'name' => $uni->university_name,
                    ];
                })->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar universitas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
