# Statistics API Implementation Guide

## Overview
Endpoint untuk menampilkan statistik dashboard admin dengan data:
- Total penelitian (keseluruhan)
- Penelitian BRIDA
- Penelitian Non-BRIDA
- Data untuk 3 chart (pie, line, bar)

## Backend Implementation Steps

### 1. Create Controller
**File**: `app/Http/Controllers/Api/StatisticsController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Get dashboard statistics
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Total Research Counts
            $totalResearch = Document::where('status', 'approved')->count();
            $bridaResearch = Document::where('status', 'approved')
                ->where('type_id', 1) // Assuming type_id = 1 is BRIDA
                ->count();
            $nonBridaResearch = $totalResearch - $bridaResearch;

            // Category Data for Pie Chart
            $categoryData = Document::select('type_id', DB::raw('count(*) as total'))
                ->where('status', 'approved')
                ->groupBy('type_id')
                ->with('type:id,name')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => $item->type->name ?? 'Unknown',
                        'value' => $item->total
                    ];
                });

            // Trend Data for Line Chart (per month and year)
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

            // Institution Data for Bar Chart
            $institutionData = Document::select(
                    'institution',
                    DB::raw('count(*) as total')
                )
                ->where('status', 'approved')
                ->whereNotNull('institution')
                ->groupBy('institution')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'institution' => $item->institution,
                        'count' => $item->total
                    ];
                });

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
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik'
            ], 500);
        }
    }
}
```

### 2. Add Route
**File**: `routes/api.php`

Add this route in the authenticated section:

```php
// Statistics (Admin/Super Admin only)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/statistics', [StatisticsController::class, 'index']);
});
```

### 3. Database Requirements

Make sure you have these columns in `documents` table:
- `status` (enum: pending, approved, rejected)
- `type_id` (foreign key to types table)
- `institution` (string, nullable)
- `created_at` (timestamp)

### 4. Optimize with Caching (Optional)

Add caching to improve performance:

```php
use Illuminate\Support\Facades\Cache;

public function index(): JsonResponse
{
    $statistics = Cache::remember('dashboard_statistics', 300, function () {
        // ... your statistics query here
        return [
            'total_research' => $totalResearch,
            // ... rest of data
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $statistics
    ]);
}
```

Cache will expire after 5 minutes (300 seconds).

### 5. Clear Cache on Document Changes

Add cache clearing to DocumentController when documents are created/updated/deleted:

```php
use Illuminate\Support\Facades\Cache;

// In store(), update(), destroy() methods:
Cache::forget('dashboard_statistics');
```

## API Response Structure

```json
{
  "success": true,
  "data": {
    "total_research": 1640,
    "brida_research": 5,
    "non_brida_research": 1635,
    "category_data": {
      "labels": ["BRIDA", "D1", "D2", "S1", "S2"],
      "values": [5, 400, 380, 450, 405]
    },
    "trend_data": {
      "series": [
        {
          "name": "Tahun 2024",
          "data": [12, 18, 25, 30, 45, 55, 60, 72, 80, 85, 90, 95]
        },
        {
          "name": "Tahun 2025",
          "data": [8, 15, 22, 35, 50, 65, 75, 88, 95, 100, 105, 110]
        }
      ],
      "months": ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
    },
    "institution_data": {
      "institutions": [
        "UNIVERSITAS HALU OLEO",
        "STIKES KARYA KESEHATAN KENDARI",
        "UNIVERSITAS SULAWESI TENGGARA"
      ],
      "counts": [1200, 800, 600]
    }
  }
}
```

## Frontend Integration

Frontend sudah siap dengan mock data. Setelah backend API ready:

1. Uncomment code di `DashboardView.vue` method `loadStatistics()`
2. Comment/hapus mock data
3. Test endpoint dengan Postman terlebih dahulu

**File**: `elibrary-brida-fe/src/pages/dashboard/DashboardView.vue`

```typescript
// Uncomment this:
const response = await api.statistics.getAll()
if (response.success && response.data) {
  totalResearch.value = response.data.total_research
  bridaResearch.value = response.data.brida_research
  nonBridaResearch.value = response.data.non_brida_research
  categoryChartSeries.value = response.data.category_data.values
  trendChartSeries.value = response.data.trend_data.series
  institutionChartSeries.value = [{
    name: 'Jumlah Penelitian',
    data: response.data.institution_data.counts
  }]
  institutionChartOptions.value.xaxis.categories = response.data.institution_data.institutions
}
```

## Testing

1. Test with Postman:
   ```
   GET http://127.0.0.1:8000/api/statistics
   Headers: 
     Authorization: Bearer {your_token}
   ```

2. Check browser console for errors
3. Verify charts render correctly

## Notes

- Adjust `type_id` logic based on your actual database structure
- Add authorization middleware if needed (only admin/super_admin can access)
- Consider adding date range filters for more flexible statistics
