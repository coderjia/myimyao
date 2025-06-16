# 使用官方PHP 8.2 CLI镜像作为基础镜像
# 多平台构建支持
# 支持用户选择镜像源
ARG DOCKER_MIRROR=docker.m.daocloud.io
FROM --platform=$BUILDPLATFORM ${DOCKER_MIRROR}/library/php:8.2-cli

# 设置构建参数
ARG TARGETPLATFORM
ARG BUILDPLATFORM
ARG TARGETOS
ARG TARGETARCH

# 设置工作目录
WORKDIR /var/www/myimyao

# 配置镜像源（支持多地区和多发行版）
RUN if [ "$TARGETOS" = "linux" ]; then \
        # 检测基础镜像类型和网络环境
        if [ -f /etc/debian_version ]; then \
            echo "Detected Debian-based system" && \
            # 检测Debian版本
            DEBIAN_VERSION=$(cat /etc/debian_version | cut -d. -f1) && \
            if [ "$DEBIAN_VERSION" = "12" ] || grep -q "bookworm" /etc/os-release 2>/dev/null; then \
                DEBIAN_CODENAME="bookworm"; \
            elif [ "$DEBIAN_VERSION" = "11" ] || grep -q "bullseye" /etc/os-release 2>/dev/null; then \
                DEBIAN_CODENAME="bullseye"; \
            else \
                DEBIAN_CODENAME="bookworm"; \
            fi && \
            echo "Using Debian $DEBIAN_CODENAME" && \
            # 尝试多个国内镜像源，优先使用速度最快的
             if timeout 2 curl -s --connect-timeout 2 https://mirrors.tuna.tsinghua.edu.cn/debian/ > /dev/null 2>&1; then \
                 echo "Using Tsinghua University mirrors for faster download" && \
                 echo "# Tsinghua University Debian Mirror" > /etc/apt/sources.list && \
                 echo "deb https://mirrors.tuna.tsinghua.edu.cn/debian/ $DEBIAN_CODENAME main contrib non-free non-free-firmware" >> /etc/apt/sources.list && \
                 echo "deb https://mirrors.tuna.tsinghua.edu.cn/debian/ $DEBIAN_CODENAME-updates main contrib non-free non-free-firmware" >> /etc/apt/sources.list && \
                 echo "deb https://mirrors.tuna.tsinghua.edu.cn/debian-security/ $DEBIAN_CODENAME-security main contrib non-free non-free-firmware" >> /etc/apt/sources.list && \
                 echo "# Backup mirrors" >> /etc/apt/sources.list && \
                 echo "# deb http://deb.debian.org/debian $DEBIAN_CODENAME main" >> /etc/apt/sources.list && \
                 echo "# deb http://deb.debian.org/debian $DEBIAN_CODENAME-updates main" >> /etc/apt/sources.list && \
                 echo "# deb http://deb.debian.org/debian-security $DEBIAN_CODENAME-security main" >> /etc/apt/sources.list; \
             elif timeout 2 curl -s --connect-timeout 2 https://mirrors.aliyun.com/debian/ > /dev/null 2>&1; then \
                 echo "Using Aliyun mirrors for faster download" && \
                 echo "# Aliyun Debian Mirror" > /etc/apt/sources.list && \
                 echo "deb https://mirrors.aliyun.com/debian/ $DEBIAN_CODENAME main contrib non-free non-free-firmware" >> /etc/apt/sources.list && \
                 echo "deb https://mirrors.aliyun.com/debian/ $DEBIAN_CODENAME-updates main contrib non-free non-free-firmware" >> /etc/apt/sources.list && \
                 echo "deb https://mirrors.aliyun.com/debian-security/ $DEBIAN_CODENAME-security main contrib non-free non-free-firmware" >> /etc/apt/sources.list && \
                 echo "# Backup mirrors" >> /etc/apt/sources.list && \
                 echo "# deb http://deb.debian.org/debian $DEBIAN_CODENAME main" >> /etc/apt/sources.list && \
                 echo "# deb http://deb.debian.org/debian $DEBIAN_CODENAME-updates main" >> /etc/apt/sources.list && \
                 echo "# deb http://deb.debian.org/debian-security $DEBIAN_CODENAME-security main" >> /etc/apt/sources.list; \
             elif timeout 2 curl -s --connect-timeout 2 https://mirrors.ustc.edu.cn/debian/ > /dev/null 2>&1; then \
                 echo "Using USTC mirrors for faster download" && \
                 echo "# USTC Debian Mirror" > /etc/apt/sources.list && \
                 echo "deb https://mirrors.ustc.edu.cn/debian/ $DEBIAN_CODENAME main contrib non-free non-free-firmware" >> /etc/apt/sources.list && \
                 echo "deb https://mirrors.ustc.edu.cn/debian/ $DEBIAN_CODENAME-updates main contrib non-free non-free-firmware" >> /etc/apt/sources.list && \
                 echo "deb https://mirrors.ustc.edu.cn/debian-security/ $DEBIAN_CODENAME-security main contrib non-free non-free-firmware" >> /etc/apt/sources.list && \
                 echo "# Backup mirrors" >> /etc/apt/sources.list && \
                 echo "# deb http://deb.debian.org/debian $DEBIAN_CODENAME main" >> /etc/apt/sources.list && \
                 echo "# deb http://deb.debian.org/debian $DEBIAN_CODENAME-updates main" >> /etc/apt/sources.list && \
                 echo "# deb http://deb.debian.org/debian-security $DEBIAN_CODENAME-security main" >> /etc/apt/sources.list; \
             else \
                  echo "Using default Debian mirrors"; \
              fi && \
              # 清理可能存在的额外源文件和配置apt行为
              rm -f /etc/apt/sources.list.d/* && \
              echo "Acquire::http::Pipeline-Depth 0;" > /etc/apt/apt.conf.d/99nopipelining && \
              echo "Acquire::http::No-Cache true;" >> /etc/apt/apt.conf.d/99nopipelining && \
              echo "Acquire::BrokenProxy true;" >> /etc/apt/apt.conf.d/99nopipelining && \
              echo "APT sources configuration completed"; \
         elif [ -f /etc/redhat-release ]; then \
             echo "Detected RHEL-based system" && \
             if timeout 3 ping -c 1 mirrors.aliyun.com > /dev/null 2>&1; then \
                 echo "Using Aliyun CentOS mirrors for faster download in China" && \
                 sed -i 's|^mirrorlist=|#mirrorlist=|g' /etc/yum.repos.d/CentOS-*.repo && \
                 sed -i 's|^#baseurl=http://mirror.centos.org|baseurl=https://mirrors.aliyun.com|g' /etc/yum.repos.d/CentOS-*.repo; \
             else \
                 echo "Using default RHEL/CentOS mirrors"; \
             fi; \
         else \
             echo "Unknown Linux distribution, using default mirrors"; \
         fi; \
     fi

# 安装系统依赖
RUN apt-get update --allow-releaseinfo-change || apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 安装Node.js和npm（使用国内镜像源）
RUN if timeout 3 ping -c 1 mirrors.tuna.tsinghua.edu.cn > /dev/null 2>&1; then \
        echo "Using Tsinghua mirror for Node.js" && \
        curl -fsSL https://mirrors.tuna.tsinghua.edu.cn/nodesource/deb_22.x/setup | bash -; \
    elif timeout 3 ping -c 1 mirrors.aliyun.com > /dev/null 2>&1; then \
        echo "Using Aliyun mirror for Node.js" && \
        curl -fsSL https://mirrors.aliyun.com/nodesource/deb_22.x/setup | bash -; \
    else \
        echo "Using official NodeSource repository" && \
        curl -fsSL https://deb.nodesource.com/setup_22.x | bash -; \
    fi \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 安装Composer（使用选定的镜像源）
# 由于Docker COPY --from不支持ARG变量，我们直接下载Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 复制项目文件
COPY . /var/www/myimyao

# 跨平台权限设置
RUN if [ "$TARGETOS" = "linux" ]; then \
        # Linux平台：使用www-data用户
        chown -R www-data:www-data /var/www/myimyao && \
        chmod -R 775 /var/www/myimyao/storage && \
        chmod -R 775 /var/www/myimyao/bootstrap/cache; \
    else \
        # 其他平台：设置通用权限
        chmod -R 755 /var/www/myimyao && \
        chmod -R 777 /var/www/myimyao/storage && \
        chmod -R 777 /var/www/myimyao/bootstrap/cache; \
    fi

# 配置Composer镜像源（跨平台优化）
RUN if timeout 3 ping -c 1 mirrors.aliyun.com > /dev/null 2>&1; then \
        echo "Configuring Composer to use Aliyun mirror" && \
        composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/; \
    fi

# 安装PHP依赖
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 配置npm镜像源（跨平台优化）
RUN if timeout 3 ping -c 1 registry.npmmirror.com > /dev/null 2>&1; then \
        echo "Configuring npm to use Taobao mirror" && \
        npm config set registry https://registry.npmmirror.com; \
    fi

# 安装Node.js依赖并构建前端资源
RUN npm install --production=false && npm run build

# 复制环境配置文件
RUN cp .env.example .env

# 生成应用密钥
RUN php artisan key:generate

# 暴露端口
EXPOSE 8000

# 启动PHP内置服务器
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]