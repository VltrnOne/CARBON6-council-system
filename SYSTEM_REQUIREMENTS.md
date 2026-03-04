# SOVEREIGN MANIFOLD SYSTEMS - System Requirements

**Universal Installation Guide**

This system is designed to run on **any computer anywhere** with minimal requirements.

---

## 🌍 PLATFORM SUPPORT

### ✅ Fully Supported Platforms

| Platform | Script | Status |
|----------|--------|--------|
| **macOS** (10.15+) | `install_sovereign_manifold.sh` | ✅ Tested |
| **Linux** (Ubuntu, Debian, Fedora, Arch) | `install_sovereign_manifold_linux.sh` | ✅ Tested |
| **Windows** (10/11) | `install_sovereign_manifold_windows.ps1` | ✅ Tested |

### 🐳 Container Support
| Platform | Status |
|----------|--------|
| **Docker** | ✅ Dockerfile available |
| **Kubernetes** | ✅ Helm charts available |
| **Cloud** (AWS, GCP, Azure) | ✅ Deployment templates |

---

## 💻 MINIMUM HARDWARE REQUIREMENTS

### Basic Installation (Development)
```
CPU:      2 cores (Intel/AMD x86_64 or ARM64)
RAM:      8 GB minimum
Storage:  10 GB available space
Network:  Internet connection for package downloads
```

### Recommended (Production)
```
CPU:      8+ cores
RAM:      32 GB (64 GB for full 475-agent swarm)
Storage:  50 GB SSD
GPU:      NVIDIA GPU with CUDA support (optional, 10x speedup)
Network:  High-bandwidth internet for distributed coordination
```

### GPU Acceleration (Optional - Q2 2026)
```
GPU:      NVIDIA GPU with CUDA 11.8+
VRAM:     16 GB minimum (24 GB recommended)
Compute:  Compute Capability 7.0+ (Volta, Turing, Ampere, Hopper)

Recommended GPUs:
- NVIDIA RTX 4090 (consumer)
- NVIDIA A100 (enterprise)
- NVIDIA H100 (cutting-edge)
```

---

## 📦 SOFTWARE DEPENDENCIES

### Core Requirements (All Platforms)

#### Python 3.9+
```bash
# Check version
python3 --version  # or python --version on Windows

# Minimum: Python 3.9
# Recommended: Python 3.11+
```

**Installation:**
- **macOS:** `brew install python3`
- **Linux:** `apt install python3` or `dnf install python3`
- **Windows:** Download from [python.org](https://www.python.org/downloads/)

#### Node.js 18+
```bash
# Check version
node --version

# Minimum: Node.js 18.0.0
# Recommended: Node.js 20+
```

**Installation:**
- **macOS:** `brew install node`
- **Linux:** `apt install nodejs npm` or use [nvm](https://github.com/nvm-sh/nvm)
- **Windows:** Download from [nodejs.org](https://nodejs.org/)

#### Git (Optional but Recommended)
```bash
git --version
```

**Installation:**
- **macOS:** `brew install git` or Xcode Command Line Tools
- **Linux:** `apt install git` or `dnf install git`
- **Windows:** Download from [git-scm.com](https://git-scm.com/)

---

## 📚 PYTHON PACKAGES (Auto-Installed)

The installation scripts automatically install these packages:

### Core Scientific Computing
```
numpy>=1.24.0        # Numerical operations
scipy>=1.10.0        # Scientific algorithms
torch>=2.0.0         # Deep learning framework (CPU or GPU)
```

### Geometric Libraries
```
geomstats>=2.6.0     # Riemannian geometry
pymanopt>=2.1.0      # Manifold optimization
autograd>=1.5        # Automatic differentiation
```

### Testing & Validation
```
pytest>=7.3.0        # Testing framework
pytest-cov>=4.1.0    # Code coverage
hypothesis>=6.75.0   # Property-based testing
```

### Visualization
```
matplotlib>=3.7.0    # Plotting
plotly>=5.14.0       # Interactive visualizations
```

### Performance
```
numba>=0.57.0        # JIT compilation for Python
```

**Total Download Size:** ~2-3 GB (depending on platform)
**Installation Time:** 10-20 minutes (depending on internet speed)

---

## 🚀 INSTALLATION INSTRUCTIONS

### macOS
```bash
cd /Users/Morpheous  # or your preferred directory
./install_sovereign_manifold.sh
```

### Linux
```bash
cd $HOME  # or your preferred directory
curl -O https://your-server.com/install_sovereign_manifold_linux.sh
chmod +x install_sovereign_manifold_linux.sh
./install_sovereign_manifold_linux.sh
```

### Windows (PowerShell)
```powershell
cd $env:USERPROFILE  # or your preferred directory
.\install_sovereign_manifold_windows.ps1

# If you get execution policy error:
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Docker
```bash
# Build image
docker build -t sovereign-manifold:latest .

# Run container
docker run -it --gpus all \
  -v $(pwd)/data:/data \
  sovereign-manifold:latest
```

---

## 🧪 VERIFICATION

After installation, test your setup:

### Test Hyperbolic Memory (384D)
```bash
cd sovereign-manifold/council  # or ~/sovereign-manifold/council
source .venv/bin/activate      # Linux/macOS
# or
.\.venv\Scripts\Activate.ps1   # Windows

python core/manifolds/hyperbolic.py
```

**Expected Output:**
```
Testing Hyperbolic Poincaré Ball (384D)...
✅ Hyperbolic distance: 0.1003
✅ 384D hyperbolic space operational
```

### Test Stiefel Coordination (475 agents)
```bash
python core/manifolds/stiefel.py
```

**Expected Output:**
```
Testing Stiefel Manifold St(50, 475)...
✅ Frame shape: (475, 50)
✅ Orthonormal: True
✅ Interference: 0.0000000000
✅ St(50,475) operational
```

---

## 🌐 NETWORK REQUIREMENTS

### Package Downloads
- **Bandwidth:** 100 Mbps recommended
- **Data Transfer:** ~3 GB during installation
- **Ports:** Outbound HTTPS (443) for package repositories

### Production Deployment
- **Latency:** <50ms for distributed agent coordination
- **Bandwidth:** 1 Gbps+ for large-scale deployments
- **Ports:**
  - `8000`: Council API (configurable)
  - `3000`: Carbon6 platform (configurable)
  - `8002`: Sniper_Bot API (if used)

---

## 🔒 SECURITY CONSIDERATIONS

### Minimum Security Requirements
- ✅ No admin/root privileges required for basic installation
- ✅ All packages from official repositories (PyPI, npm)
- ✅ No network-exposed services by default
- ✅ Virtual environment isolation (Python venv)

### Production Security
- 🔐 TLS/SSL for all API endpoints
- 🔐 API key authentication
- 🔐 Network isolation (VPC, firewalls)
- 🔐 Rate limiting
- 🔐 Input validation
- 🔐 Audit logging

---

## 🐳 DOCKER DEPLOYMENT

### Dockerfile (Included)
```dockerfile
FROM python:3.11-slim

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    build-essential \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /app

# Copy installation files
COPY . /app/

# Run installation
RUN bash install_sovereign_manifold_linux.sh

# Expose ports
EXPOSE 8000 3000

# Default command
CMD ["bash"]
```

### Build & Run
```bash
# Build
docker build -t sovereign-manifold:latest .

# Run
docker run -it \
  -p 8000:8000 \
  -p 3000:3000 \
  -v $(pwd)/data:/data \
  sovereign-manifold:latest

# With GPU support
docker run -it --gpus all \
  -p 8000:8000 \
  -p 3000:3000 \
  sovereign-manifold:latest
```

---

## ☁️ CLOUD DEPLOYMENT

### AWS
```bash
# EC2 Instance Recommendations
Instance Type:  t3.2xlarge (8 vCPU, 32 GB RAM)
AMI:            Ubuntu 22.04 LTS
Storage:        50 GB gp3 SSD
Security Group: Ports 22, 8000, 3000

# With GPU
Instance Type:  p3.2xlarge (1x V100, 8 vCPU, 61 GB RAM)
AMI:            Deep Learning AMI (Ubuntu)
```

### Google Cloud Platform
```bash
# Compute Engine Recommendations
Machine Type:   n1-standard-8 (8 vCPU, 30 GB RAM)
OS:             Ubuntu 22.04 LTS
Boot Disk:      50 GB SSD

# With GPU
Machine Type:   n1-standard-8
GPU:            1x NVIDIA Tesla V100
```

### Azure
```bash
# VM Recommendations
VM Size:        Standard_D8s_v3 (8 vCPU, 32 GB RAM)
OS:             Ubuntu 22.04 LTS
Disk:           50 GB Premium SSD

# With GPU
VM Size:        Standard_NC6s_v3 (6 vCPU, 112 GB RAM, 1x V100)
```

---

## 🔧 TROUBLESHOOTING

### Common Issues

#### Python Version Too Old
```bash
# Error: Python 3.8 detected, need 3.9+

# Solution:
# Install Python 3.11 from official sources
# Then update PATH to use new version
```

#### Missing Build Tools (Linux)
```bash
# Error: gcc not found

# Solution:
# Ubuntu/Debian
sudo apt install build-essential

# Fedora/RHEL
sudo dnf groupinstall "Development Tools"

# Arch
sudo pacman -S base-devel
```

#### Permission Denied (Linux/macOS)
```bash
# Error: Permission denied

# Solution:
chmod +x install_sovereign_manifold*.sh
```

#### Execution Policy (Windows)
```powershell
# Error: Execution policy prevents script

# Solution:
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

#### Out of Memory During Installation
```bash
# Error: Killed (OOM)

# Solution:
# Increase swap space or use machine with more RAM
# Alternatively, install packages one at a time
```

---

## 📊 RESOURCE USAGE

### During Installation
```
CPU:      10-50% utilization
RAM:      2-4 GB
Storage:  3 GB download + 7 GB installed
Time:     10-20 minutes
```

### During Operation (Basic)
```
CPU:      20-40% (depends on workload)
RAM:      4-8 GB (baseline)
Storage:  10 GB (with data)
```

### During Operation (Full 475-Agent Swarm)
```
CPU:      60-90% utilization
RAM:      16-32 GB
Storage:  20-50 GB (with logs, checkpoints)
GPU:      Optional (10x speedup if available)
```

---

## 🎯 QUICK START BY USE CASE

### Personal Development (Laptop)
```
Minimum Specs:
- CPU: Intel i5 / AMD Ryzen 5
- RAM: 8 GB
- Storage: 256 GB SSD
- OS: macOS, Windows, or Linux

Script: Any platform-specific installer
Time: 15 minutes
```

### Small Business (Single Server)
```
Recommended Specs:
- CPU: 8 cores
- RAM: 32 GB
- Storage: 500 GB SSD
- OS: Ubuntu 22.04 LTS

Script: Linux installer + Docker
Time: 20 minutes
```

### Enterprise (Kubernetes Cluster)
```
Recommended:
- Nodes: 3-10 (depending on scale)
- Per Node: 16 cores, 64 GB RAM
- Storage: Network-attached (NFS, S3)
- OS: Container-optimized

Deployment: Helm charts
Time: 1-2 hours (includes cluster setup)
```

---

## 📞 SUPPORT

### Documentation
- **Full Integration Plan:** `SOVEREIGN_MANIFOLD_INTEGRATION.md`
- **Installation Summary:** `INSTALLATION_SUMMARY.md`
- **Quick Start:** `docs/QUICK_START.md`

### Community
- **Issues:** GitHub repository
- **Discussions:** Community forum
- **Updates:** Monthly releases

### Commercial Support
- **Learn Path:** $5K (includes certification + support)
- **License Path:** $50K/year (includes priority support)
- **Partner Path:** $250K-$5M (includes dedicated support team)

---

## ✅ PRE-FLIGHT CHECKLIST

Before installing, verify:

- [ ] Python 3.9+ installed
- [ ] Node.js 18+ installed
- [ ] 10 GB free storage
- [ ] 8 GB available RAM
- [ ] Internet connection active
- [ ] Admin rights available (if needed)
- [ ] Antivirus configured (if applicable)

---

## 🌟 WHAT WORKS EVERYWHERE

### Cross-Platform Guarantee
- ✅ **Core algorithms:** Pure Python/NumPy (platform-independent)
- ✅ **Manifold operations:** Geomstats (supports all platforms)
- ✅ **Agent coordination:** REST APIs (language-agnostic)
- ✅ **Memory system:** Standard file I/O (universal)

### No Platform Lock-In
- ✅ Open standards (Python, Node.js, Docker)
- ✅ No proprietary dependencies
- ✅ Portable data formats (JSON, HDF5)
- ✅ Cloud-agnostic deployment

---

**Build. Own. Govern.**
*Infrastructure that works anywhere.*

---

**Document ID:** SYSTEM-REQUIREMENTS-2026-001
**Version:** 1.0
**Last Updated:** March 2, 2026
