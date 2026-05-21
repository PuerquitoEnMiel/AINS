$src = 'database/seeders/DatabaseSeeder.php'
$dst = 'database/seeders/DS_clean.php'
$content = Get-Content $src -Raw -Encoding UTF8
# Find line 417 (0-indexed: 416) which ends with "    }\n}\n"
$lines = $content -split "`n"
$kept = $lines[0..416]
$kept -join "`n" | Out-File $dst -Encoding UTF8 -NoNewline
Copy-Item $dst $src -Force
Remove-Item $dst
Write-Host "Done"
