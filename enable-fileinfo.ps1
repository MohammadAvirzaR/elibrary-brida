# Enable PHP fileinfo extension
$phpIniPath = "C:\Users\Mohammad Avirza R\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.ini"

Write-Host "Enabling fileinfo extension in PHP..." -ForegroundColor Yellow

# Read the php.ini file
$content = Get-Content $phpIniPath

# Replace ;extension=fileinfo with extension=fileinfo
$newContent = $content -replace ';extension=fileinfo', 'extension=fileinfo'

# Also uncomment pdo_firebird to remove the warning
$newContent = $newContent -replace ';extension=pdo_firebird', ';extension=pdo_firebird  ; Commented by script'

# Write back to file
Set-Content -Path $phpIniPath -Value $newContent

Write-Host "SUCCESS! fileinfo extension enabled" -ForegroundColor Green
Write-Host "Please restart the Laravel server (php artisan serve)" -ForegroundColor Yellow
Write-Host ""
Write-Host "Verification:" -ForegroundColor Cyan
php -m | Select-String "fileinfo"
