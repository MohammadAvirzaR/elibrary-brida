# E-Library BRIDA - Comprehensive Testing Script
# Date: November 10, 2025

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  E-LIBRARY BRIDA - FULL SYSTEM TEST" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$API_BASE = "http://127.0.0.1:8000/api"
$TEST_RESULTS = @()

# Function to add test result
function Add-TestResult {
    param(
        [string]$TestName,
        [bool]$Passed,
        [string]$Message
    )
    
    $TEST_RESULTS += @{
        Test = $TestName
        Passed = $Passed
        Message = $Message
    }
    
    if ($Passed) {
        Write-Host "✅ $TestName" -ForegroundColor Green
    } else {
        Write-Host "❌ $TestName" -ForegroundColor Red
        Write-Host "   Error: $Message" -ForegroundColor Yellow
    }
}

Write-Host "🧪 Starting Comprehensive System Tests..." -ForegroundColor Yellow
Write-Host ""

# ==========================================
# TEST 1: BACKEND AVAILABILITY
# ==========================================
Write-Host "1️⃣ Testing Backend Availability..." -ForegroundColor Cyan

try {
    $response = Invoke-WebRequest -Uri "$API_BASE/filters" -Method GET -ErrorAction Stop
    if ($response.StatusCode -eq 200) {
        Add-TestResult -TestName "Backend API Accessible" -Passed $true -Message "API responding correctly"
    }
} catch {
    Add-TestResult -TestName "Backend API Accessible" -Passed $false -Message $_.Exception.Message
    Write-Host "⚠️ Backend is not running. Please start the backend server first." -ForegroundColor Red
    exit 1
}

Write-Host ""

# ==========================================
# TEST 2: REGISTER NEW USER
# ==========================================
Write-Host "2️⃣ Testing User Registration..." -ForegroundColor Cyan

$timestamp = Get-Date -Format "yyyyMMddHHmmss"
$testEmail = "test_$timestamp@example.com"
$testName = "Test User $timestamp"

$registerData = @{
    name = $testName
    email = $testEmail
    institution = "Test Institution"
    password = "password123"
    password_confirmation = "password123"
} | ConvertTo-Json

try {
    $registerResponse = Invoke-WebRequest -Uri "$API_BASE/register" `
        -Method POST `
        -Headers @{
            "Content-Type" = "application/json"
            "Accept" = "application/json"
        } `
        -Body $registerData `
        -ErrorAction Stop

    $registerJson = $registerResponse.Content | ConvertFrom-Json
    
    if ($registerResponse.StatusCode -eq 200 -and $registerJson.token) {
        Add-TestResult -TestName "User Registration" -Passed $true -Message "User created successfully"
        $GUEST_TOKEN = $registerJson.token
        $GUEST_USER_ID = $registerJson.user.id
        Write-Host "   User ID: $GUEST_USER_ID" -ForegroundColor Gray
        Write-Host "   Email: $testEmail" -ForegroundColor Gray
        Write-Host "   Role: $($registerJson.user.role)" -ForegroundColor Gray
    }
} catch {
    Add-TestResult -TestName "User Registration" -Passed $false -Message $_.Exception.Message
}

Write-Host ""

# ==========================================
# TEST 3: LOGIN AS SUPER ADMIN
# ==========================================
Write-Host "3️⃣ Testing Super Admin Login..." -ForegroundColor Cyan

$loginData = @{
    email = "admin@brida.com"
    password = "admin123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-WebRequest -Uri "$API_BASE/login" `
        -Method POST `
        -Headers @{
            "Content-Type" = "application/json"
            "Accept" = "application/json"
        } `
        -Body $loginData `
        -ErrorAction Stop

    $loginJson = $loginResponse.Content | ConvertFrom-Json
    
    if ($loginResponse.StatusCode -eq 200 -and $loginJson.token) {
        Add-TestResult -TestName "Super Admin Login" -Passed $true -Message "Login successful"
        $ADMIN_TOKEN = $loginJson.token
        Write-Host "   User: $($loginJson.user.name)" -ForegroundColor Gray
        Write-Host "   Email: $($loginJson.user.email)" -ForegroundColor Gray
        Write-Host "   Role: $($loginJson.user.role)" -ForegroundColor Gray
    }
} catch {
    Add-TestResult -TestName "Super Admin Login" -Passed $false -Message $_.Exception.Message
}

Write-Host ""

# ==========================================
# TEST 4: GET USER INFO (/me)
# ==========================================
Write-Host "4️⃣ Testing Get Current User..." -ForegroundColor Cyan

try {
    $meResponse = Invoke-WebRequest -Uri "$API_BASE/me" `
        -Method GET `
        -Headers @{
            "Authorization" = "Bearer $ADMIN_TOKEN"
            "Accept" = "application/json"
        } `
        -ErrorAction Stop

    if ($meResponse.StatusCode -eq 200) {
        Add-TestResult -TestName "Get Current User (/me)" -Passed $true -Message "User info retrieved"
    }
} catch {
    Add-TestResult -TestName "Get Current User (/me)" -Passed $false -Message $_.Exception.Message
}

Write-Host ""

# ==========================================
# TEST 5: GET ALL USERS (Super Admin Only)
# ==========================================
Write-Host "5️⃣ Testing Get All Users..." -ForegroundColor Cyan

try {
    $usersResponse = Invoke-WebRequest -Uri "$API_BASE/users" `
        -Method GET `
        -Headers @{
            "Authorization" = "Bearer $ADMIN_TOKEN"
            "Accept" = "application/json"
        } `
        -ErrorAction Stop

    $usersJson = $usersResponse.Content | ConvertFrom-Json
    
    if ($usersResponse.StatusCode -eq 200) {
        $userCount = $usersJson.users.Count
        Add-TestResult -TestName "Get All Users" -Passed $true -Message "Retrieved $userCount users"
        
        # Check if newly registered user exists
        $newUser = $usersJson.users | Where-Object { $_.email -eq $testEmail }
        if ($newUser) {
            Add-TestResult -TestName "New User Appears in List" -Passed $true -Message "User $testEmail found in database"
        } else {
            Add-TestResult -TestName "New User Appears in List" -Passed $false -Message "User not found in database"
        }
    }
} catch {
    Add-TestResult -TestName "Get All Users" -Passed $false -Message $_.Exception.Message
}

Write-Host ""

# ==========================================
# TEST 6: GET ALL ROLES (Super Admin Only)
# ==========================================
Write-Host "6️⃣ Testing Get All Roles..." -ForegroundColor Cyan

try {
    $rolesResponse = Invoke-WebRequest -Uri "$API_BASE/roles" `
        -Method GET `
        -Headers @{
            "Authorization" = "Bearer $ADMIN_TOKEN"
            "Accept" = "application/json"
        } `
        -ErrorAction Stop

    $rolesJson = $rolesResponse.Content | ConvertFrom-Json
    
    if ($rolesResponse.StatusCode -eq 200) {
        $roleCount = $rolesJson.roles.Count
        Add-TestResult -TestName "Get All Roles" -Passed $true -Message "Retrieved $roleCount roles"
        
        Write-Host "   Available Roles:" -ForegroundColor Gray
        $rolesJson.roles | ForEach-Object {
            Write-Host "   - $($_.name) (ID: $($_.id))" -ForegroundColor Gray
        }
    }
} catch {
    Add-TestResult -TestName "Get All Roles" -Passed $false -Message $_.Exception.Message
}

Write-Host ""

# ==========================================
# TEST 7: ROLE-BASED ACCESS CONTROL
# ==========================================
Write-Host "7️⃣ Testing Role-Based Access Control..." -ForegroundColor Cyan

# Try to access /users with guest token (should fail)
try {
    $guestAccessResponse = Invoke-WebRequest -Uri "$API_BASE/users" `
        -Method GET `
        -Headers @{
            "Authorization" = "Bearer $GUEST_TOKEN"
            "Accept" = "application/json"
        } `
        -ErrorAction Stop

    # Should not reach here
    Add-TestResult -TestName "RBAC - Guest Denied Access" -Passed $false -Message "Guest was able to access super_admin endpoint"
} catch {
    # Expected to fail with 403
    if ($_.Exception.Response.StatusCode -eq 403) {
        Add-TestResult -TestName "RBAC - Guest Denied Access" -Passed $true -Message "Guest correctly denied access"
    } else {
        Add-TestResult -TestName "RBAC - Guest Denied Access" -Passed $false -Message "Unexpected error: $($_.Exception.Message)"
    }
}

Write-Host ""

# ==========================================
# TEST 8: UPDATE USER ROLE
# ==========================================
Write-Host "8️⃣ Testing Update User Role..." -ForegroundColor Cyan

if ($GUEST_USER_ID) {
    $updateData = @{
        role_id = 3  # Change to contributor
    } | ConvertTo-Json

    try {
        $updateResponse = Invoke-WebRequest -Uri "$API_BASE/users/$GUEST_USER_ID" `
            -Method PUT `
            -Headers @{
                "Authorization" = "Bearer $ADMIN_TOKEN"
                "Content-Type" = "application/json"
                "Accept" = "application/json"
            } `
            -Body $updateData `
            -ErrorAction Stop

        if ($updateResponse.StatusCode -eq 200) {
            Add-TestResult -TestName "Update User Role" -Passed $true -Message "Role updated successfully"
        }
    } catch {
        Add-TestResult -TestName "Update User Role" -Passed $false -Message $_.Exception.Message
    }
}

Write-Host ""

# ==========================================
# TEST 9: DELETE TEST USER
# ==========================================
Write-Host "9️⃣ Testing Delete User..." -ForegroundColor Cyan

if ($GUEST_USER_ID) {
    try {
        $deleteResponse = Invoke-WebRequest -Uri "$API_BASE/users/$GUEST_USER_ID" `
            -Method DELETE `
            -Headers @{
                "Authorization" = "Bearer $ADMIN_TOKEN"
                "Accept" = "application/json"
            } `
            -ErrorAction Stop

        if ($deleteResponse.StatusCode -eq 200) {
            Add-TestResult -TestName "Delete User" -Passed $true -Message "User deleted successfully"
        }
    } catch {
        Add-TestResult -TestName "Delete User" -Passed $false -Message $_.Exception.Message
    }
}

Write-Host ""

# ==========================================
# TEST 10: LOGOUT
# ==========================================
Write-Host "🔟 Testing Logout..." -ForegroundColor Cyan

try {
    $logoutResponse = Invoke-WebRequest -Uri "$API_BASE/logout" `
        -Method POST `
        -Headers @{
            "Authorization" = "Bearer $ADMIN_TOKEN"
            "Accept" = "application/json"
        } `
        -ErrorAction Stop

    if ($logoutResponse.StatusCode -eq 200) {
        Add-TestResult -TestName "Logout" -Passed $true -Message "Logout successful"
    }
} catch {
    Add-TestResult -TestName "Logout" -Passed $false -Message $_.Exception.Message
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "          TEST SUMMARY" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$totalTests = $TEST_RESULTS.Count
$passedTests = ($TEST_RESULTS | Where-Object { $_.Passed -eq $true }).Count
$failedTests = $totalTests - $passedTests
$successRate = [math]::Round(($passedTests / $totalTests) * 100, 2)

Write-Host "Total Tests  : $totalTests" -ForegroundColor White
Write-Host "Passed       : $passedTests" -ForegroundColor Green
Write-Host "Failed       : $failedTests" -ForegroundColor Red
Write-Host "Success Rate : $successRate%" -ForegroundColor $(if ($successRate -ge 90) { "Green" } elseif ($successRate -ge 70) { "Yellow" } else { "Red" })
Write-Host ""

if ($failedTests -gt 0) {
    Write-Host "❌ Failed Tests:" -ForegroundColor Red
    $TEST_RESULTS | Where-Object { $_.Passed -eq $false } | ForEach-Object {
        Write-Host "   - $($_.Test): $($_.Message)" -ForegroundColor Yellow
    }
    Write-Host ""
}

if ($successRate -eq 100) {
    Write-Host "🎉 ALL TESTS PASSED! System is working perfectly!" -ForegroundColor Green
} elseif ($successRate -ge 90) {
    Write-Host "✅ TESTS MOSTLY PASSED! Minor issues detected." -ForegroundColor Yellow
} elseif ($successRate -ge 70) {
    Write-Host "⚠️ SOME TESTS FAILED. Please review the errors above." -ForegroundColor Yellow
} else {
    Write-Host "❌ MANY TESTS FAILED. Critical issues detected!" -ForegroundColor Red
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
