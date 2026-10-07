param(
    [int]$Port = 8080,
    [string]$Root = $PSScriptRoot
)

$listener = New-Object System.Net.HttpListener
$prefix = "http://localhost:$Port/"
$listener.Prefixes.Add($prefix)

try {
    $listener.Start()
    Write-Host "STILLMIND_SERVER_STARTED: $prefix"
} catch {
    Write-Error "Failed to start listener on ${prefix}: $_"
    exit 1
}

$mimeTypes = @{
    ".html" = "text/html; charset=utf-8"
    ".htm"  = "text/html; charset=utf-8"
    ".css"  = "text/css; charset=utf-8"
    ".js"   = "application/javascript; charset=utf-8"
    ".json" = "application/json; charset=utf-8"
    ".jpg"  = "image/jpeg"
    ".jpeg" = "image/jpeg"
    ".png"  = "image/png"
    ".gif"  = "image/gif"
    ".svg"  = "image/svg+xml"
    ".ico"  = "image/x-icon"
    ".webp" = "image/webp"
    ".woff" = "font/woff"
    ".woff2"= "font/woff2"
    ".txt"  = "text/plain; charset=utf-8"
    ".xml"  = "application/xml; charset=utf-8"
}

$rootResolved = (Resolve-Path $Root).Path

while ($listener.IsListening) {
    try {
        $context = $listener.GetContext()
        $req = $context.Request
        $resp = $context.Response

        $rawPath = $req.Url.LocalPath.TrimStart('/')
        if ([string]::IsNullOrEmpty($rawPath)) {
            $rawPath = "index.html"
        }

        $decodedPath = [System.Uri]::UnescapeDataString($rawPath).Replace('/', '\')
        $fullPath = [System.IO.Path]::GetFullPath([System.IO.Path]::Combine($rootResolved, $decodedPath))

        if (!$fullPath.StartsWith($rootResolved, [System.StringComparison]::OrdinalIgnoreCase)) {
            $resp.StatusCode = 403
            $resp.Close()
            continue
        }

        if (Test-Path $fullPath -PathType Leaf) {
            $ext = [System.IO.Path]::GetExtension($fullPath).ToLower()
            if ($mimeTypes.ContainsKey($ext)) {
                $resp.ContentType = $mimeTypes[$ext]
            } else {
                $resp.ContentType = "application/octet-stream"
            }

            if ($ext -in @('.jpg', '.jpeg', '.png', '.svg', '.woff2')) {
                $resp.Headers.Add("Cache-Control", "public, max-age=86400")
            } else {
                $resp.Headers.Add("Cache-Control", "no-cache")
            }

            # Enable CORS for local testing if needed
            $resp.Headers.Add("Access-Control-Allow-Origin", "*")

            $bytes = [System.IO.File]::ReadAllBytes($fullPath)
            $resp.ContentLength64 = $bytes.Length
            $resp.StatusCode = 200
            if ($req.HttpMethod -ne 'HEAD') {
                $resp.OutputStream.Write($bytes, 0, $bytes.Length)
            }
        } else {
            $resp.StatusCode = 404
            $notFoundBytes = [System.Text.Encoding]::UTF8.GetBytes("404 Not Found")
            $resp.ContentLength64 = $notFoundBytes.Length
            if ($req.HttpMethod -ne 'HEAD') {
                $resp.OutputStream.Write($notFoundBytes, 0, $notFoundBytes.Length)
            }
        }
        $resp.Close()
    } catch {
        # Loop continues on transient errors
    }
}
