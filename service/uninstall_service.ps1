$ServicePath = Get-Location

Write-Host "parando servicios..." -ForegroundColor Cyan
& "$ServicePath\email.exe" stop
& "$ServicePath\queue.exe" stop
& "$ServicePath\reverb.exe" stop
& "$ServicePath\schedule.exe" stop

Start-Sleep -Seconds 2

Write-Host "desinstalando servicios..." -ForegroundColor Green
& "$ServicePath\email.exe" uninstall
& "$ServicePath\queue.exe" uninstall
& "$ServicePath\reverb.exe" uninstall
& "$ServicePath\schedule.exe" uninstall

Write-Host "¡Todo listo!" -ForegroundColor Yellow