$endpoints = @(
    "http://localhost:8080/",
    "http://localhost:8080/index.html",
    "http://localhost:8080/assets/css/styles.css",
    "http://localhost:8080/services/adhd-evaluation.html",
    "http://localhost:8080/services/adult-psychotherapy.html",
    "http://localhost:8080/services/child-adolescent-therapy.html",
    "http://localhost:8080/services/medication-management.html",
    "http://localhost:8080/valant-portal-preview.html"
)

$allOk = $true
foreach ($ep in $endpoints) {
    try {
        $res = Invoke-WebRequest -Uri $ep -UseBasicParsing -TimeoutSec 5
        if ($res.StatusCode -eq 200) {
            Write-Host "[200 OK] $ep" -ForegroundColor Green
        } else {
            Write-Host "[$($res.StatusCode)] $ep" -ForegroundColor Yellow
            $allOk = $false
        }
    } catch {
        Write-Host "[ERROR] $ep -> $($_.Exception.Message)" -ForegroundColor Red
        $allOk = $false
    }
}

if ($allOk) {
    Write-Host "`nAll endpoints verified successfully (200 OK)!" -ForegroundColor Green
} else {
    Write-Host "`nSome endpoints failed verification." -ForegroundColor Red
}
