param(
    [string]$Source = $PSScriptRoot,
    [string]$Dest = "C:\xampp\htdocs\beauty-store"
)

$ErrorActionPreference = "Stop"
$utf8 = New-Object System.Text.UTF8Encoding $false

$skip = @('deploy.ps1', 'SETUP-XAMPP.bat', 'FIX-NOW.bat', 'fix-encoding.bat', 'fix-encoding.ps1', 'convert-utf8.ps1')

Write-Host "Source: $Source"
Write-Host "Dest:   $Dest"

if (-not (Test-Path $Source)) {
    throw "Source folder not found: $Source"
}

if (Test-Path $Dest) {
    Remove-Item -Path $Dest -Recurse -Force
}
New-Item -ItemType Directory -Path $Dest -Force | Out-Null

$count = 0
Get-ChildItem -Path $Source -Recurse -File | ForEach-Object {
    if ($skip -contains $_.Name) { return }
    if ($_.Extension -notin @('.php', '.css', '.js', '.sql', '.md', '.txt', '.bat')) { return }

    $rel = $_.FullName.Substring($Source.Length).TrimStart('\')
    $target = Join-Path $Dest $rel
    $dir = Split-Path $target -Parent
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }

    $bytes = [System.IO.File]::ReadAllBytes($_.FullName)
    $text = $null

    if ($bytes.Length -ge 2 -and $bytes[0] -eq 0xFF -and $bytes[1] -eq 0xFE) {
        $text = [System.Text.Encoding]::Unicode.GetString($bytes, 2, $bytes.Length - 2)
    } else {
        $nulls = 0
        $max = [Math]::Min(400, $bytes.Length - 1)
        for ($i = 0; $i -lt $max; $i += 2) {
            if ($bytes[$i] -ge 9 -and $bytes[$i] -le 126 -and $bytes[$i + 1] -eq 0) { $nulls++ }
        }
        if ($nulls -gt 15) {
            $text = [System.Text.Encoding]::Unicode.GetString($bytes)
        } else {
            if ($bytes.Length -ge 3 -and $bytes[0] -eq 0xEF -and $bytes[1] -eq 0xBB -and $bytes[2] -eq 0xBF) {
                $text = [System.Text.Encoding]::UTF8.GetString($bytes, 3, $bytes.Length - 3)
            } else {
                $text = [System.Text.Encoding]::UTF8.GetString($bytes)
            }
        }
    }

    [System.IO.File]::WriteAllText($target, $text, $utf8)
    $count++
}

Write-Host "Copied $count files to htdocs (UTF-8)."
