# Skript zum Hochladen des Repositories zu GitHub
# Dieses Skript verbindet das lokale Repository mit dem angegebenen GitHub-Repository und lädt die Dateien hoch

# Prüfen, ob Git installiert ist
if (!(Get-Command git -ErrorAction SilentlyContinue)) {
    Write-Host "Git ist nicht installiert. Bitte installieren Sie Git und versuchen Sie es erneut."
    exit 1
}

# Prüfen, ob das Verzeichnis ein Git-Repository ist
if (!(Test-Path -Path ".git")) {
    Write-Host "Dieses Verzeichnis ist kein Git-Repository. Bitte führen Sie zuerst setup-git-repo.ps1 aus."
    exit 1
}

# Remote-Repository hinzufügen
Write-Host "Verbinde mit GitHub-Repository..."
git remote add origin https://github.com/sammyr/schoenegruesse.git

# Branch umbenennen
Write-Host "Benenne Branch in 'main' um..."
git branch -M main

# Dateien hochladen
Write-Host "Lade Dateien zu GitHub hoch..."
git push -u origin main

Write-Host "`nRepository wurde erfolgreich zu GitHub hochgeladen!"
Write-Host "Sie können es unter https://github.com/sammyr/schoenegruesse ansehen."
