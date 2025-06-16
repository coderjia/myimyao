# 多平台 Docker 部署指南

本指南详细说明如何在 Windows、Linux 和 macOS 平台上部署 MyImYao Laravel 应用。

## 📋 目录

- [系统要求](#系统要求)
- [快速开始](#快速开始)
- [平台特定配置](#平台特定配置)
- [构建选项](#构建选项)
- [部署模式](#部署模式)
- [故障排除](#故障排除)
- [性能优化](#性能优化)

## 🔧 系统要求

### 通用要求
- Docker 20.10+ (推荐 24.0+)
- Docker Compose 2.0+
- 至少 4GB 可用内存
- 至少 10GB 可用磁盘空间

### 平台特定要求

#### Windows
- Windows 10/11 (64位)
- WSL2 (推荐)
- Docker Desktop for Windows
- PowerShell 5.1+ 或 PowerShell Core 7+

#### macOS
- macOS 10.15+ (Catalina)
- Docker Desktop for Mac
- Homebrew (可选，用于安装依赖)

#### Linux
- **Ubuntu/Debian 系列**: Ubuntu 18.04+, Debian 10+
- **RHEL/CentOS 系列**: CentOS 7+, RHEL 8+, Rocky Linux 8+, AlmaLinux 8+
- **其他发行版**: Fedora, openSUSE 等主流发行版
- Docker Engine 20.10+
- Docker Compose 2.0+

## 🚀 快速开始

### 1. 克隆项目

```bash
git clone <your-repository-url>
cd myimyao
```

### 2. 配置环境变量

```bash
# 复制环境配置文件
cp .env.multiplatform .env.docker

# 编辑配置文件
# Windows: notepad .env.docker
# macOS/Linux: nano .env.docker
```

### 3. 选择构建方式

#### 方式一：使用构建脚本（推荐）

**Windows:**
```cmd
# 基础构建
build-multiplatform.bat

# 自定义构建
build-multiplatform.bat --name myapp --tag v1.0.0 --no-cache
```

**macOS/Linux:**
```bash
# 给脚本执行权限
chmod +x build-multiplatform.sh

# 基础构建
./build-multiplatform.sh

# 自定义构建
./build-multiplatform.sh --name myapp --tag v1.0.0 --no-cache
```

#### 方式二：使用 Docker Compose

```bash
# 构建并启动所有服务
docker compose -f docker-compose.multiplatform.yml up -d

# 仅构建镜像
docker compose -f docker-compose.multiplatform.yml build
```

### 4. 访问应用

- 应用地址: http://localhost:8000
- Nginx 代理: http://localhost:80
- MySQL: localhost:3306
- Redis: localhost:6379

## 🔧 平台特定配置

### Windows 配置

#### WSL2 优化
```bash
# 在 .wslconfig 文件中配置
[wsl2]
memory=4GB
processors=2
swap=2GB
```

#### 文件权限
```cmd
# Windows 下可能需要调整文件权限
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
```

#### 网络配置
```yaml
# docker-compose.override.yml for Windows
version: '3.8'
services:
  app:
    environment:
      - DB_HOST=host.docker.internal
```

### macOS 配置

#### 性能优化
```yaml
# docker-compose.override.yml for macOS
version: '3.8'
services:
  app:
    volumes:
      - .:/var/www/myimyao:cached
      - /var/www/myimyao/node_modules
      - /var/www/myimyao/vendor
```

#### 文件监听
```bash
# 安装 fswatch 用于文件监听
brew install fswatch
```

### Linux 配置

#### Ubuntu/Debian 系列配置

**包管理器优化:**
```bash
# 更新包列表
sudo apt update

# 安装必要工具
sudo apt install -y curl wget gnupg lsb-release

# 配置国内镜像源（可选）
sudo cp /etc/apt/sources.list /etc/apt/sources.list.backup
echo "deb https://mirrors.aliyun.com/ubuntu/ $(lsb_release -cs) main restricted universe multiverse" | sudo tee /etc/apt/sources.list
echo "deb https://mirrors.aliyun.com/ubuntu/ $(lsb_release -cs)-updates main restricted universe multiverse" | sudo tee -a /etc/apt/sources.list
sudo apt update
```

**用户权限:**
```bash
# 设置正确的用户权限
sudo chown -R $USER:$USER .
sudo chmod -R 755 storage bootstrap/cache

# 添加用户到docker组
sudo usermod -aG docker $USER
newgrp docker
```

**防火墙配置:**
```bash
# Ubuntu/Debian 使用 ufw
sudo ufw allow 8000/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw --force enable
```

#### RHEL/CentOS 系列配置

**包管理器优化:**
```bash
# CentOS 7/8
sudo yum update -y
sudo yum install -y curl wget

# RHEL 8+ / Rocky Linux / AlmaLinux
sudo dnf update -y
sudo dnf install -y curl wget

# 配置国内镜像源（CentOS 可选）
sudo cp /etc/yum.repos.d/CentOS-Base.repo /etc/yum.repos.d/CentOS-Base.repo.backup
sudo wget -O /etc/yum.repos.d/CentOS-Base.repo https://mirrors.aliyun.com/repo/Centos-8.repo
sudo yum makecache
```

**SELinux 配置:**
```bash
# 检查 SELinux 状态
getenforce

# 临时禁用 SELinux（如果需要）
sudo setenforce 0

# 永久禁用 SELinux（重启后生效）
sudo sed -i 's/SELINUX=enforcing/SELINUX=disabled/' /etc/selinux/config
```

**用户权限:**
```bash
# 设置正确的用户权限
sudo chown -R $USER:$USER .
sudo chmod -R 755 storage bootstrap/cache

# 添加用户到docker组
sudo usermod -aG docker $USER
newgrp docker
```

**防火墙配置:**
```bash
# CentOS/RHEL 使用 firewalld
sudo firewall-cmd --permanent --add-port=8000/tcp
sudo firewall-cmd --permanent --add-port=80/tcp
sudo firewall-cmd --permanent --add-port=443/tcp
sudo firewall-cmd --reload

# 检查防火墙状态
sudo firewall-cmd --list-all
```

#### 通用 Linux 配置

**Docker 安装验证:**
```bash
# 检查 Docker 版本
docker --version
docker compose version

# 测试 Docker 运行
docker run hello-world
```

**系统资源检查:**
```bash
# 检查可用内存
free -h

# 检查磁盘空间
df -h

# 检查 CPU 信息
lscpu
```

## 🏗️ 构建选项

### 多架构构建

```bash
# 构建 AMD64 和 ARM64 架构
./build-multiplatform.sh --platforms linux/amd64,linux/arm64

# 仅构建 ARM64 (适用于 Apple Silicon)
./build-multiplatform.sh --platforms linux/arm64

# 仅构建 AMD64 (适用于 Intel/AMD)
./build-multiplatform.sh --platforms linux/amd64
```

### 构建参数

```bash
# 传递构建时变量
./build-multiplatform.sh --build-arg PHP_VERSION=8.2 --build-arg NODE_VERSION=18

# 无缓存构建
./build-multiplatform.sh --no-cache

# 构建并推送到注册表
./build-multiplatform.sh --push
```

### 环境特定构建

```bash
# 开发环境
docker compose -f docker-compose.multiplatform.yml -f docker-compose.dev.yml up -d

# 生产环境
docker compose -f docker-compose.multiplatform.yml -f docker-compose.prod.yml up -d

# 测试环境
docker compose -f docker-compose.multiplatform.yml -f docker-compose.test.yml up -d
```

## 🚀 部署模式

### 开发模式

```bash
# 启用开发模式（代码热重载）
export DEV_MODE=true
export APP_DEBUG=true
docker compose -f docker-compose.multiplatform.yml up -d
```

### 生产模式

```bash
# 生产环境配置
export APP_ENV=production
export APP_DEBUG=false
export DEV_MODE=false
docker compose -f docker-compose.multiplatform.yml up -d
```

### 集群模式

```bash
# 启用队列和调度器
docker compose -f docker-compose.multiplatform.yml --profile queue --profile scheduler up -d
```

## 🔍 故障排除

### 常见问题

#### 1. 构建失败

```bash
# 清理 Docker 缓存
docker system prune -a

# 重新构建
./build-multiplatform.sh --no-cache
```

#### 2. 权限问题

```bash
# Linux/macOS
sudo chown -R $USER:$USER .
chmod -R 755 storage bootstrap/cache

# Windows (PowerShell as Administrator)
Get-Acl . | Set-Acl storage
Get-Acl . | Set-Acl bootstrap\cache
```

#### 3. 网络连接问题

```bash
# 检查网络连接
docker network ls
docker network inspect myimyao_myimyao-network

# 重新创建网络
docker network rm myimyao_myimyao-network
docker compose -f docker-compose.multiplatform.yml up -d
```

#### 4. 数据库连接问题

```bash
# 检查数据库状态
docker compose -f docker-compose.multiplatform.yml logs mysql

# 测试数据库连接
docker compose -f docker-compose.multiplatform.yml exec app php artisan tinker
# 在 tinker 中执行: DB::connection()->getPdo()
```

### 日志查看

```bash
# 查看所有服务日志
docker compose -f docker-compose.multiplatform.yml logs

# 查看特定服务日志
docker compose -f docker-compose.multiplatform.yml logs app
docker compose -f docker-compose.multiplatform.yml logs mysql
docker compose -f docker-compose.multiplatform.yml logs redis

# 实时查看日志
docker compose -f docker-compose.multiplatform.yml logs -f app
```

### 性能监控

```bash
# 查看容器资源使用情况
docker stats

# 查看容器详细信息
docker compose -f docker-compose.multiplatform.yml ps
docker inspect myimyao-app
```

## ⚡ 性能优化

### 镜像优化

1. **多阶段构建**: Dockerfile 已使用多阶段构建减少镜像大小
2. **层缓存**: 合理安排 Dockerfile 指令顺序以最大化缓存利用
3. **依赖优化**: 分离 PHP 和 Node.js 依赖安装

### 运行时优化

```yaml
# docker-compose.override.yml
version: '3.8'
services:
  app:
    deploy:
      resources:
        limits:
          cpus: '2.0'
          memory: 1G
        reservations:
          cpus: '1.0'
          memory: 512M
```

### 存储优化

```yaml
# 使用命名卷提高性能
volumes:
  mysql_data:
    driver: local
    driver_opts:
      type: none
      o: bind
      device: /opt/myimyao/mysql
```

## 📚 更多资源

- [Docker 官方文档](https://docs.docker.com/)
- [Laravel 部署文档](https://laravel.com/docs/deployment)
- [Docker Compose 参考](https://docs.docker.com/compose/)
- [多架构构建指南](https://docs.docker.com/build/building/multi-platform/)

## 🤝 贡献

欢迎提交 Issue 和 Pull Request 来改进这个多平台部署方案。

## 📄 许可证

本项目采用 MIT 许可证。详见 [LICENSE](LICENSE) 文件。