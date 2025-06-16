# Docker 镜像源配置指南

本项目已配置使用 DaoCloud 镜像源来加速 Docker 镜像拉取。

## 自动配置（推荐）

项目的 Dockerfile 支持用户选择镜像源，默认使用 DaoCloud 镜像源：
- 基础镜像：`${DOCKER_MIRROR}/library/php:8.2-cli`
- Composer 镜像：`${DOCKER_MIRROR}/library/composer:latest`
- 默认镜像源：`docker.m.daocloud.io`

### 交互式选择镜像源（推荐）

使用构建脚本时，如果没有通过 `--mirror` 参数指定镜像源，程序会提示用户选择：

```bash
# Windows
build-multiplatform.bat

# Linux/macOS
./build-multiplatform.sh
```

程序会显示以下选项：
- 0. DaoCloud 镜像源 (默认): docker.m.daocloud.io
- 1. 阿里云镜像源: a7mywwsb.mirror.aliyuncs.com
- 2. 轩辕镜像源: docker.xuanyuan.me
- 3. 1ms 镜像源: docker.1ms.run

### 命令行指定镜像源

也可以通过命令行参数直接指定：

```bash
# 使用构建脚本指定镜像源
build-multiplatform.bat --mirror docker.xuanyuan.me
./build-multiplatform.sh --mirror docker.xuanyuan.me

# 直接使用 docker build 命令
docker build --build-arg DOCKER_MIRROR=docker.xuanyuan.me .
```

## 全局 Docker 镜像源配置

### Windows 系统

1. 打开 Docker Desktop
2. 点击设置图标（齿轮图标）
3. 选择 "Docker Engine"
4. 将以下配置添加到 JSON 配置中：

```json
{
  "registry-mirrors": [
    "https://docker.m.daocloud.io",
    "https://a7mywwsb.mirror.aliyuncs.com",
    "https://docker.xuanyuan.me",
    "https://docker.1ms.run"
  ]
}
```

5. 点击 "Apply & Restart"

### Linux 系统

1. 创建或编辑 `/etc/docker/daemon.json` 文件：

```bash
sudo mkdir -p /etc/docker
sudo tee /etc/docker/daemon.json <<-'EOF'
{
  "registry-mirrors": [
    "https://docker.m.daocloud.io",
    "https://a7mywwsb.mirror.aliyuncs.com",
    "https://docker.xuanyuan.me",
    "https://docker.1ms.run"
  ]
}
EOF
```

2. 重启 Docker 服务：

```bash
sudo systemctl daemon-reload
sudo systemctl restart docker
```

### macOS 系统

1. 打开 Docker Desktop
2. 点击设置图标
3. 选择 "Docker Engine"
4. 添加镜像源配置（同 Windows）
5. 点击 "Apply & Restart"

## 验证配置

运行以下命令验证镜像源配置是否生效：

```bash
docker info | grep -A 10 "Registry Mirrors"
```

## 可用的镜像源

- **DaoCloud**: `https://docker.m.daocloud.io`（首选，默认）
- **阿里云镜像**: `https://a7mywwsb.mirror.aliyuncs.com`（次选）
- **轩辕镜像**: `https://docker.xuanyuan.me`（第三选择）
- **1ms**: `https://docker.1ms.run`（第四选择）
- **网易**: `https://hub-mirror.c.163.com`
- **阿里云个人**: `https://registry.cn-hangzhou.aliyuncs.com`（需要注册）

## 注意事项

1. 配置镜像源后需要重启 Docker 服务
2. 某些镜像源可能不稳定，建议配置多个备用源
3. 如果遇到拉取失败，可以尝试切换到其他镜像源
4. 企业环境可能需要配置代理或内网镜像源

## 故障排除

### 镜像拉取失败

1. 检查网络连接
2. 尝试使用不同的镜像源
3. 清理 Docker 缓存：`docker system prune -a`
4. 重启 Docker 服务

### 配置不生效

1. 确认 JSON 格式正确
2. 重启 Docker Desktop 或服务
3. 检查 Docker 日志：`docker system events`