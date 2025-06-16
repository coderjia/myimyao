#!/bin/bash

# 跨平台 Docker 构建脚本
# 支持 Windows、Linux 和 macOS

set -e

# 颜色输出
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 默认配置
DEFAULT_IMAGE_NAME="myimyao-app"
DEFAULT_TAG="latest"
DEFAULT_PLATFORMS="linux/amd64,linux/arm64"
DEFAULT_PUSH=false
DEFAULT_NO_CACHE=false
DEFAULT_BUILD_ARGS=""
DEFAULT_DOCKER_MIRROR="docker.m.daocloud.io"

IMAGE_NAME="$DEFAULT_IMAGE_NAME"
TAG="$DEFAULT_TAG"
PLATFORMS="$DEFAULT_PLATFORMS"
PUSH=$DEFAULT_PUSH
NO_CACHE=$DEFAULT_NO_CACHE
BUILD_ARGS="$DEFAULT_BUILD_ARGS"
DOCKER_MIRROR="$DEFAULT_DOCKER_MIRROR"

# 显示帮助信息
show_help() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  -n, --name NAME        Docker image name (default: myimyao-app)"
    echo "  -t, --tag TAG          Docker image tag (default: latest)"
    echo "  -p, --platforms PLAT   Target platforms (default: linux/amd64,linux/arm64)"
    echo "  --push                 Push image to registry after build"
    echo "  --no-cache             Build without using cache"
    echo "  --build-arg ARG        Pass build-time variables"
    echo "  --mirror <mirror_url>  Set Docker mirror (default: a7mywwsb.mirror.aliyuncs.com)"
    echo "  -h, --help             Show this help message"
    echo ""
    echo "Available mirrors:"
    echo "  a7mywwsb.mirror.aliyuncs.com  (Alibaba Cloud - default)"
    echo "  docker.m.daocloud.io          (DaoCloud)"
    echo "  docker.xuanyuan.me            (Xuanyuan)"
    echo "  docker.1ms.run                (1ms)"
    echo ""
    echo "Examples:"
    echo "  $0                                    # Basic build"
    echo "  $0 --name myapp --tag v1.0.0         # Custom name and tag"
    echo "  $0 --platforms linux/amd64           # Single platform"
    echo "  $0 --push                            # Build and push"
    echo "  $0 --no-cache                        # Build without cache"
    echo "  $0 --mirror docker.m.daocloud.io     # Use DaoCloud mirror"
}

# 解析命令行参数
while [[ $# -gt 0 ]]; do
    case $1 in
        -n|--name)
            IMAGE_NAME="$2"
            shift 2
            ;;
        -t|--tag)
            TAG="$2"
            shift 2
            ;;
        -p|--platforms)
            PLATFORMS="$2"
            shift 2
            ;;
        --push)
            PUSH=true
            shift
            ;;
        --no-cache)
            NO_CACHE=true
            shift
            ;;
        --build-arg)
            BUILD_ARGS="$BUILD_ARGS --build-arg $2"
            shift 2
            ;;
        --mirror)
            if [ -z "$2" ]; then
                echo -e "${RED}Error: --mirror requires a value${NC}"
                exit 1
            fi
            DOCKER_MIRROR="$2"
            shift 2
            ;;
        -h|--help)
            show_help
            exit 0
            ;;
        *)
            echo -e "${RED}Unknown option: $1${NC}"
            show_help
            exit 1
            ;;
    esac
done

# 检测操作系统和发行版
detect_os() {
    case "$(uname -s)" in
        Linux*)
            # 检测Linux发行版
            if [ -f /etc/os-release ]; then
                . /etc/os-release
                case "$ID" in
                    ubuntu|debian)
                        echo "Linux-Ubuntu/Debian"
                        ;;
                    rhel|centos|fedora|rocky|almalinux)
                        echo "Linux-RHEL/CentOS"
                        ;;
                    *)
                        echo "Linux-Other"
                        ;;
                esac
            elif [ -f /etc/redhat-release ]; then
                echo "Linux-RHEL/CentOS"
            elif [ -f /etc/debian_version ]; then
                echo "Linux-Ubuntu/Debian"
            else
                echo "Linux-Unknown"
            fi
            ;;
        Darwin*)    echo "macOS";;
        CYGWIN*|MINGW*|MSYS*) echo "Windows";;
        *)          echo "Unknown";;
    esac
}

# 检查 Docker 和 buildx
check_requirements() {
    echo -e "${BLUE}Checking requirements...${NC}"
    
    # 检查 Docker
    if ! command -v docker &> /dev/null; then
        echo -e "${RED}Error: Docker is not installed${NC}"
        exit 1
    fi
    
    # 检查 Docker 版本
    DOCKER_VERSION=$(docker --version | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')
    echo -e "${GREEN}Docker version: $DOCKER_VERSION${NC}"
    
    # 检查 buildx
    if ! docker buildx version &> /dev/null; then
        echo -e "${YELLOW}Warning: Docker buildx not available, using regular build${NC}"
        return 1
    fi
    
    echo -e "${GREEN}Docker buildx is available${NC}"
    return 0
}

# 创建 buildx builder
setup_builder() {
    echo -e "${BLUE}Setting up buildx builder...${NC}"
    
    # 创建新的 builder 实例
    if ! docker buildx ls | grep -q "multiplatform-builder"; then
        docker buildx create --name multiplatform-builder --use
    else
        docker buildx use multiplatform-builder
    fi
    
    # 启动 builder
    docker buildx inspect --bootstrap
}

# 构建镜像
build_image() {
    # 如果没有指定镜像源，提示用户选择
    if [ "$DOCKER_MIRROR" = "$DEFAULT_DOCKER_MIRROR" ]; then
        echo ""
        echo "请选择 Docker 镜像源："
        echo "0. DaoCloud 镜像源 (默认): docker.m.daocloud.io"
        echo "1. 阿里云镜像源: a7mywwsb.mirror.aliyuncs.com"
        echo "2. 轩辕镜像源: docker.xuanyuan.me"
        echo "3. 1ms 镜像源: docker.1ms.run"
        echo ""
        read -p "请输入选择 (0-3，直接回车使用默认): " mirror_choice
        
        case "$mirror_choice" in
            "")
                DOCKER_MIRROR="docker.m.daocloud.io"
                ;;
            0)
                DOCKER_MIRROR="docker.m.daocloud.io"
                ;;
            1)
                DOCKER_MIRROR="a7mywwsb.mirror.aliyuncs.com"
                ;;
            2)
                DOCKER_MIRROR="docker.xuanyuan.me"
                ;;
            3)
                DOCKER_MIRROR="docker.1ms.run"
                ;;
            *)
                echo "${RED}无效选择，使用默认镜像源${NC}"
                DOCKER_MIRROR="docker.m.daocloud.io"
                ;;
        esac
        
        echo "已选择镜像源: $DOCKER_MIRROR"
        echo ""
    fi

    echo -e "${BLUE}Building Docker image...${NC}"
    echo -e "${YELLOW}Image: $IMAGE_NAME:$TAG${NC}"
    echo -e "${YELLOW}Platforms: $PLATFORMS${NC}"
    echo -e "${YELLOW}OS: $(detect_os)${NC}"
    echo -e "${YELLOW}Docker Mirror: $DOCKER_MIRROR${NC}"
    
    # 构建命令
    BUILD_CMD="docker buildx build"
    
    # 添加平台参数
    BUILD_CMD="$BUILD_CMD --platform $PLATFORMS"
    
    # 添加标签
    BUILD_CMD="$BUILD_CMD -t $IMAGE_NAME:$TAG"
    
    # 添加构建参数
    if [ -n "$BUILD_ARGS" ]; then
        BUILD_CMD="$BUILD_CMD $BUILD_ARGS"
    fi
    
    # 添加 Docker 镜像参数
    BUILD_CMD="$BUILD_CMD --build-arg DOCKER_MIRROR=$DOCKER_MIRROR"
    
    # 添加 no-cache 参数
    if [ "$NO_CACHE" = true ]; then
        BUILD_CMD="$BUILD_CMD --no-cache"
    fi
    
    # 添加推送参数
    if [ "$PUSH" = true ]; then
        BUILD_CMD="$BUILD_CMD --push"
    else
        BUILD_CMD="$BUILD_CMD --load"
    fi
    
    # 添加构建上下文
    BUILD_CMD="$BUILD_CMD ."
    
    echo -e "${BLUE}Executing: $BUILD_CMD${NC}"
    eval $BUILD_CMD
}

# 主函数
main() {
    echo -e "${GREEN}=== Multi-platform Docker Build Script ===${NC}"
    echo -e "${BLUE}Operating System: $(detect_os)${NC}"
    
    # 检查要求
    if check_requirements; then
        # 设置 buildx builder
        setup_builder
        
        # 构建镜像
        build_image
    else
        # 回退到常规构建
        echo -e "${YELLOW}Falling back to regular Docker build...${NC}"
        
        FALLBACK_CMD="docker build"
        
        if [ "$NO_CACHE" = true ]; then
            FALLBACK_CMD="$FALLBACK_CMD --no-cache"
        fi
        
        FALLBACK_CMD="$FALLBACK_CMD -t $IMAGE_NAME:$TAG ."
        
        echo -e "${BLUE}Executing: $FALLBACK_CMD${NC}"
        eval $FALLBACK_CMD
    fi
    
    echo -e "${GREEN}Build completed successfully!${NC}"
    
    # 显示镜像信息
    echo -e "${BLUE}Image information:${NC}"
    docker images | grep "$IMAGE_NAME" | head -5
}

# 运行主函数
main "$@"