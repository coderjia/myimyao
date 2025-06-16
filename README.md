# 咪咪药视频管理系统

一个基于 Laravel 12 开发的会员卡视频管理系统，提供会员登录、视频列表浏览和视频详情查看功能。

## 项目特色

- 🎯 **会员卡认证系统** - 支持会员卡号和密码登录验证
- 🎬 **视频资源管理** - 完整的视频列表展示和详情查看
- 📱 **响应式设计** - 支持桌面端和移动端访问
- ⚡ **流畅动画效果** - 精心设计的页面切换和交互动画
- 🔒 **安全会话管理** - 基于 Laravel Session 的用户状态管理

## 技术栈

- **后端框架**: Laravel 12.x
- **PHP版本**: ^8.2
- **数据库**: MySQL (通过 Eloquent ORM)
- **前端**: Blade 模板引擎 + 原生 JavaScript
- **样式**: CSS3 + 响应式设计

## 功能模块

### 🔐 用户认证
- 会员卡号登录
- 密码验证
- 登录状态保持
- 安全退出

### 📺 视频管理
- 视频列表展示
- 分页加载更多
- 视频详情查看
- 资源状态管理

### 🎨 用户界面
- 现代化登录界面
- 流畅的页面切换动画
- 响应式布局设计
- 优雅的错误提示

## 快速开始

### 环境要求

- PHP >= 8.2
- Composer
- MySQL 5.7+
- Node.js & NPM (可选，用于前端资源编译)

### 安装步骤

1. **克隆项目**
   ```bash
   git clone <repository-url>
   cd myimyao
   ```

2. **安装依赖**
   ```bash
   composer install
   ```

3. **环境配置**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **数据库配置**
   
   编辑 `.env` 文件，配置数据库连接：
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=myimyao
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **数据库迁移**
   ```bash
   php artisan migrate
   ```

6. **启动服务**
   ```bash
   php artisan serve
   ```

   访问 `http://localhost:8000` 即可使用系统。

## 项目结构

```
myimyao/
├── app/
│   ├── Http/Controllers/
│   │   └── VideoController.php    # 主控制器
│   └── Models/
│       ├── MemberCard.php         # 会员卡模型
│       └── Resource.php           # 资源模型
├── resources/views/
│   ├── login.blade.php            # 登录页面
│   ├── video-list.blade.php       # 视频列表页面
│   └── video-detail.blade.php     # 视频详情页面
├── routes/
│   └── web.php                    # 路由定义
└── database/
    └── migrations/                # 数据库迁移文件
```

## 路由说明

| 路由 | 方法 | 功能 | 中间件 |
|------|------|------|--------|
| `/` | GET | 重定向到登录页 | - |
| `/login` | GET | 显示登录页面 | - |
| `/login` | POST | 处理登录请求 | - |
| `/logout` | POST | 用户退出登录 | - |
| `/videos` | GET | 视频列表页面 | - |
| `/videos/load-more` | GET | 加载更多视频 | - |
| `/video/{id}` | GET | 视频详情页面 | - |

## 数据模型

### MemberCard (会员卡)
- `card_number` - 卡号
- `card_password` - 卡密码
- `card_name` - 卡片名称
- `card_status` - 卡片状态
- `balance` - 余额
- `active_date` - 激活日期

### Resource (资源)
- `title` - 资源标题
- `description` - 资源描述
- `file_path` - 文件路径
- `status` - 资源状态

## 开发说明

### 代码规范
- 遵循 PSR-12 编码标准
- 使用 Laravel Pint 进行代码格式化
- 控制器方法需要添加注释说明

### 测试
```bash
# 运行测试
php artisan test

# 代码格式检查
./vendor/bin/pint
```

## 部署

### 生产环境部署

1. **优化配置**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **设置权限**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

3. **环境变量**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

## 贡献指南

1. Fork 本项目
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 开启 Pull Request

## 许可证

本项目基于 [MIT License](https://opensource.org/licenses/MIT) 开源。

## 联系方式

如有问题或建议，请通过以下方式联系：

- 项目地址: [GitHub Repository]
- 问题反馈: [Issues]

---

**注意**: 请确保在生产环境中正确配置数据库连接和安全设置。
