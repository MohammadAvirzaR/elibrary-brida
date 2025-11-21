Write-Host "=== Testing Document Upload API ===" -ForegroundColor Cyan

Write-Host "`n1. Logging in as admin..." -ForegroundColor Yellow
$loginBody = @{
    email = "admin@brida.com"
    password = "admin123"
}

$loginResponse = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/login" `
    -Method POST `
    -Body ($loginBody | ConvertTo-Json) `
    -ContentType "application/json"

$token = $loginResponse.data.token
Write-Host "Success! Token received" -ForegroundColor Green

Write-Host "`n2. Creating test PDF..." -ForegroundColor Yellow
$testFile = Join-Path $env:TEMP "test-upload-$(Get-Date -Format 'yyyyMMdd-HHmmss').pdf"
@"
%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj
3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj
xref
0 4
trailer<</Size 4/Root 1 0 R>>
startxref
180
%%EOF
"@ | Out-File -FilePath $testFile -Encoding ASCII -NoNewline

Write-Host "Test file created: $testFile" -ForegroundColor Green

Write-Host "`n3. Uploading document to API..." -ForegroundColor Yellow

# Read file as bytes
$fileBytes = [System.IO.File]::ReadAllBytes($testFile)
$fileName = [System.IO.Path]::GetFileName($testFile)

# Create multipart form data
$boundary = [System.Guid]::NewGuid().ToString()
$LF = "`r`n"

$bodyLines = @(
    "--$boundary",
    "Content-Disposition: form-data; name=`"file`"; filename=`"$fileName`"",
    "Content-Type: application/pdf",
    "",
    [System.Text.Encoding]::ASCII.GetString($fileBytes),
    "--$boundary",
    "Content-Disposition: form-data; name=`"title`"",
    "",
    "Test Document from API",
    "--$boundary",
    "Content-Disposition: form-data; name=`"description`"",
    "",
    "This document was uploaded via API test to verify the upload endpoint works correctly",
    "--$boundary",
    "Content-Disposition: form-data; name=`"category`"",
    "",
    "penelitian",
    "--$boundary",
    "Content-Disposition: form-data; name=`"year`"",
    "",
    "2024",
    "--$boundary",
    "Content-Disposition: form-data; name=`"author`"",
    "",
    "API Test Author",
    "--$boundary",
    "Content-Disposition: form-data; name=`"publisher`"",
    "",
    "BRIDA Test Publisher",
    "--$boundary",
    "Content-Disposition: form-data; name=`"keywords`"",
    "",
    "test, api, upload, automation",
    "--$boundary--"
)

$body = $bodyLines -join $LF

try {
    $uploadResponse = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/documents/upload" `
        -Method POST `
        -Headers @{
            "Authorization" = "Bearer $token"
            "Accept" = "application/json"
        } `
        -ContentType "multipart/form-data; boundary=$boundary" `
        -Body $body

    Write-Host "SUCCESS! Document uploaded" -ForegroundColor Green
    Write-Host "  ID: $($uploadResponse.data.id)" -ForegroundColor Cyan
    Write-Host "  Title: $($uploadResponse.data.title)" -ForegroundColor Cyan
    Write-Host "  Status: $($uploadResponse.data.status)" -ForegroundColor Cyan
    Write-Host "  Author: $($uploadResponse.data.author)" -ForegroundColor Cyan
    Write-Host "  Created: $($uploadResponse.data.created_at)" -ForegroundColor Cyan
    
    Write-Host "`n4. Verifying document in database..." -ForegroundColor Yellow
    $listResponse = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/documents" `
        -Method GET `
        -Headers @{
            "Authorization" = "Bearer $token"
            "Accept" = "application/json"
        }
    
    $totalDocs = $listResponse.data.Count
    Write-Host "Total documents in database: $totalDocs" -ForegroundColor Cyan
    
    $uploadedDoc = $listResponse.data | Where-Object { $_.id -eq $uploadResponse.data.id }
    if ($uploadedDoc) {
        Write-Host "Document verified in list!" -ForegroundColor Green
    }
    
} catch {
    Write-Host "FAILED! Upload error" -ForegroundColor Red
    Write-Host "Error: $_" -ForegroundColor Red
    
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "Response: $responseBody" -ForegroundColor Yellow
    }
}

Remove-Item $testFile -ErrorAction SilentlyContinue

Write-Host "`n=== Test Complete ===" -ForegroundColor Cyan
Write-Host "API Endpoint: http://127.0.0.1:8000/api/documents/upload" -ForegroundColor Gray
Write-Host "Frontend: http://localhost:5173" -ForegroundColor Gray
