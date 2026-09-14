# Face API Runner Script
# Chạy Face API với venv từ đường dẫn tuyệt đối

Write-Host "🚀 FACE API RUNNER" -ForegroundColor Green
Write-Host "===================" -ForegroundColor Green

# Cấu hình đường dẫn venv (thay đổi theo nhu cầu)
$VENV_PATH = "E:\Python_Environments\face_api_env"
$CURRENT_VENV = "venv"  # venv hiện tại

# Kiểm tra xem có muốn dùng venv từ đường dẫn tuyệt đối không
$useAbsolutePath = $false

if ($useAbsolutePath) {
    # Sử dụng venv từ đường dẫn tuyệt đối
    $ACTIVATE_SCRIPT = "$VENV_PATH\Scripts\Activate.ps1"
    Write-Host "🔧 Using venv from: $VENV_PATH" -ForegroundColor Yellow
    
    # Kiểm tra venv có tồn tại không
    if (!(Test-Path $ACTIVATE_SCRIPT)) {
        Write-Host "❌ Virtual environment not found at: $VENV_PATH" -ForegroundColor Red
        Write-Host "💡 Creating new venv..." -ForegroundColor Yellow
        
        # Tạo thư mục nếu chưa có
        $parentDir = Split-Path $VENV_PATH -Parent
        if (!(Test-Path $parentDir)) {
            New-Item -ItemType Directory -Path $parentDir -Force
        }
        
        # Tạo venv mới
        python -m venv $VENV_PATH
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✅ Virtual environment created successfully!" -ForegroundColor Green
        } else {
            Write-Host "❌ Failed to create virtual environment" -ForegroundColor Red
            exit 1
        }
    }
} else {
    # Sử dụng venv local
    $ACTIVATE_SCRIPT = "$CURRENT_VENV\Scripts\Activate.ps1"
    Write-Host "🔧 Using local venv: $CURRENT_VENV" -ForegroundColor Yellow
    
    # Kiểm tra venv local có tồn tại không
    if (!(Test-Path $ACTIVATE_SCRIPT)) {
        Write-Host "❌ Local virtual environment not found" -ForegroundColor Red
        Write-Host "💡 Creating local venv..." -ForegroundColor Yellow
        
        python -m venv $CURRENT_VENV
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✅ Local virtual environment created successfully!" -ForegroundColor Green
        } else {
            Write-Host "❌ Failed to create local virtual environment" -ForegroundColor Red
            exit 1
        }
    }
}

Write-Host "🔄 Activating virtual environment..." -ForegroundColor Yellow

# Activate venv
try {
    & $ACTIVATE_SCRIPT
    Write-Host "✅ Virtual environment activated!" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to activate virtual environment" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Kiểm tra dependencies
Write-Host "📦 Checking dependencies..." -ForegroundColor Yellow
$requirementsFile = "requirements.txt"

if (Test-Path $requirementsFile) {
    # Kiểm tra một số package quan trọng
    $packages = @("flask", "requests", "opencv-python-headless", "insightface", "onnxruntime")
    $missingPackages = @()
    
    foreach ($package in $packages) {
        $installed = pip show $package 2>$null
        if ($LASTEXITCODE -ne 0) {
            $missingPackages += $package
        }
    }
    
    if ($missingPackages.Count -gt 0) {
        Write-Host "⚠️  Missing packages: $($missingPackages -join ', ')" -ForegroundColor Red
        Write-Host "💡 Installing missing packages..." -ForegroundColor Yellow
        
        pip install -r $requirementsFile
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✅ Dependencies installed successfully!" -ForegroundColor Green
        } else {
            Write-Host "❌ Failed to install dependencies" -ForegroundColor Red
            exit 1
        }
    } else {
        Write-Host "✅ All dependencies are installed!" -ForegroundColor Green
    }
} else {
    Write-Host "⚠️  requirements.txt not found" -ForegroundColor Yellow
}

# Kiểm tra face_api.py có tồn tại không
if (!(Test-Path "face_api.py")) {
    Write-Host "❌ face_api.py not found in current directory" -ForegroundColor Red
    exit 1
}

Write-Host "🚀 Starting Face API..." -ForegroundColor Green
Write-Host "===================" -ForegroundColor Green

# Chạy Face API
python face_api.py 