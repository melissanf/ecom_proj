$root = $PSScriptRoot
$utf8 = New-Object System.Text.UTF8Encoding $false
$fixed = 0

Get-ChildItem -Path $root -Recurse -File | ForEach-Object {
    $path = $_.FullName
    if ($path -match '\\(\.git|node_modules)\\') { return }

    $bytes = [System.IO.File]::ReadAllBytes($path)
    if ($bytes.Length -lt 2) { return }

    $text = $null
    $isUtf16 = $false

    if ($bytes[0] -eq 0xFF -and $bytes[1] -eq 0xFE) {
        $text = [System.Text.Encoding]::Unicode.GetString($bytes, 2, $bytes.Length - 2)
        $isUtf16 = $true
    } elseif ($bytes[0] -eq 0xFE -and $bytes[1] -eq 0xFF) {
        $text = [System.Text.Encoding]::BigEndianUnicode.GetString($bytes, 2, $bytes.Length - 2)
        $isUtf16 = $true
    } else {
        $sample = [Math]::Min(500, $bytes.Length)
        $nullPairs = 0
        for ($i = 0; $i -lt $sample - 1; $i += 2) {
            if ($bytes[$i] -ge 32 -and $bytes[$i] -le 126 -and $bytes[$i + 1] -eq 0) {
                $nullPairs++
            }
        }
        if ($nullPairs -gt 20) {
            $text = [System.Text.Encoding]::Unicode.GetString($bytes)
            $isUtf16 = $true
        }
    }

    if ($isUtf16 -and $text -ne $null) {
        [System.IO.File]::WriteAllText($path, $text, $utf8)
        Write-Host "Fixed: $($_.Name)"
        $script:fixed++
    }
}

Write-Host ""
Write-Host "Converted $fixed file(s) to UTF-8."
