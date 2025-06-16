<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $video->resource_name ?: '视频详情' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            line-height: 1.6;
            opacity: 0;
            animation: fadeIn 0.4s ease-out forwards;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            background: white;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideInDown 0.3s ease-out 0.1s both;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .back-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.15s ease;
            position: relative;
            overflow: hidden;
        }
        
        .back-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }
        
        .back-btn:active {
            transform: translateY(0);
            animation: pulse 0.15s ease;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(0.95); }
            100% { transform: scale(1); }
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        
        .logout-btn:active {
            transform: translateY(0);
            animation: pulse 0.3s ease;
        }
        
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 0 20px;
            animation: slideInUp 0.4s ease-out 0.2s both;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .video-player {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .video-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            background: #000;
            animation: scaleIn 0.3s ease-out 0.3s both;
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transition: all 0.3s ease;
        }
        
        .video-container video:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        .video-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #333 25%, transparent 25%), 
                        linear-gradient(-45deg, #333 25%, transparent 25%), 
                        linear-gradient(45deg, transparent 75%, #333 75%), 
                        linear-gradient(-45deg, transparent 75%, #333 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }
        
        .video-info {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: slideInRight 0.4s ease-out 0.4s both;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .video-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            line-height: 1.4;
        }
        
        .video-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .meta-item {
            color: #666;
            font-size: 14px;
        }
        
        .meta-label {
            font-weight: bold;
            color: #333;
        }
        
        .video-description {
            color: #555;
            font-size: 16px;
            line-height: 1.8;
            white-space: pre-wrap;
        }
        
        .description-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }
            
            .video-info {
                padding: 20px;
            }
            
            .video-title {
                font-size: 20px;
            }
            
            .video-meta {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="{{ route('video.list') }}" class="back-btn">← 返回列表</a>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="logout-btn">退出登录</button>
        </form>
    </div>
    
    <div class="container">
        <div class="video-player">
            <div class="video-container">
                @if($video->resource_url)
                    <video controls preload="metadata" autoplay muted>
                        <source src="https://mhealth-prod.oss-cn-beijing.aliyuncs.com/{{ $video->resource_url }}" type="video/mp4">
                        <source src="https://mhealth-prod.oss-cn-beijing.aliyuncs.com/{{ $video->resource_url }}" type="video/webm">
                        <source src="https://mhealth-prod.oss-cn-beijing.aliyuncs.com/{{ $video->resource_url }}" type="video/ogg">
                        您的浏览器不支持视频播放。
                    </video>
                @else
                    <div class="video-placeholder">
                        <div>
                            <div style="font-size: 48px; margin-bottom: 10px;">📹</div>
                            <div>暂无视频源</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="video-info">
            <h1 class="video-title">{{ $video->resource_name ?: '无标题' }}</h1>
            
            <div class="video-meta">
                <div class="meta-item">
                    <span class="meta-label">上传时间：</span>
                    {{ $video->upload_time ? date('Y年m月d日 H:i', strtotime($video->upload_time)) : '未知' }}
                </div>
                
                @if($video->operator_name)
                <div class="meta-item">
                    <span class="meta-label">上传者：</span>
                    {{ $video->operator_name }}
                </div>
                @endif
                
                @if($video->like_count)
                <div class="meta-item">
                    <span class="meta-label">点赞数：</span>
                    {{ $video->like_count }}
                </div>
                @endif
                
                @if($video->pv)
                <div class="meta-item">
                    <span class="meta-label">浏览量：</span>
                    {{ $video->pv }}
                </div>
                @endif
            </div>
            
            @if($video->resource_description)
            <div class="description-title">视频简介</div>
            <div class="video-description">{{ $video->resource_description }}</div>
            @endif
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.querySelector('video');
            const backBtn = document.querySelector('.back-btn');
            const logoutBtn = document.querySelector('.logout-btn');
            
            // 视频加载错误处理
            if (video) {
                video.addEventListener('error', function() {
                    const container = video.parentElement;
                    video.style.display = 'none';
                    
                    const placeholder = document.createElement('div');
                    placeholder.className = 'video-placeholder';
                    placeholder.innerHTML = `
                        <div>
                            <div style="font-size: 48px; margin-bottom: 10px;">⚠️</div>
                            <div>视频加载失败</div>
                        </div>
                    `;
                    
                    container.appendChild(placeholder);
                });
                
                // 视频加载成功动画
                 video.addEventListener('loadeddata', function() {
                     this.style.animation = 'videoFadeIn 0.4s ease-out';
                 });
            }
            
            // 返回按钮点击效果
            if (backBtn) {
                backBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // 创建波纹效果
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.3);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.3s linear;
                        pointer-events: none;
                    `;
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                         ripple.remove();
                     }, 300);
                    
                    // 页面淡出效果
                     document.body.style.transition = 'opacity 0.25s ease';
                     document.body.style.opacity = '0';
                     
                     // 延迟跳转
                     setTimeout(() => {
                         window.history.back();
                     }, 250);
                });
            }
            
            // 退出按钮点击效果
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    // 创建波纹效果
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.3);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.3s linear;
                        pointer-events: none;
                    `;
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 300);
                    
                    // 添加加载状态
                    this.textContent = '退出中...';
                    this.style.pointerEvents = 'none';
                    
                    // 页面淡出效果
                     document.body.style.transition = 'opacity 0.25s ease';
                     document.body.style.opacity = '0';
                });
            }
            
            // 添加动画样式
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
                
                @keyframes videoFadeIn {
                    from {
                        opacity: 0;
                        transform: scale(0.95);
                    }
                    to {
                        opacity: 1;
                        transform: scale(1);
                    }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>