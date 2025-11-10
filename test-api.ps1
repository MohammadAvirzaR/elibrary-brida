# Quick API Testing Script for Windows
# This script tests the registration and user endpoints

$API_BASE = "http://127.0.0.1:8000/api"
$TIMESTAMP = Get-Date -Format "yyyyMMddHHmmss"
$EMAIL = "testuser$TIMESTAMP@example.com"
$PASSWORD = "password123"

Write-Host "🧪 E-Library BRIDA - API Testing" -ForegroundColor Green
Write-Host "==================================" -ForegroundColor Green
Write-Host ""

# Test 1: Register new user
Write-Host "1️⃣ Testing Registration Endpoint" -ForegroundColor Yellow
Write-Host "Request: POST $API_BASE/register" -ForegroundColor Cyan
Write-Host ""

$RegisterData = @{
    name = "Test User $TIMESTAMP"
    email = $EMAIL
    institution = "Test Institution"
    password = $PASSWORD
    password_confirmation = $PASSWORD
} | ConvertTo-Json

try {
    $RegisterResponse = Invoke-WebRequest -Uri "$API_BASE/register" `
        -Method POST `
        -Headers @{
            "Content-Type" = "application/json"
            "Accept" = "application/json"
        } `
        -Body $RegisterData `
        -ErrorAction Stop

    $RegisterJson = $RegisterResponse.Content | ConvertFrom-Json
    Write-Host "Response:" -ForegroundColor Green
    Write-Host ($RegisterJson | ConvertTo-Json -Depth 3) -ForegroundColor White
    Write-Host ""

    $TOKEN = $RegisterJson.token

    if ($TOKEN) {
        Write-Host "✅ Registration successful!" -ForegroundColor Green
        Write-Host "Token: $TOKEN" -ForegroundColor Cyan
        Write-Host "User Email: $EMAIL" -ForegroundColor Cyan
        Write-Host ""

        # Test 2: Get all users (requires authentication)
        Write-Host "2️⃣ Testing Get Users Endpoint" -ForegroundColor Yellow
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
            Write-Host "Response:" -ForegroundColor Green
            Write-Host ($UsersJson | ConvertTo-Json -Depth 3) -ForegroundColor White
            Write-Host ""

            # Check if new user is in the list
            $NewUserFound = $UsersJson.data | Where-Object { $_.email -eq $EMAIL }

            if ($NewUserFound) {
                Write-Host "✅ New user found in users list!" -ForegroundColor Green
                Write-Host ($NewUserFound | ConvertTo-Json) -ForegroundColor Cyan
            } else {
                Write-Host "❌ New user NOT found in users list" -ForegroundColor Red
            }
        } catch {
            Write-Host "❌ Error getting users: $($_.Exception.Message)" -ForegroundColor Red
        }
    } else {
        Write-Host "❌ Registration failed!" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Error during registration: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "==================================" -ForegroundColor Green
Write-Host "Testing Complete!" -ForegroundColor Green
