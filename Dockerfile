# 使用官方PHP 8.2 CLI镜像作为基础镜像
FROM php:8.2-cli

# 设置工作目录
WORKDIR /var/www/myimyao

# 安装系统依赖
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 安装Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 复制项目文件
COPY . /var/www/myimyao

# 设置权限
RUN chown -R www-data:www-data /var/www/myimyao \
    && chmod -R 755 /var/www/myimyao/storage \
    && chmod -R 755 /var/www/myimyao/bootstrap/cache

# 安装PHP依赖
RUN composer install --no-dev --optimize-autoloader

# 安装Node.js依赖并构建前端资源
RUN npm install && npm run build

# 复制环境配置文件
RUN cp .env.example .env

# 生成应用密钥
RUN php artisan key:generate

# 暴露端口
EXPOSE 8000

# 启动PHP内置服务器
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]