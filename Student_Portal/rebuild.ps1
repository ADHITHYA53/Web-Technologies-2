# Full rebuild & redeploy script for Java, JSP, and CSS
$ErrorActionPreference = "Stop"

$env:JAVA_HOME = "C:\Program Files\Eclipse Adoptium\jdk-21.0.12.101-hotspot"
$env:CATALINA_HOME = "C:\Tomcat\apache-tomcat-10.1.61-windows-x64\apache-tomcat-10.1.61"
$env:Path = "$env:JAVA_HOME\bin;C:\Maven\apache-maven-3.10.0-bin\apache-maven-3.10.0\bin;" + $env:Path

$tomcat = $env:CATALINA_HOME
$webappsTarget = "$tomcat\webapps\Student_Portal"
$workTarget = "$tomcat\work\Catalina\localhost\Student_Portal"

Write-Host "1. Building WAR with Maven..." -ForegroundColor Cyan
mvn clean package -DskipTests

Write-Host "2. Purging Tomcat work cache..." -ForegroundColor Cyan
if (Test-Path $workTarget) {
    Remove-Item -Path $workTarget -Recurse -Force -ErrorAction SilentlyContinue
}

Write-Host "3. Deploying classes & webapp files..." -ForegroundColor Cyan
Copy-Item -Path "target\Student_Portal.war" -Destination "$tomcat\webapps\Student_Portal.war" -Force
Copy-Item -Path "target\Student_Portal\*" -Destination "$webappsTarget\" -Recurse -Force

Get-ChildItem -Path $webappsTarget -Recurse | ForEach-Object {
    $_.LastWriteTime = (Get-Date)
}

Write-Host "Rebuild & deploy finished! Refresh your browser (Ctrl + F5)." -ForegroundColor Green
