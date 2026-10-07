param(
    [string]$SourceDir = $PSScriptRoot,
    [string]$TargetZip1 = "c:\Users\User\Downloads\stillmind-mental-health.zip",
    [string]$TargetZip2 = "c:\Users\User\Downloads\stillmind-main7\stillmind-mental-health.zip"
)

Write-Host "Creating production zip package from: $SourceDir"

if (Test-Path $TargetZip1) {
    Remove-Item $TargetZip1 -Force
}

$itemsToZip = Get-ChildItem -Path $SourceDir -Exclude "*.zip"
Compress-Archive -Path $itemsToZip -DestinationPath $TargetZip1 -CompressionLevel Optimal

Copy-Item -Path $TargetZip1 -Destination $TargetZip2 -Force

$zipInfo = Get-Item $TargetZip1
$sizeMb = [Math]::Round(($zipInfo.Length / 1MB), 2)

Write-Host "SUCCESS: Zip created at $TargetZip1 ($sizeMb MB)"
Write-Host "SUCCESS: Backup copy at $TargetZip2"
Start-Process "http://localhost:8080"
