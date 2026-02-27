$ServicePath = Get-Location

Write-Host "parando servicios..." -ForegroundColor Cyan
& "$ServicePath\email.exe" status
& "$ServicePath\queue.exe" status
& "$ServicePath\reverb.exe" status
& "$ServicePath\schedule.exe" status

Write-Host "¡Todo listo!" -ForegroundColor Yellow