Write-Host ""
Write-Host "=== UPLOAD FLOW STATUS CHECK ===" -ForegroundColor Cyan
Write-Host ""

Write-Host "Testing API..." -ForegroundColor Yellow
$loginBody = '{"email":"admin@brida.com","password":"admin123"}'
try {
    $response = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/login" -Method POST -Body $loginBody -ContentType "application/json"
    Write-Host "SUCCESS - API is working!" -ForegroundColor Green
    Write-Host "  User: $($response.user.name)" -ForegroundColor Cyan
    Write-Host "  Email: $($response.user.email)" -ForegroundColor Cyan
    
    $token = $response.data.token
    
    Write-Host ""
    Write-Host "Getting documents..." -ForegroundColor Yellow
    $docs = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/documents" -Headers @{"Authorization"="Bearer $token";"Accept"="application/json"}
    $count = $docs.data.Count
    Write-Host "Total documents: $count" -ForegroundColor Cyan
    
    Write-Host ""
    Write-Host "=== READY FOR TESTING ===" -ForegroundColor Green
    Write-Host ""
    Write-Host "Upload Endpoint: http://127.0.0.1:8000/api/documents/upload" -ForegroundColor White
    Write-Host "Frontend URL: http://localhost:5173" -ForegroundColor White
    Write-Host ""
    Write-Host "Test Steps:" -ForegroundColor Yellow
    Write-Host "  1. Open http://localhost:5173 in browser" -ForegroundColor White
    Write-Host "  2. Login: admin@brida.com / admin123" -ForegroundColor White
    Write-Host "  3. Click 'Upload Dokumen'" -ForegroundColor White
    Write-Host "  4. Upload a PDF file with details" -ForegroundColor White
    Write-Host "  5. Verify success notification" -ForegroundColor White
    Write-Host ""
    
} catch {
    Write-Host "ERROR - API not responding: $_" -ForegroundColor Red
}
