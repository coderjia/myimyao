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
docker stop myimyao-app 2>/dev/null || true
docker rm myimyao-app 2>/dev/null || true

# 构建新镜像
echo "构建Docker镜像..."
docker build -t myimyao-app .

# 启动容器
echo "启动容器..."
docker run -d --name myimyao-app \
  -p 8000:8000 \
  --add-host=host.docker.internal:host-gateway \
  -v "$(pwd):/var/www/myimyao" \
  -v myimyao_storage:/var/www/myimyao/storage \
  myimyao-app

# 等待容器启动
echo "等待容器启动..."
sleep 10

# 检查容器状态
echo "检查容器状态..."
docker ps | grep myimyao-app

# 设置storage目录权限
echo "设置storage目录权限..."
docker exec myimyao-app chown -R www-data:www-data /var/www/myimyao/storage
docker exec myimyao-app chown -R www-data:www-data /var/www/myimyao/bootstrap/cache
docker exec myimyao-app chmod -R 775 /var/www/myimyao/storage
docker exec myimyao-app chmod -R 775 /var/www/myimyao/bootstrap/cache

# 运行Laravel命令
echo "执行Laravel初始化命令..."
docker exec myimyao-app php artisan config:cache
docker exec myimyao-app php artisan route:cache
docker exec myimyao-app php artisan view:cache
docker exec myimyao-app php artisan migrate --force

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
echo "查看日志: docker logs -f myimyao-app"
echo "进入容器: docker exec -it myimyao-app bash"