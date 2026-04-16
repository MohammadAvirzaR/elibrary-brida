# 🧪 Quick Test: Statistik Penelitian

## Langkah-Langkah Testing

### Step 1: Verify Route
```bash
# Check di routes/api.php
# Pastikan ada line:
# Route::get('/statistics', [StatisticsController::class, 'index']);
```

### Step 2: Test via Terminal
```bash
cd c:\laragon\www\prjct-elibrary-brida\elibrary-brida\elibrary-brida-be

# 1. Start Laravel server
php artisan serve

# 2. Di terminal lain, test API dengan token
# Ganti YOUR_TOKEN dengan token dari login admin
curl -X GET http://localhost:8000/api/statistics \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# Expected response:
# {
#   "success": true,
#   "data": {
#     "total_research": <number>,
#     "brida_research": <number>,
#     "non_brida_research": <number>,
#     "category_data": { "labels": [...], "values": [...] },
#     "trend_data": { "series": [...], "months": [...] },
#     "institution_data": { "institutions": [...], "counts": [...] }
#   }
# }
```

### Step 3: Test via Browser
1. Login ke Admin Dashboard
2. Buka Developer Tools (F12)
3. Go to Console tab
4. Refresh halaman
5. Lihat logs:
   - `📊 Loading statistics from API...`
   - `✅ API Response received: {...}`
   - `✨ Statistics loaded successfully!`
6. Verify charts display data

### Step 4: Check Database

```sql
-- Check di database
-- Pastikan ada dokumen approved

USE elibrary_brida;

-- Check total approved documents
SELECT COUNT(*) as total FROM documents WHERE status = 'approved';

-- Check approved by type
SELECT type_id, COUNT(*) as count FROM documents WHERE status = 'approved' GROUP BY type_id;

-- Check approved by institution
SELECT institution, COUNT(*) as count FROM documents WHERE status = 'approved' GROUP BY institution ORDER BY count DESC LIMIT 10;

-- Check approved by year/month
SELECT YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count 
FROM documents 
WHERE status = 'approved' 
GROUP BY YEAR(created_at), MONTH(created_at)
ORDER BY year DESC, month DESC;
```

### Step 5: Verify Type ID for BRIDA

```sql
-- Check apa type_id untuk BRIDA
SELECT id, name FROM types WHERE LOWER(name) LIKE '%brida%';

-- If hasil tidak ada atau ID beda, update StatisticsController
-- Example jika BRIDA adalah type_id = 5:
-- WHERE type_id = 5  (instead of 1)
```

---

## Expected Output

### API Response Sample
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
        {"name": "2024", "data": [12, 18, 25, 30, 45, 55, 60, 72, 80, 85, 90, 95]},
        {"name": "2025", "data": [8, 15, 22, 35, 50, 65, 75, 88, 95, 100, 105, 110]},
        {"name": "2026", "data": [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60]}
      ],
      "months": ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
    },
    "institution_data": {
      "institutions": ["UNIV A", "UNIV B", "UNIV C", ...],
      "counts": [1200, 800, 600, ...]
    }
  }
}
```

### Dashboard Display
- ✅ "Jumlah Keseluruhan Penelitian" card shows 1640
- ✅ "Jumlah Penelitian BRIDA" card shows 5
- ✅ "Jumlah Penelitian Non-BRIDA" card shows 1635
- ✅ Pie chart shows kategori breakdown
- ✅ Line chart shows tren per bulan
- ✅ Bar chart shows institusi

---

## Potential Issues & Solutions

| Issue | Solution |
|-------|----------|
| API returns 401 | Check token valid & user is admin/super_admin |
| All charts empty | Check database has approved documents |
| Numbers are 0 | Check database filtering (status='approved') |
| Type ID mismatch | Verify BRIDA type_id in database, update controller |
| 404 Not Found | Check route registered in routes/api.php |
| CORS Error | Check cors.php config allows frontend domain |

---

## Console Logs to Look For

✅ Good signs:
```
📊 Loading statistics from API...
✅ API Response received: {data}
📊 Pie Chart updated: {labels, values}
📈 Line Chart updated with X years
📊 Bar Chart updated with Y institutions
✨ Statistics loaded successfully!
```

❌ Bad signs:
```
❌ Gagal memuat statistik: Error...
⚠️ API response not successful
Network Error / 401 / 403 / 404
```

---

## Verification Checklist

- [ ] Route exists in api.php
- [ ] StatisticsController.php exists
- [ ] Database has approved documents
- [ ] Type ID for BRIDA is correct
- [ ] Token is valid
- [ ] User role is admin or super_admin
- [ ] API endpoint returns data
- [ ] Frontend charts display data
- [ ] Console logs show no errors
- [ ] Database queries are optimized

---

**Once all tests pass, you're ready for production! 🚀**
