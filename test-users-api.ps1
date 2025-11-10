# Quick API Testing - Login as Super Admin
$API_BASE = "http://127.0.0.1:8000/api"

Write-Host "🧪 Testing Login as Super Admin" -ForegroundColor Green

$LoginData = @{
    email = "admin@brida.com"
    password = "admin123"
} | ConvertTo-Json

try {
    $LoginResponse = Invoke-WebRequest -Uri "$API_BASE/login" `
        -Method POST `
        -Headers @{
            "Content-Type" = "application/json"
            "Accept" = "application/json"
        } `
        -Body $LoginData `
        -ErrorAction Stop

    $LoginJson = $LoginResponse.Content | ConvertFrom-Json
    Write-Host "Login Response:" -ForegroundColor Green
    Write-Host ($LoginJson | ConvertTo-Json -Depth 3) -ForegroundColor White
    Write-Host ""

    $TOKEN = $LoginJson.token
    $USER_ROLE = $LoginJson.user.role

    if ($TOKEN) {
        Write-Host "✅ Login successful!" -ForegroundColor Green
        Write-Host "Token: $TOKEN" -ForegroundColor Cyan
        Write-Host "User Role: $USER_ROLE" -ForegroundColor Cyan
        Write-Host ""

        # Save token to file for later use
        $TOKEN | Out-File -FilePath "admin_token.txt"

        # Now test get users endpoint
        Write-Host "2️⃣ Testing Get Users with Super Admin Token" -ForegroundColor Yellow
        Write-Host "Request: GET $API_BASE/users" -ForegroundColor Cyan
        Write-Host ""

        try {
            $UsersResponse = Invoke-WebRequest -Uri "$API_BASE/users" `
                -Method GET `
                -Headers @{
                    "Authorization" = "Bearer $TOKEN"
                    "Accept" = "application/json"
                } `
                -ErrorAction Stop

            $UsersJson = $UsersResponse.Content | ConvertFrom-Json
            Write-Host "Response Status:" -ForegroundColor Green
            Write-Host $UsersResponse.StatusCode -ForegroundColor White
            Write-Host ""
            Write-Host "Response Data:" -ForegroundColor Green
            Write-Host ($UsersJson | ConvertTo-Json -Depth 10) -ForegroundColor White
            
        } catch {
            Write-Host "❌ Error getting users: $($_.Exception.Message)" -ForegroundColor Red
            Write-Host "Status Code: $($_.Exception.Response.StatusCode)" -ForegroundColor Red
            
            # Try to get error details
            try {
                $errorResponse = $_.Exception.Response.Content.ToString() | ConvertFrom-Json
                Write-Host "Error Details:" -ForegroundColor Red
                Write-Host ($errorResponse | ConvertTo-Json) -ForegroundColor Cyan
            } catch {}
        }
    } else {
        Write-Host "❌ Login failed!" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Error during login: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "==================================" -ForegroundColor Green
