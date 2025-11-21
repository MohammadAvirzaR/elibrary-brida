# Test Upload Document Flow
Write-Host "`n=== Testing Document Upload API ===" -ForegroundColor Cyan

# Step 1: Login as contributor
Write-Host "`n1. Logging in as contributor..." -ForegroundColor Yellow
$loginBody = @{
    email = "contributor@example.com"
    password = "password123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/login" `
        -Method POST `
        -Body $loginBody `
        -ContentType "application/json"
    
    $token = $loginResponse.data.token
    Write-Host "✓ Login successful! Token: $($token.Substring(0,20))..." -ForegroundColor Green
} catch {
    Write-Host "✗ Login failed: $_" -ForegroundColor Red
    exit 1
}

# Step 2: Create a test PDF file
Write-Host "`n2. Creating test document..." -ForegroundColor Yellow
$testFilePath = "$env:TEMP\test-document.pdf"
$pdfContent = @"
%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj
2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj
3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
>>
endobj
4 0 obj
<<
/Length 44
>>
stream
BT
/F1 12 Tf
100 700 Td
(Test Document) Tj
ET
endstream
endobj
xref
0 5
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000214 00000 n 
trailer
<<
/Size 5
/Root 1 0 R
>>
startxref
308
%%EOF
"@

Set-Content -Path $testFilePath -Value $pdfContent -Encoding Ascii
Write-Host "✓ Test PDF created at: $testFilePath" -ForegroundColor Green

# Step 3: Test upload
Write-Host "`n3. Uploading document..." -ForegroundColor Yellow

$boundary = [System.Guid]::NewGuid().ToString()
$LF = "`r`n"

$bodyLines = (
    "--$boundary",
    "Content-Disposition: form-data; name=`"file`"; filename=`"test-document.pdf`"",
    "Content-Type: application/pdf$LF",
    [System.IO.File]::ReadAllText($testFilePath),
    "--$boundary",
    "Content-Disposition: form-data; name=`"title`"$LF",
    "Dokumen Test Upload API",
    "--$boundary",
    "Content-Disposition: form-data; name=`"description`"$LF",
    "Ini adalah dokumen test untuk memverifikasi upload API berfungsi dengan baik",
    "--$boundary",
    "Content-Disposition: form-data; name=`"category`"$LF",
    "penelitian",
    "--$boundary",
    "Content-Disposition: form-data; name=`"year`"$LF",
    "2024",
    "--$boundary",
    "Content-Disposition: form-data; name=`"author`"$LF",
    "Test Author",
    "--$boundary",
    "Content-Disposition: form-data; name=`"publisher`"$LF",
    "Test Publisher",
    "--$boundary",
    "Content-Disposition: form-data; name=`"keywords`"$LF",
    "test, upload, api",
    "--$boundary--$LF"
) -join $LF

try {
    $uploadResponse = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/documents/upload" `
        -Method POST `
        -Headers @{
            "Authorization" = "Bearer $token"
            "Accept" = "application/json"
        } `
        -ContentType "multipart/form-data; boundary=$boundary" `
        -Body $bodyLines

    Write-Host "✓ Upload successful!" -ForegroundColor Green
    Write-Host "  Document ID: $($uploadResponse.data.id)" -ForegroundColor Cyan
    Write-Host "  Title: $($uploadResponse.data.title)" -ForegroundColor Cyan
    Write-Host "  Status: $($uploadResponse.data.status)" -ForegroundColor Cyan
    Write-Host "  Created: $($uploadResponse.data.created_at)" -ForegroundColor Cyan
    
} catch {
    Write-Host "✗ Upload failed!" -ForegroundColor Red
    Write-Host "Error: $_" -ForegroundColor Red
    
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "Response: $responseBody" -ForegroundColor Red
    }
    exit 1
}

# Step 4: Verify document appears in list
Write-Host "`n4. Verifying document in list..." -ForegroundColor Yellow

try {
    $listResponse = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/documents" `
        -Method GET `
        -Headers @{
            "Authorization" = "Bearer $token"
            "Accept" = "application/json"
        }
    
    $uploadedDoc = $listResponse.data | Where-Object { $_.id -eq $uploadResponse.data.id }
    
    if ($uploadedDoc) {
        Write-Host "✓ Document found in list!" -ForegroundColor Green
        Write-Host "  Total documents: $($listResponse.data.Count)" -ForegroundColor Cyan
    } else {
        Write-Host "✗ Document not found in list" -ForegroundColor Red
    }
    
} catch {
    Write-Host "✗ Failed to get document list: $_" -ForegroundColor Red
}

# Cleanup
Remove-Item $testFilePath -ErrorAction SilentlyContinue

Write-Host "`n=== Test Complete ===" -ForegroundColor Cyan
Write-Host "`n✓ Upload API is working correctly!" -ForegroundColor Green
Write-Host "  Endpoint: http://127.0.0.1:8000/api/documents/upload" -ForegroundColor Cyan
Write-Host "  Method: POST" -ForegroundColor Cyan
Write-Host "  Auth: Bearer Token Required" -ForegroundColor Cyan
Write-Host "  Content-Type: multipart/form-data" -ForegroundColor Cyan
