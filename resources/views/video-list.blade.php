<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>明医明药 - 视频列表</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
            padding: 20px;
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
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        
        .header h1 {
            color: #333;
            font-size: 24px;
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.15s ease;
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
            animation: pulse 0.15s ease;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(0.95); }
            100% { transform: scale(1); }
        }
        
        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
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
        
        .video-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(20px);
            animation: cardFadeIn 0.3s ease-out forwards;
        }
        
        .video-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .video-card:active {
            transform: translateY(-5px) scale(0.98);
            transition: all 0.05s ease;
        }
        
        @keyframes cardFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .video-thumbnail {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #eee;
        }
        
        .video-info {
            padding: 15px;
        }
        
        .video-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .video-date {
            color: #666;
            font-size: 14px;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 16px;
            animation: fadeIn 0.25s ease-out;
            position: relative;
        }
        
        .loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #ccc;
            border-top: 2px solid #666;
            border-radius: 50%;
            animation: spin 0.5s linear infinite;
            margin-left: 10px;
            vertical-align: middle;
        }
        
        .no-more {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 14px;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .placeholder-img {
            width: 100%;
            height: 200px;
            background: linear-gradient(45deg, #f0f0f0 25%, transparent 25%), 
                        linear-gradient(-45deg, #f0f0f0 25%, transparent 25%), 
                        linear-gradient(45deg, transparent 75%, #f0f0f0 75%), 
                        linear-gradient(-45deg, transparent 75%, #f0f0f0 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 14px;
        }
        
        /* 手机端响应式设计 */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .header {
                padding: 10px 15px;
                margin-bottom: 15px;
            }
            
            .header h1 {
                font-size: 20px;
            }
            
            .video-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
                max-width: 100%;
            }
            
            .video-thumbnail {
                height: 120px;
            }
            
            .placeholder-img {
                height: 120px;
                font-size: 12px;
            }
            
            .video-info {
                padding: 10px;
            }
            
            .video-title {
                font-size: 14px;
                margin-bottom: 5px;
            }
            
            .video-date {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>视频列表</h1>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="logout-btn">退出登录</button>
        </form>
    </div>
    
    <div class="video-grid" id="videoGrid">
        @foreach($videos as $video)
            <div class="video-card" onclick="goToVideo('{{ $video->id }}')">
                @if($video->resource_image_url)
                    <img src=https://mhealth-prod.oss-cn-beijing.aliyuncs.com/{{ $video->resource_image_url }}" alt="{{ $video->resource_name }}" class="video-thumbnail" 
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="placeholder-img" style="display: none;">暂无图片</div>
                @else
                    <div class="placeholder-img">暂无图片</div>
                @endif
                
                <div class="video-info">
                    <div class="video-title">{{ $video->resource_name ?: '无标题' }}</div>
                    <div class="video-date">{{ $video->upload_time ? date('Y-m-d', strtotime($video->upload_time)) : '未知日期' }}</div>
                </div>
            </div>
        @endforeach
    </div>
    
    <div id="loading" class="loading" style="display: none;">加载中...</div>
    <div id="noMore" class="no-more" style="display: none;">没有更多视频了</div>
    
    <script>
        let currentPage = 1;
        let isLoading = false;
        let hasMore = true;
        
        function goToVideo(id) {
            window.location.href = `/video/${id}`;
        }
        
        function loadMoreVideos() {
            if (isLoading || !hasMore) return;
            
            isLoading = true;
            document.getElementById('loading').style.display = 'block';
            
            fetch(`/videos/load-more?page=${currentPage + 1}`)
                .then(response => response.json())
                .then(data => {
                    if (data.videos && data.videos.length > 0) {
                        currentPage++;
                        
                        data.videos.forEach((video, index) => {
                            const videoCard = createVideoCard(video, currentPage === 1 ? index : 0);
                            document.getElementById('videoGrid').appendChild(videoCard);
                        });
                        
                        hasMore = data.hasMore;
                        if (!hasMore) {
                            document.getElementById('noMore').style.display = 'block';
                        }
                    } else {
                        hasMore = false;
                        document.getElementById('noMore').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('加载失败:', error);
                })
                .finally(() => {
                    isLoading = false;
                    document.getElementById('loading').style.display = 'none';
                });
        }
        
        function createVideoCard(video, index = 0) {
            const card = document.createElement('div');
            card.className = 'video-card';
            card.style.animationDelay = `${index * 0.05}s`;
            
            // 添加点击波纹效果
            card.addEventListener('click', function(e) {
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
                    background: rgba(79, 172, 254, 0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.3s linear;
                    pointer-events: none;
                    z-index: 1;
                `;
                
                this.style.position = 'relative';
                this.appendChild(ripple);
                
                setTimeout(() => {
                     ripple.remove();
                 }, 300);
                
                // 延迟跳转以显示动画
                 setTimeout(() => {
                     goToVideo(video.id);
                 }, 100);
            });
            
            const thumbnailHtml = video.resource_image_url 
                ? `<img src="https://mhealth-prod.oss-cn-beijing.aliyuncs.com/${video.resource_image_url}" alt="${video.resource_name}" class="video-thumbnail" 
                       onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                   <div class="placeholder-img" style="display: none;">暂无图片</div>`
                : `<div class="placeholder-img">暂无图片</div>`;
            
            const uploadDate = video.upload_time ? new Date(video.upload_time).toLocaleDateString('zh-CN') : '未知日期';
            
            card.innerHTML = `
                ${thumbnailHtml}
                <div class="video-info">
                    <div class="video-title">${video.resource_name || '无标题'}</div>
                    <div class="video-date">${uploadDate}</div>
                </div>
            `;
            
            return card;
        }
        
        // 监听滚动事件
        window.addEventListener('scroll', () => {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1000) {
                loadMoreVideos();
            }
        });
        
        // 添加波纹动画样式
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
        
        // 退出按钮点击效果
        document.querySelector('.logout-btn').addEventListener('click', function(e) {
            // 添加加载状态
            this.textContent = '退出中...';
            this.style.pointerEvents = 'none';
            
            // 页面淡出效果
             document.body.style.transition = 'opacity 0.25s ease';
             document.body.style.opacity = '0';
        });
    </script>
</body>
</html>