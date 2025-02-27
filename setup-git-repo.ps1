# Git-Repository initialisieren und Dateien hinzufügen
# Dieses Skript initialisiert ein Git-Repository und fügt alle Dateien hinzu

# Prüfen, ob Git installiert ist
if (!(Get-Command git -ErrorAction SilentlyContinue)) {
    Write-Host "Git ist nicht installiert. Bitte installieren Sie Git und versuchen Sie es erneut."
    exit 1
}

# Aktuelles Verzeichnis als Git-Repository initialisieren
Write-Host "Initialisiere Git-Repository..."
git init

# Alle Dateien zum Repository hinzufügen
Write-Host "Füge Dateien zum Repository hinzu..."
git add .

# Ersten Commit erstellen
Write-Host "Erstelle ersten Commit..."
git commit -m "Initial commit: SchoenGruesse Theme und Plugins"

# Anweisungen für den Upload zu GitHub
Write-Host "`nRepository wurde erfolgreich erstellt!`n"
Write-Host "Um dieses Repository zu GitHub hochzuladen, führen Sie folgende Befehle aus:"
Write-Host "1. Erstellen Sie ein neues Repository auf GitHub (https://github.com/new)"
Write-Host "2. Führen Sie die folgenden Befehle aus:"
Write-Host "   git remote add origin https://github.com/BENUTZERNAME/schoenegruesse.git"
Write-Host "   git branch -M main"
Write-Host "   git push -u origin main"
Write-Host "`nErsetzen Sie 'BENUTZERNAME' durch Ihren GitHub-Benutzernamen."
