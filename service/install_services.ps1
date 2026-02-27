$ServicePath = Get-Location

Write-Host "Instalando servicios..." -ForegroundColor Cyan
& "$ServicePath\email.exe" install
& "$ServicePath\queue.exe" install
& "$ServicePath\reverb.exe" install
& "$ServicePath\schedule.exe" install

Start-Sleep -Seconds 2

Write-Host "Iniciando servicios..." -ForegroundColor Green
& "$ServicePath\email.exe" start
& "$ServicePath\queue.exe" start
& "$ServicePath\reverb.exe" start
& "$ServicePath\schedule.exe" start

Write-Host "¡Todo listo!" -ForegroundColor Yellow