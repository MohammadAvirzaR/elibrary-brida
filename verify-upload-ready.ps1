Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "   UPLOAD DOCUMENT FLOW - FINAL CHECK" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

# Check Backend Server
Write-Host "[1/4] Checking Backend Server..." -ForegroundColor Yellow
try {
    $health = Invoke-WebRequest -Uri "http://127.0.0.1:8000" -UseBasicParsing -TimeoutSec 3 -ErrorAction Stop
    Write-Host "  SUCCESS - Backend running on http://127.0.0.1:8000" -ForegroundColor Green
} catch {
    Write-Host "  FAILED - Backend not running!" -ForegroundColor Red
    Write-Host "  Run: cd elibrary-brida-be; php artisan serve" -ForegroundColor Yellow
    exit 1
}

# Check Frontend Server
Write-Host "`n[2/4] Checking Frontend Server..." -ForegroundColor Yellow
try {
    $frontend = Invoke-WebRequest -Uri "http://localhost:5173" -UseBasicParsing -TimeoutSec 3 -ErrorAction Stop
    Write-Host "  SUCCESS - Frontend running on http://localhost:5173" -ForegroundColor Green
} catch {
    Write-Host "  FAILED - Frontend not running!" -ForegroundColor Red
    Write-Host "  Run: cd elibrary-brida-fe; npm run dev" -ForegroundColor Yellow
    exit 1
}

# Check API Authentication
Write-Host "`n[3/4] Testing API Authentication..." -ForegroundColor Yellow
try {
    $loginBody = @{
        email = "admin@brida.com"
        password = "admin123"
    } | ConvertTo-Json
    
    $loginResponse = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/login" `
        -Method POST `
        -Body $loginBody `
        -ContentType "application/json" `
        -ErrorAction Stop
    
    if ($loginResponse.data.token) {
        Write-Host "  SUCCESS - Authentication working" -ForegroundColor Green
        Write-Host "  User: $($loginResponse.data.user.full_name)" -ForegroundColor Cyan
        Write-Host "  Role: $($loginResponse.data.user.role.name)" -ForegroundColor Cyan
        $token = $loginResponse.data.token
    } else {
        Write-Host "  FAILED - No token received" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "  FAILED - Login error: $_" -ForegroundColor Red
    exit 1
}

# Check Current Documents
Write-Host "`n[4/4] Checking Document Database..." -ForegroundColor Yellow
try {
    $docs = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/documents" `
        -Method GET `
        -Headers @{
            "Authorization" = "Bearer $token"
            "Accept" = "application/json"
        } `
        -ErrorAction Stop
    
    $totalDocs = $docs.data.Count
    $pendingDocs = ($docs.data | Where-Object { $_.status -eq 'pending' }).Count
    $approvedDocs = ($docs.data | Where-Object { $_.status -eq 'approved' }).Count
    
    Write-Host "  SUCCESS - Database accessible" -ForegroundColor Green
    Write-Host "  Total documents: $totalDocs" -ForegroundColor Cyan
    Write-Host "  Pending: $pendingDocs" -ForegroundColor Yellow
    Write-Host "  Approved: $approvedDocs" -ForegroundColor Green
    
    if ($totalDocs -gt 0) {
        Write-Host "`n  Latest document:" -ForegroundColor Cyan
        $latest = $docs.data | Sort-Object -Property created_at -Descending | Select-Object -First 1
        Write-Host "    - ID: $($latest.id)" -ForegroundColor Gray
        Write-Host "    - Title: $($latest.title)" -ForegroundColor Gray
        Write-Host "    - Status: $($latest.status)" -ForegroundColor Gray
        Write-Host "    - Date: $($latest.created_at)" -ForegroundColor Gray
    }
} catch {
    Write-Host "  FAILED - Database error: $_" -ForegroundColor Red
    exit 1
}

# Final Summary
Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "   ALL SYSTEMS READY!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan

Write-Host "`nUPLOAD ENDPOINT DETAILS:" -ForegroundColor Yellow
Write-Host "  URL: http://127.0.0.1:8000/api/documents/upload" -ForegroundColor White
Write-Host "  Method: POST" -ForegroundColor White
Write-Host "  Auth: Bearer Token (Sanctum)" -ForegroundColor White
Write-Host "  Content-Type: multipart/form-data" -ForegroundColor White

Write-Host "`nREQUIRED FIELDS:" -ForegroundColor Yellow
Write-Host "  - file (PDF/DOC/DOCX, max 10MB)" -ForegroundColor White
Write-Host "  - title (string, max 255)" -ForegroundColor White
Write-Host "  - description (string)" -ForegroundColor White
Write-Host "  - year (1900-2025)" -ForegroundColor White
Write-Host "  - author (string, max 255)" -ForegroundColor White

Write-Host "`nOPTIONAL FIELDS:" -ForegroundColor Yellow
Write-Host "  - category (string)" -ForegroundColor White
Write-Host "  - publisher (string)" -ForegroundColor White
Write-Host "  - keywords (comma-separated)" -ForegroundColor White

Write-Host "`nTEST CREDENTIALS:" -ForegroundColor Yellow
Write-Host "  Email: admin@brida.com" -ForegroundColor White
Write-Host "  Password: admin123" -ForegroundColor White
Write-Host "  Role: super_admin (can upload & review)" -ForegroundColor White

Write-Host "`nNEXT STEPS:" -ForegroundColor Yellow
Write-Host "  1. Open browser: http://localhost:5173" -ForegroundColor White
Write-Host "  2. Login with admin credentials" -ForegroundColor White
Write-Host "  3. Navigate to dashboard" -ForegroundColor White
Write-Host "  4. Click 'Upload Dokumen' button" -ForegroundColor White
Write-Host "  5. Fill form and upload a PDF file" -ForegroundColor White
Write-Host "  6. Verify success notification" -ForegroundColor White
Write-Host "  7. Check document appears in list" -ForegroundColor White

Write-Host "`nDOCUMENTATION:" -ForegroundColor Yellow
Write-Host "  See: UPLOAD_FLOW_GUIDE.md for detailed info" -ForegroundColor White

Write-Host "`n========================================`n" -ForegroundColor Cyan
