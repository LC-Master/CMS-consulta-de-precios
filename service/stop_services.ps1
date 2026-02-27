$ServicePath = Get-Location

Write-Host "parando servicios..." -ForegroundColor Cyan
& "$ServicePath\email.exe" stop
& "$ServicePath\queue.exe" stop
& "$ServicePath\reverb.exe" stop
& "$ServicePath\schedule.exe" stop

Write-Host "¡Todo listo!" -ForegroundColor Yellow