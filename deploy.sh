#!/bin/bash

# Laravel Docker部署脚本

echo "开始部署Laravel应用到Docker..."

# 检查Docker是否运行
if ! docker info > /dev/null 2>&1; then
    echo "错误: Docker未运行，请先启动Docker服务"
    exit 1
fi

# 停止并删除现有容器
echo "停止现有容器..."
docker-compose down

# 构建新镜像
echo "构建Docker镜像..."
docker-compose build --no-cache

# 启动容器
echo "启动容器..."
docker-compose up -d

# 等待容器启动
echo "等待容器启动..."
sleep 10

# 检查容器状态
echo "检查容器状态..."
docker-compose ps

# 运行Laravel命令
echo "执行Laravel初始化命令..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app php artisan migrate --force

echo "部署完成！"
echo "应用已在端口8000上运行，请配置Nginx反向代理指向 http://localhost:8000"
echo ""
echo "Nginx配置示例:"
echo "location / {"
echo "    proxy_pass http://localhost:8000;"
echo "    proxy_set_header Host \$host;"
echo "    proxy_set_header X-Real-IP \$remote_addr;"
echo "    proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;"
echo "    proxy_set_header X-Forwarded-Proto \$scheme;"
echo "}"
echo ""
echo "查看日志: docker-compose logs -f"
echo "进入容器: docker-compose exec app bash"