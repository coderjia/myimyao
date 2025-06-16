# Laravel Docker 部署指南

本项目已配置为使用Docker容器运行，适用于已有MySQL和Nginx的服务器环境。

## 前提条件

- 服务器已安装Docker (版本 26.1.3+)
- 服务器已安装并运行MySQL
- 服务器已安装并运行Nginx

## 快速开始

### 1. 配置环境变量

复制并编辑Docker环境配置文件：
```bash
cp .env.docker .env
```

编辑 `.env` 文件，修改以下配置：
- `DB_PASSWORD`: 设置为你的MySQL root密码
- `APP_URL`: 设置为你的域名
- `APP_KEY`: 运行 `php artisan key:generate` 生成

### 2. 构建和启动容器

使用部署脚本（推荐）：
```bash
chmod +x deploy.sh
./deploy.sh
```

或手动执行：
```bash
# 构建镜像
docker-compose build

# 启动容器
docker-compose up -d

# 执行Laravel初始化
docker-compose exec app php artisan migrate
docker-compose exec app php artisan config:cache
```

### 3. 配置Nginx反向代理

在你的Nginx配置文件中添加以下配置：

```nginx
server {
    listen 80;
    server_name your-domain.com;

    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $server_name;
    }

    # 处理静态文件
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

重新加载Nginx配置：
```bash
sudo nginx -t
sudo systemctl reload nginx
```

## 常用命令

### 容器管理
```bash
# 查看容器状态
docker-compose ps

# 查看日志
docker-compose logs -f

# 停止容器
docker-compose down

# 重启容器
docker-compose restart
```

### Laravel命令
```bash
# 进入容器
docker-compose exec app bash

# 运行Artisan命令
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache
```

### 更新部署
```bash
# 拉取最新代码
git pull

# 重新构建和部署
./deploy.sh
```

## 文件结构

```
├── Dockerfile              # Docker镜像构建文件
├── docker-compose.yml      # Docker Compose配置
├── .dockerignore           # Docker构建忽略文件
├── .env.docker             # Docker环境配置模板
├── deploy.sh               # 部署脚本
└── DOCKER_README.md        # 本文档
```

## 故障排除

### 1. 容器无法连接MySQL
- 检查MySQL是否允许来自Docker容器的连接
- 确认 `.env` 文件中的数据库配置正确
- 检查防火墙设置

### 2. Nginx反向代理不工作
- 确认容器在端口8000上运行：`docker-compose ps`
- 检查Nginx配置语法：`nginx -t`
- 查看Nginx错误日志：`tail -f /var/log/nginx/error.log`

### 3. 权限问题
- 确保storage和bootstrap/cache目录有写权限
- 在容器内执行：`chown -R www-data:www-data storage bootstrap/cache`

### 4. 查看详细日志
```bash
# Laravel日志
docker-compose exec app tail -f storage/logs/laravel.log

# Apache日志
docker-compose exec app tail -f /var/log/apache2/error.log
```

## 安全建议

1. 定期更新Docker镜像
2. 使用强密码
3. 配置SSL证书
4. 限制数据库访问权限
5. 定期备份数据

## 性能优化

1. 启用Laravel缓存：
```bash
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

2. 配置Redis缓存（如果可用）
3. 优化Nginx配置
4. 使用CDN加速静态资源