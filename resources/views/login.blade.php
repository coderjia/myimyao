<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会员卡登录</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            margin: 0;
            padding: 0;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="80" cy="40" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="40" cy="80" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>') repeat;
            pointer-events: none;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 30px;
            z-index: 1;
            position: relative;
        }
        
        .project-title {
            font-size: 36px;
            font-weight: 800;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 2px 10px rgba(79, 172, 254, 0.3);
            margin: 0;
            letter-spacing: 2px;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }
        
        .login-title {
            text-align: center;
            margin-bottom: 35px;
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
        }
        
        .page-footer {
            text-align: center;
            padding: 20px;
            z-index: 1;
            position: relative;
            margin-top: auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }
        
        .copyright, .icp {
            color: #6c7b7f;
            font-size: 12px;
            margin: 5px 0;
            opacity: 0.8;
        }
        
        .copyright {
            font-weight: 500;
        }
        
        .icp {
            font-weight: 400;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #5a6c7d;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 18px;
            border: 2px solid #e8ecf4;
            border-radius: 12px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.15s ease;
            backdrop-filter: blur(5px);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #4facfe;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
            transform: translateY(-1px);
        }
        
        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 8px 20px rgba(79, 172, 254, 0.3);
        }
        
        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(79, 172, 254, 0.4);
            background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
        }
        
        .error-message {
            background: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
            animation: slideInDown 0.2s ease-out;
        }
        
        /* 页面加载动画 */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-5px);
            }
            75% {
                transform: translateX(5px);
            }
        }
        
        @keyframes loading {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        
        /* 页面元素动画 */
        .main-content {
            animation: fadeInUp 0.3s ease-out;
        }
        
        .page-header {
            animation: fadeInUp 0.25s ease-out 0.05s both;
        }
        
        .login-container {
            animation: fadeInUp 0.3s ease-out 0.1s both;
        }
        
        .page-footer {
            animation: fadeInUp 0.25s ease-out 0.15s both;
        }
        
        /* 表单元素动画 */
        .form-group {
            animation: fadeInUp 0.25s ease-out both;
        }
        
        .form-group:nth-child(2) {
            animation-delay: 0.3s;
        }
        
        .form-group:nth-child(3) {
            animation-delay: 0.35s;
        }
        
        .login-btn {
            animation: fadeInUp 0.25s ease-out 0.4s both;
            position: relative;
            overflow: hidden;
        }
        
        .login-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }
        
        .login-btn:active::before {
            width: 300px;
            height: 300px;
        }
        
        .login-btn:active {
            animation: pulse 0.15s ease-in-out;
        }
        
        /* 输入框聚焦动画 */
        .form-group input:focus {
            animation: pulse 0.15s ease-in-out;
        }
        
        /* 错误状态动画 */
        .form-group.error {
            animation: shake 0.2s ease-in-out;
        }
        
        /* 加载状态 */
        .login-btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }
        
        .login-btn.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid #ffffff;
            border-radius: 50%;
            animation: loading 1s linear infinite;
        }
        
        /* 悬停动画增强 */
        .project-title {
            transition: all 0.3s ease;
        }
        
        .project-title:hover {
            transform: scale(1.05);
            text-shadow: 0 4px 15px rgba(79, 172, 254, 0.5);
        }
        
        /* 页面切换动画 */
        .page-transition {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .page-transition.active {
            opacity: 1;
            visibility: visible;
        }
        
        .page-transition::before {
            content: '';
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* 手机端响应式设计 */
        @media (max-width: 768px) {
            .main-content {
                padding: 15px 20px;
            }
            
            .page-header {
                margin-bottom: 25px;
            }
            
            .project-title {
                font-size: 28px;
                letter-spacing: 1px;
            }
            
            .login-container {
                padding: 40px 30px;
                margin: 0 20px;
                max-width: calc(100% - 40px);
            }
            
            .login-title {
                font-size: 20px;
                margin-bottom: 30px;
            }
            
            .form-group input {
                padding: 12px 15px;
                font-size: 16px;
            }
            
            .login-btn {
                padding: 12px;
                font-size: 16px;
            }
            
            .page-footer {
                margin-top: 25px;
            }
            
            .copyright, .icp {
                font-size: 11px;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 10px 15px;
            }
            
            .page-header {
                margin-bottom: 20px;
            }
            
            .project-title {
                font-size: 24px;
                letter-spacing: 0.5px;
            }
            
            .login-container {
                padding: 30px 25px;
                margin: 0 15px;
                max-width: calc(100% - 30px);
                border-radius: 15px;
            }
            
            .login-title {
                font-size: 18px;
                margin-bottom: 25px;
            }
            
            .page-footer {
                margin-top: 20px;
            }
            
            .copyright, .icp {
                font-size: 10px;
                margin: 3px 0;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="page-header">
            <h1 class="project-title">明医明药</h1>
        </div>
        
        <div class="login-container">
        <h1 class="login-title">会员卡登录</h1>
        
        @if($errors->has('login'))
            <div class="error-message">
                {{ $errors->first('login') }}
            </div>
        @endif
        
        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="card_number">会员卡号</label>
                <input type="text" id="card_number" name="card_number" 
                       value="{{ old('card_number') }}" 
                       placeholder="请输入会员卡号" required>
                @error('card_number')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="card_password">卡密码</label>
                <input type="password" id="card_password" name="card_password" 
                       placeholder="请输入卡密码" required>
                @error('card_password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="login-btn">登录</button>
        </form>
        </div>
    </div>
    
    <!-- 页面切换动画层 -->
    <div class="page-transition" id="pageTransition"></div>
    
    <div class="page-footer">
        <div class="copyright">© 2024 北京明医明药中医药研究院有限责任公司 版权所有</div>
        <div class="icp">ICP备案号：京ICP备17009715号-1</div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const loginBtn = document.querySelector('.login-btn');
            const pageTransition = document.getElementById('pageTransition');
            const inputs = document.querySelectorAll('input');
            
            // 表单提交动画
            form.addEventListener('submit', function(e) {
                // 添加加载状态
                loginBtn.classList.add('loading');
                loginBtn.textContent = '登录中...';
                
                // 延迟提交以显示动画效果
                e.preventDefault();
                
                // 验证表单
                let isValid = true;
                inputs.forEach(input => {
                    const formGroup = input.closest('.form-group');
                    if (!input.value.trim()) {
                        formGroup.classList.add('error');
                        isValid = false;
                        
                        // 移除错误状态
                        setTimeout(() => {
                            formGroup.classList.remove('error');
                        }, 200);
                    }
                });
                
                if (isValid) {
                    // 显示页面切换动画
                    setTimeout(() => {
                        pageTransition.classList.add('active');
                        
                        // 实际提交表单
                        setTimeout(() => {
                            form.submit();
                        }, 125);
                    }, 250);
                } else {
                    // 恢复按钮状态
                    setTimeout(() => {
                        loginBtn.classList.remove('loading');
                        loginBtn.textContent = '登录';
                    }, 125);
                }
            });
            
            // 输入框动画效果
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.closest('.form-group').classList.remove('error');
                });
                
                input.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        this.style.borderColor = '#4facfe';
                    }
                });
            });
            
            // 按钮点击波纹效果
            loginBtn.addEventListener('click', function(e) {
                if (!this.classList.contains('loading')) {
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
                    }, 150);
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
            
            // 页面加载完成后的动画
            setTimeout(() => {
                document.body.style.overflow = 'visible';
            }, 250);
        });
        
        // 页面卸载动画
        window.addEventListener('beforeunload', function() {
            const pageTransition = document.getElementById('pageTransition');
            if (pageTransition) {
                pageTransition.classList.add('active');
            }
        });
    </script>
</body>
</html>