#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Setup script untuk SIMPUS - Sistem Informasi Manajemen Puskesmas
.DESCRIPTION
    Script otomatis untuk setup project SIMPUS di Windows (Laragon/XAMPP)
    Menjalankan: composer install, npm install, migration, seeder, dll.
.EXAMPLE
    .\setup.ps1
#>

param(
    [switch]$SkipDependencies,
    [switch]$SkipDatabase,
    [switch]$SkipBuild
)

# Colors
$ColorReset = "`e[0m"
$ColorGreen = "`e[32m"
$ColorYellow = "`e[33m"
$ColorRed = "`e[31m"
$ColorBlue = "`e[34m"

function Write-Step {
    param([string]$Message)
    Write-Host "${ColorBlue}==>${ColorReset} ${Message}"
}

function Write-Success {
    param([string]$Message)
    Write-Host "${ColorGreen}✓${ColorReset} ${Message}"
}

function Write-Warning {
    param([string]$Message)
    Write-Host "${ColorYellow}⚠${ColorReset} ${Message}"
}

function Write-Error {
    param([string]$Message)
    Write-Host "${ColorRed}✗${ColorReset} ${Message}"
}

function Test-Command {
    param([string]$Command)
    $null = Get-Command $Command -ErrorAction SilentlyContinue
    return $?
}

# Banner
Write-Host @"
${ColorBlue}
╔═══════════════════════════════════════════╗
║                                           ║
║         SIMPUS Setup Script               ║
║   Sistem Informasi Manajemen Puskesmas   ║
║                                           ║
╚═══════════════════════════════════════════╝
${ColorReset}
"@

# Check prerequisites
Write-Step "Checking prerequisites..."

$missingTools = @()

if (-not (Test-Command "php")) {
    $missingTools += "PHP"
    Write-Error "PHP not found in PATH"
} else {
    $phpVersion = php -r "echo PHP_VERSION;"
    Write-Success "PHP $phpVersion found"
}

if (-not (Test-Command "composer")) {
    $missingTools += "Composer"
    Write-Error "Composer not found in PATH"
} else {
    $composerVersion = composer --version --no-ansi 2>$null | Select-String -Pattern "Composer version (\d+\.\d+\.\d+)" | ForEach-Object { $_.Matches.Groups[1].Value }
    Write-Success "Composer $composerVersion found"
}

if (-not (Test-Command "npm")) {
    $missingTools += "Node.js/npm"
    Write-Error "npm not found in PATH"
} else {
    $nodeVersion = node -v
    $npmVersion = npm -v
    Write-Success "Node.js $nodeVersion, npm $npmVersion found"
}

if (-not (Test-Command "mysql")) {
    Write-Warning "MySQL client not found in PATH (optional for database check)"
}

if ($missingTools.Count -gt 0) {
    Write-Error "Missing required tools: $($missingTools -join ', ')"
    Write-Host "Please install missing tools and add them to PATH, then try again."
    exit 1
}

# Check if .env exists
if (-not (Test-Path ".env")) {
    Write-Step "Creating .env from .env.example..."
    Copy-Item ".env.example" ".env"
    Write-Success ".env created"
    Write-Warning "Please edit .env and configure your database credentials before continuing"
    Write-Host "Press any key to continue after editing .env..."
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
} else {
    Write-Success ".env already exists"
}

# Install Composer dependencies
if (-not $SkipDependencies) {
    Write-Step "Installing Composer dependencies..."
    composer install
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Composer install failed"
        exit 1
    }
    Write-Success "Composer dependencies installed"

    # Install npm dependencies
    Write-Step "Installing npm dependencies..."
    npm install
    if ($LASTEXITCODE -ne 0) {
        Write-Error "npm install failed"
        exit 1
    }
    Write-Success "npm dependencies installed"
} else {
    Write-Warning "Skipping dependency installation"
}

# Generate app key
Write-Step "Generating application key..."
php artisan key:generate --force
if ($LASTEXITCODE -ne 0) {
    Write-Error "Failed to generate app key"
    exit 1
}
Write-Success "Application key generated"

# Database setup
if (-not $SkipDatabase) {
    Write-Step "Setting up database..."
    
    # Read .env for database info
    $dbName = (Get-Content .env | Select-String "^DB_DATABASE=(.+)$").Matches.Groups[1].Value
    $dbHost = (Get-Content .env | Select-String "^DB_HOST=(.+)$").Matches.Groups[1].Value
    $dbUser = (Get-Content .env | Select-String "^DB_USERNAME=(.+)$").Matches.Groups[1].Value
    
    Write-Host "Database: $dbName @ $dbHost (user: $dbUser)"
    
    # Ask to create database
    $createDb = Read-Host "Do you want to create the database '$dbName' if it doesn't exist? (y/n)"
    if ($createDb -eq 'y') {
        $dbPassword = Read-Host "Enter MySQL root password (press Enter if none)" -AsSecureString
        $dbPasswordPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto(
            [Runtime.InteropServices.Marshal]::SecureStringToBSTR($dbPassword)
        )
        
        if (Test-Command "mysql") {
            if ($dbPasswordPlain) {
                mysql -u root -p"$dbPasswordPlain" -e "CREATE DATABASE IF NOT EXISTS \`$dbName\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>$null
            } else {
                mysql -u root -e "CREATE DATABASE IF NOT EXISTS \`$dbName\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>$null
            }
            
            if ($LASTEXITCODE -eq 0) {
                Write-Success "Database '$dbName' created or already exists"
            } else {
                Write-Warning "Could not create database. Please create it manually."
            }
        } else {
            Write-Warning "MySQL client not available. Please create database '$dbName' manually."
        }
    }
    
    # Run migrations
    Write-Step "Running database migrations..."
    php artisan migrate --force
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Migration failed. Please check your database configuration in .env"
        exit 1
    }
    Write-Success "Database migrated"
    
    # Run seeders
    $runSeed = Read-Host "Do you want to run seeders (populate sample data)? (y/n)"
    if ($runSeed -eq 'y') {
        Write-Step "Running database seeders..."
        php artisan db:seed --force
        if ($LASTEXITCODE -ne 0) {
            Write-Error "Seeder failed"
            exit 1
        }
        Write-Success "Database seeded"
        
        Write-Host @"

${ColorGreen}Default Login Accounts:${ColorReset}
- Admin:        admin@simpus.test / password
- Dokter:       dokter@simpus.test / password
- Petugas Lab:  lab@simpus.test / password
- Apoteker:     apoteker@simpus.test / password
- Pendaftaran:  pendaftaran@simpus.test / password

"@
    }
} else {
    Write-Warning "Skipping database setup"
}

# Create storage link
Write-Step "Creating storage symbolic link..."
php artisan storage:link --force
if ($LASTEXITCODE -eq 0) {
    Write-Success "Storage link created"
} else {
    Write-Warning "Storage link creation failed (may already exist)"
}

# Build frontend assets
if (-not $SkipBuild) {
    Write-Step "Building frontend assets..."
    $buildChoice = Read-Host "Build for (1) Development or (2) Production? (1/2)"
    
    if ($buildChoice -eq '2') {
        npm run build
    } else {
        Write-Host "Running npm run dev (you may need to run this in a separate terminal)"
        npm run dev &
    }
    
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Frontend assets built"
    } else {
        Write-Warning "Asset build had warnings (may be normal)"
    }
} else {
    Write-Warning "Skipping frontend build"
}

# Clear caches
Write-Step "Clearing application caches..."
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
Write-Success "Caches cleared"

# Summary
Write-Host @"

${ColorGreen}
╔═══════════════════════════════════════════╗
║                                           ║
║          Setup Complete! 🎉              ║
║                                           ║
╚═══════════════════════════════════════════╝
${ColorReset}

${ColorBlue}Next Steps:${ColorReset}

1. ${ColorYellow}Start the application:${ColorReset}
   • With Laragon: Add virtual host (simpus.test) and restart
   • Or run: ${ColorGreen}php artisan serve${ColorReset}
   
2. ${ColorYellow}Start queue worker (in separate terminal):${ColorReset}
   ${ColorGreen}php artisan queue:work --queue=default,satusehat${ColorReset}

3. ${ColorYellow}Start Vite dev server (if not already running):${ColorReset}
   ${ColorGreen}npm run dev${ColorReset}

4. ${ColorYellow}Access the application:${ColorReset}
   • Laragon: ${ColorBlue}http://simpus.test${ColorReset}
   • Artisan serve: ${ColorBlue}http://localhost:8000${ColorReset}

5. ${ColorYellow}Configure integrations in .env (optional):${ColorReset}
   • BPJS_CONS_ID, BPJS_SECRET, BPJS_USER_KEY
   • SATUSEHAT_CLIENT_ID, SATUSEHAT_CLIENT_SECRET

${ColorYellow}Documentation:${ColorReset}
• README.md - Setup & usage guide
• docs/FITUR-LENGKAP-INTEGRASI.md - Full feature documentation
• docs/TESTING-BPJS-MOCK.md - Testing with mock data

${ColorGreen}Happy coding! 🚀${ColorReset}
"@
