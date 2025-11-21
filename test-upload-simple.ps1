# Simple Upload Test
Write-Host "`n=== Testing Document Upload ===" -ForegroundColor Cyan

# Login as admin
Write-Host "`nLogging in..." -ForegroundColor Yellow
$loginBody = @{
    email = "admin@brida.com"
    password = "admin123"
} | ConvertTo-Json

$loginResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/login" `
    -Method POST `
    -Body $loginBody `
    -ContentType "application/json"

$loginData = $loginResponse.Content | ConvertFrom-Json
$token = $loginData.data.token
Write-Host "✓ Login successful" -ForegroundColor Green

# Create test file
$testFile = "$env:TEMP\test-doc.pdf"
"%PDF-1.4`nTest Document" | Out-File -FilePath $testFile -Encoding ASCII

# Upload using curl
Write-Host "`nUploading document..." -ForegroundColor Yellow

$uploadUrl = "http://127.0.0.1:8000/api/documents/upload"
curl -X POST $uploadUrl `
  -H "Authorization: Bearer $token" `
  -H "Accept: application/json" `
  -F "file=@$testFile" `
  -F "title=Test Document" `
  -F "description=Test upload via API" `
  -F "category=penelitian" `
  -F "year=2024" `
  -F "author=Test Author" `
  -F "publisher=Test Publisher" `
  -F "keywords=test"

Remove-Item $testFile -ErrorAction SilentlyContinue
Write-Host ""
Write-Host "Test Complete" -ForegroundColor Cyan
