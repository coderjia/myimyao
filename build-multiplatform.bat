@echo off
setlocal enabledelayedexpansion

REM Multi-platform Docker Build Script for Windows
REM Supports Windows, Linux and macOS

REM Default configuration
set IMAGE_NAME=myimyao-app
set TAG=latest
set PLATFORMS=linux/amd64,linux/arm64
set BUILD_ARGS=
set PUSH=false
set NO_CACHE=false
set DEFAULT_DOCKER_MIRROR=docker.m.daocloud.io
set DOCKER_MIRROR=!DEFAULT_DOCKER_MIRROR!

REM Main entry point
echo === Multi-platform Docker Build Script Windows ===
echo INFO Operating System: Windows
echo INFO Note: This script supports building for multiple Linux distributions
goto :parse_args

REM Show help information
:show_help
echo Usage: %~nx0 [OPTIONS]
echo.
echo Options:
echo   -n, --name NAME        Docker image name (default: myimyao-app)
echo   -t, --tag TAG          Docker image tag (default: latest)
echo   -p, --platforms PLAT   Target platforms (default: linux/amd64,linux/arm64)
echo   --push                 Push image to registry after build
echo   --no-cache             Build without using cache
echo   --build-arg ARG        Pass build-time variables
echo   --mirror URL           Set Docker mirror (default: docker.m.daocloud.io)
echo   -h, --help             Show this help message
echo.
echo Available mirrors:
echo   0. docker.m.daocloud.io          (DaoCloud - default)
echo   1. a7mywwsb.mirror.aliyuncs.com  (Alibaba Cloud)
echo   2. docker.xuanyuan.me            (Xuanyuan)
echo   3. docker.1ms.run                (1ms)
echo.
echo Examples:
echo   %~nx0                                    Basic build
echo   %~nx0 --name myapp --tag v1.0.0         Custom name and tag
echo   %~nx0 --platforms linux/amd64           Single platform
echo   %~nx0 --push                            Build and push
echo   %~nx0 --no-cache                        Build without cache
echo   %~nx0 --mirror docker.m.daocloud.io     Use DaoCloud mirror
goto :eof

REM Parse command line arguments
:parse_args
if "%~1"=="" goto :check_requirements
if "%~1"=="-n" (
    set IMAGE_NAME=%~2
    shift
    shift
    goto :parse_args
)
if "%~1"=="--name" (
    set IMAGE_NAME=%~2
    shift
    shift
    goto :parse_args
)
if "%~1"=="-t" (
    set TAG=%~2
    shift
    shift
    goto :parse_args
)
if "%~1"=="--tag" (
    set TAG=%~2
    shift
    shift
    goto :parse_args
)
if "%~1"=="-p" (
    set PLATFORMS=%~2
    shift
    shift
    goto :parse_args
)
if "%~1"=="--platforms" (
    set PLATFORMS=%~2
    shift
    shift
    goto :parse_args
)
if "%~1"=="--push" (
    set PUSH=true
    shift
    goto :parse_args
)
if "%~1"=="--no-cache" (
    set NO_CACHE=true
    shift
    goto :parse_args
)
if "%~1"=="--build-arg" (
    set BUILD_ARGS=!BUILD_ARGS! --build-arg %~2
    shift
    shift
    goto :parse_args
)
if "%~1"=="--mirror" (
    set DOCKER_MIRROR=%~2
    shift
    shift
    goto :parse_args
)
if "%~1"=="-h" goto :show_help
if "%~1"=="--help" goto :show_help
echo Unknown option: %~1
goto :show_help

REM Check Docker and buildx
:check_requirements
echo INFO Checking requirements...

REM Check Docker
docker --version >nul 2>&1
if errorlevel 1 (
    echo ERROR Docker is not installed
    exit /b 1
)

REM Get Docker version
for /f "tokens=3" %%i in ('docker --version') do set DOCKER_VERSION=%%i
echo INFO Docker version: !DOCKER_VERSION!

REM Check buildx
docker buildx version >nul 2>&1
if errorlevel 1 (
    echo WARNING Docker buildx not available, using regular build
    goto :fallback_build
)

echo INFO Docker buildx is available
goto :setup_builder

REM Create buildx builder
:setup_builder
echo INFO Setting up buildx builder...

REM Check if builder already exists
docker buildx ls | findstr "multiplatform-builder" >nul 2>&1
if errorlevel 1 (
    docker buildx create --name multiplatform-builder --use
) else (
    docker buildx use multiplatform-builder
)

REM Start builder
echo INFO Bootstrapping buildx builder...
docker buildx inspect --bootstrap
if errorlevel 1 (
    echo WARNING Failed to bootstrap buildx builder, network issues detected
    echo INFO Falling back to regular Docker build...
    goto :fallback_build
)
goto :build_image

REM Build image
:build_image
REM If no mirror specified, prompt user to choose
if "!DOCKER_MIRROR!"=="!DEFAULT_DOCKER_MIRROR!" (
    echo.
    echo Please select Docker mirror source:
    echo 0. DaoCloud Mirror ^(default^): docker.m.daocloud.io
    echo 1. Alibaba Cloud Mirror: a7mywwsb.mirror.aliyuncs.com
    echo 2. Xuanyuan Mirror: docker.xuanyuan.me
    echo 3. 1ms Mirror: docker.1ms.run
    echo.
    set /p "mirror_choice=Please enter your choice (0-3, press Enter for default): "
    
    if "!mirror_choice!"=="" set "mirror_choice=0"
    if "!mirror_choice!"=="0" set "DOCKER_MIRROR=docker.m.daocloud.io"
    if "!mirror_choice!"=="1" set "DOCKER_MIRROR=a7mywwsb.mirror.aliyuncs.com"
    if "!mirror_choice!"=="2" set "DOCKER_MIRROR=docker.xuanyuan.me"
    if "!mirror_choice!"=="3" set "DOCKER_MIRROR=docker.1ms.run"
    
    echo Selected mirror source: !DOCKER_MIRROR!
    echo.
)

echo INFO Building Docker image...
echo INFO Image: !IMAGE_NAME!:!TAG!
echo INFO Platforms: !PLATFORMS!
echo INFO OS: Windows
echo INFO Docker Mirror: !DOCKER_MIRROR!
echo INFO Using mirror for faster image pulling...

REM Build command
set BUILD_CMD=docker buildx build

REM Add platform parameter
set BUILD_CMD=!BUILD_CMD! --platform !PLATFORMS!

REM Add tag
set BUILD_CMD=!BUILD_CMD! -t !IMAGE_NAME!:!TAG!

REM Add build arguments
if not "!BUILD_ARGS!"=="" (
    set BUILD_CMD=!BUILD_CMD! !BUILD_ARGS!
)

REM Add Docker mirror build argument
set BUILD_CMD=!BUILD_CMD! --build-arg DOCKER_MIRROR=!DOCKER_MIRROR!

REM Add no-cache parameter
if "!NO_CACHE!"=="true" (
    set BUILD_CMD=!BUILD_CMD! --no-cache
)

REM Add push parameter
if "!PUSH!"=="true" (
    set BUILD_CMD=!BUILD_CMD! --push
) else (
    set BUILD_CMD=!BUILD_CMD! --load
)

REM Add build context
set BUILD_CMD=!BUILD_CMD! .

echo INFO Executing: !BUILD_CMD!
!BUILD_CMD!

if errorlevel 1 (
    echo WARNING Multi-platform build failed, attempting fallback...
    goto :fallback_build
)

goto :success

REM Fallback build
:fallback_build
echo INFO Falling back to regular Docker build...

set FALLBACK_CMD=docker build

if "!NO_CACHE!"=="true" (
    set FALLBACK_CMD=!FALLBACK_CMD! --no-cache
)

REM Add Docker mirror build argument
set FALLBACK_CMD=!FALLBACK_CMD! --build-arg DOCKER_MIRROR=!DOCKER_MIRROR!

set FALLBACK_CMD=!FALLBACK_CMD! -t !IMAGE_NAME!:!TAG! .

echo INFO Executing: !FALLBACK_CMD!
!FALLBACK_CMD!

if errorlevel 1 (
    echo ERROR Build failed
    exit /b 1
)

REM Success
:success
echo SUCCESS Build completed successfully!

REM Show image information
echo INFO Image information:
docker images | findstr "!IMAGE_NAME!"

REM Script end
exit /b 0