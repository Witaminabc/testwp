#Requires -Version 5.1
Set-Location "D:\osPanel543\OSPanel\domains\test"
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

$date = Get-Date -Format "yyyyMMdd-HHmmss"
$backupFile = "backups/db-$date.sql"
$phpPath = "D:\osPanel543\OSPanel\modules\PHP\PHP-8.1\php.exe"

# Создать папку
New-Item -Force -ItemType Directory -Path "backups" | Out-Null

Write-Host "📦 WP DB Backup..." -ForegroundColor Yellow
& $phpPath wp-cli.phar db export $backupFile

Write-Host "💾 Git..." -ForegroundColor Yellow
git add .
git status --porcelain | Out-String | ForEach-Object { if($_) { git commit -m "backup: БД $date + Doctors CPT" } }
git push origin main

Write-Host "✅ Готово! $backupFile" -ForegroundColor Green
