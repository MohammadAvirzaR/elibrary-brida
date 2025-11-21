# View OTP Helper Script
# Run this after attempting registration to see the OTP code

Write-Host "=== Latest OTP Code ===" -ForegroundColor Green
Write-Host ""

$logFile = "storage\logs\laravel.log"

if (Test-Path $logFile) {
    $content = Get-Content $logFile -Tail 50 | Out-String

    # Extract OTP code
    if ($content -match "Kode OTP Anda adalah: (\d{6})") {
        $otp = $matches[1]
        Write-Host "OTP Code: " -NoNewline -ForegroundColor Cyan
        Write-Host $otp -ForegroundColor Yellow -BackgroundColor DarkBlue
        Write-Host ""
        Write-Host "Copy this code and paste it in the OTP verification modal." -ForegroundColor Gray
    } else {
        Write-Host "No OTP found in recent logs." -ForegroundColor Red
        Write-Host "Try registering again and re-run this script." -ForegroundColor Gray
    }
} else {
    Write-Host "Log file not found: $logFile" -ForegroundColor Red
}

Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
