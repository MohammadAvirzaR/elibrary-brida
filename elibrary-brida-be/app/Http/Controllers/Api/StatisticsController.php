<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatisticsController extends Controller
{
    /**
     * Get comprehensive dashboard statistics for super_admin
     *
     * Includes:
     * - Total research count (all, BRIDA, Non-BRIDA)
     * - Category breakdown (Pie chart data)
     * - Trend data (Line chart data - monthly trends)
     * - Institution breakdown (Bar chart data)
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            Log::info('📊 Statistics API called');

            // 1. TOTAL RESEARCH COUNTS
            $totalResearch = Document::where('status', 'approved')->count();

            // Assuming type_id = 1 is BRIDA (adjust if different)
            $bridaResearch = Document::where('status', 'approved')
                ->where('type_id', 1)
                ->count();

            $nonBridaResearch = $totalResearch - $bridaResearch;

            Log::info('Research counts', [
                'total' => $totalResearch,
                'brida' => $bridaResearch,
                'non_brida' => $nonBridaResearch
            ]);

            // 2. CATEGORY DATA (Pie Chart)
            $categoryData = Document::select('type_id', DB::raw('count(*) as total'))
                ->where('status', 'approved')
                ->groupBy('type_id')
                ->with('type:id,type_name')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => $item->type?->type_name ?? 'Unknown',
                        'value' => $item->total
                    ];
                });

            Log::info('Category data collected', ['count' => $categoryData->count()]);

            // 3. TREND DATA (Line Chart - per month and year)
            $trendData = Document::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('count(*) as total')
                )
                ->where('status', 'approved')
                ->whereYear('created_at', '>=', now()->subYears(2)->year)
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get()
                ->groupBy('year')
                ->map(function ($yearData, $year) {
                    $monthlyData = array_fill(0, 12, 0);
                    foreach ($yearData as $item) {
                        $monthlyData[$item->month - 1] = $item->total;
                    }
                    return [
                        'name' => 'Tahun ' . $year,
                        'data' => $monthlyData
                    ];
                })
                ->values();

            Log::info('Trend data collected', ['years' => $trendData->count()]);

            // 4. INSTITUTION DATA (Bar Chart - Top 10)
            // Get institutions from documents.university_id (primary source)
            $institutionData = DB::table('documents')
                ->leftJoin('university', 'documents.university_id', '=', 'university.id')
                ->where('documents.status', 'approved')
                ->selectRaw('COALESCE(university.university_name, "Tanpa Instansi") as institution_name, COUNT(documents.id) as total')
                ->whereNotNull('documents.university_id')
                ->groupBy('institution_name')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get();

            // If no data from documents, fallback to document_authors
            if ($institutionData->isEmpty()) {
                $institutionData = DB::table('document_authors')
                    ->leftJoin('university', 'document_authors.university_id', '=', 'university.id')
                    ->join('documents', 'document_authors.document_id', '=', 'documents.id')
                    ->where('documents.status', 'approved')
                    ->selectRaw('COALESCE(university.university_name, document_authors.institution) as institution_name, COUNT(DISTINCT document_authors.document_id) as total')
                    ->whereRaw('COALESCE(university.university_name, document_authors.institution) IS NOT NULL AND COALESCE(university.university_name, document_authors.institution) != ""')
                    ->groupBy('institution_name')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get();
            }

            // If still no data, try document_supervisors
            if ($institutionData->isEmpty()) {
                $institutionData = DB::table('document_supervisors')
                    ->leftJoin('university', 'document_supervisors.university_id', '=', 'university.id')
                    ->join('documents', 'document_supervisors.document_id', '=', 'documents.id')
                    ->where('documents.status', 'approved')
                    ->selectRaw('COALESCE(university.university_name, document_supervisors.institution) as institution_name, COUNT(DISTINCT document_supervisors.document_id) as total')
                    ->whereRaw('COALESCE(university.university_name, document_supervisors.institution) IS NOT NULL AND COALESCE(university.university_name, document_supervisors.institution) != ""')
                    ->groupBy('institution_name')
                    ->orderBy('total', 'desc')
                    ->limit(10)
                    ->get();
            }

            $institutionData = $institutionData->map(function ($item) {
                return [
                    'institution' => $item->institution_name,
                    'count' => $item->total
                ];
            });

            Log::info('Institution data collected', ['count' => $institutionData->count()]);

            return response()->json([
                'success' => true,
                'data' => [
                    'total_research' => $totalResearch,
                    'brida_research' => $bridaResearch,
                    'non_brida_research' => $nonBridaResearch,
                    'category_data' => [
                        'labels' => $categoryData->pluck('label')->toArray(),
                        'values' => $categoryData->pluck('value')->toArray(),
                    ],
                    'trend_data' => [
                        'series' => $trendData,
                        'months' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                    ],
                    'institution_data' => [
                        'institutions' => $institutionData->pluck('institution')->toArray(),
                        'counts' => $institutionData->pluck('count')->toArray()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error in statistics endpoint: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
