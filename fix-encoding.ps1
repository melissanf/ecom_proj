# Run this once: Right-click -> Run with PowerShell
# Fixes UTF-16 PHP files so XAMPP can execute them.

$root = $PSScriptRoot
$utf8 = New-Object System.Text.UTF8Encoding $false

Get-ChildItem -Path $root -Recurse -File | Where-Object {
    $_.Extension -match '^\.(php|css|js|html|htaccess|sql|md)$' -or $_.Name -eq '.htaccess'
} | ForEach-Object {
    $bytes = [System.IO.File]::ReadAllBytes($_.FullName)
    if ($bytes.Length -lt 2) { return }

    $isUtf16Le = ($bytes[0] -eq 0xFF -and $bytes[1] -eq 0xFE) -or (
        $bytes.Length -gt 10 -and ($bytes | Where-Object { $_ -eq 0 }).Count -gt ($bytes.Length / 4)
    )

    if ($isUtf16Le) {
        if ($bytes[0] -eq 0xFF -and $bytes[1] -eq 0xFE) {
            $text = [System.Text.Encoding]::Unicode.GetString($bytes, 2, $bytes.Length - 2)
        } else {
            $text = [System.Text.Encoding]::Unicode.GetString($bytes)
        }
        [System.IO.File]::WriteAllText($_.FullName, $text, $utf8)
        Write-Host "Fixed: $($_.FullName)"
    }
}

Write-Host ""
Write-Host "Done. Copy this folder to C:\xampp\htdocs\beauty-store if needed."
Write-Host "Then open http://localhost/beauty-store/install.php"
