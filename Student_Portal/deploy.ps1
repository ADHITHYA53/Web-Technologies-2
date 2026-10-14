# Quick deployment script for Student_Portal
$ErrorActionPreference = "Stop"

$tomcat = "C:\Tomcat\apache-tomcat-10.1.61-windows-x64\apache-tomcat-10.1.61"
$webappsTarget = "$tomcat\webapps\Student_Portal"
$workTarget = "$tomcat\work\Catalina\localhost\Student_Portal"

Write-Host "1. Purging JSP work cache..." -ForegroundColor Cyan
if (Test-Path $workTarget) {
    Remove-Item -Path $workTarget -Recurse -Force -ErrorAction SilentlyContinue
}

Write-Host "2. Syncing webapp files (JSP, CSS, WEB-INF)..." -ForegroundColor Cyan
Copy-Item -Path "src\main\webapp\*" -Destination "$webappsTarget\" -Recurse -Force

# Touch files to ensure newer timestamps than any cached versions
Get-ChildItem -Path $webappsTarget -Recurse | ForEach-Object {
    $_.LastWriteTime = (Get-Date)
}

Write-Host "Deployment completed successfully! Refresh your browser (Ctrl + F5)." -ForegroundColor Green
