$ServicePath = Get-Location

Write-Host "parando servicios..." -ForegroundColor Cyan
& "$ServicePath\email.exe" start
& "$ServicePath\queue.exe" start
& "$ServicePath\reverb.exe" start
& "$ServicePath\schedule.exe" start

Write-Host "¡Todo listo!" -ForegroundColor Yellow