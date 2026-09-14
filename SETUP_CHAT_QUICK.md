# Setup Nhanh - Giao Diện Chat Zalo

## Bước 1: Chuẩn Bị

### Kiểm tra Requirements
- Laravel 8+ đã cài đặt
- PHP 8.0+
- Database đã setup
- Model CrmMessage và User đã có sẵn

### Tạo Storage Link
```bash
php artisan storage:link
```

## Bước 2: Tạo Demo Data

### Chạy Command Tạo Demo Data
```bash
php artisan chat:create-demo-data
```

Command này sẽ tạo:
- 5 demo users với password `demo123`
- Các cuộc trò chuyện mẫu với tin nhắn

### Demo Users
- **Nguyễn Văn An** - an@demo.com
- **Trần Thị Bình** - binh@demo.com  
- **Lê Văn Cường** - cuong@demo.com
- **Phạm Thị Dung** - dung@demo.com
- **Hoàng Văn Em** - em@demo.com

## Bước 3: Test Giao Diện

### 1. Đăng Nhập
- Truy cập trang login của hệ thống
- Đăng nhập bằng một trong các tài khoản demo ở trên
- Password: `demo123`

### 2. Truy Cập Chat
- Sau khi đăng nhập, truy cập: `/chat`
- Bạn sẽ thấy giao diện chat giống Zalo

### 3. Test Các Tính Năng

#### Xem Conversations
- Sidebar bên trái hiển thị danh sách cuộc trò chuyện
- Click vào conversation để xem tin nhắn

#### Gửi Tin Nhắn
- Nhập tin nhắn trong ô input
- Nhấn Enter hoặc click nút gửi
- Tin nhắn hiển thị ngay lập tức

#### Bắt Đầu Chat Mới
- Click nút "+" trong sidebar
- Tìm kiếm user khác
- Click để bắt đầu conversation

#### Upload File
- Click icon paperclip để upload file
- Click icon image để upload hình ảnh
- File sẽ hiển thị trong chat

## Bước 4: Customization (Tùy Chọn)

### Thay Đổi Avatar Mặc Định
1. Thay thế file `public/tpl_modernize/assets/images/svgs/icon-user-male.svg` bằng ảnh thật
2. Kích thước khuyến nghị: 128x128 pixels

### Thay Đổi Màu Sắc
Chỉnh sửa CSS variables trong `resources/views/chat/layout.blade.php`:
```css
:root {
    --zalo-primary: #0068ff;        /* Màu chính */
    --zalo-secondary: #f0f2f5;      /* Màu nền */
    --zalo-message-sent: #0068ff;   /* Tin nhắn đã gửi */
    --zalo-message-received: #e4e6ea; /* Tin nhắn nhận */
}
```

## Bước 5: Production Setup

### 1. Database Indexes
Thêm indexes để tối ưu performance:
```sql
-- Indexes cho CrmMessage table
CREATE INDEX idx_crm_messages_thread_id ON crm_messages(thread_id);
CREATE INDEX idx_crm_messages_uid_from ON crm_messages(uid_from);
CREATE INDEX idx_crm_messages_id_to ON crm_messages(id_to);
CREATE INDEX idx_crm_messages_created_at ON crm_messages(created_at);
```

### 2. File Permissions
```bash
chmod -R 775 storage/
chmod -R 775 public/storage/
```

### 3. Cache Config
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

### Lỗi Thường Gặp

#### 1. "Class 'ChatController' not found"
```bash
composer dump-autoload
```

#### 2. "Storage link not found"
```bash
php artisan storage:link
```

#### 3. "CSRF token mismatch"
- Kiểm tra meta tag csrf-token trong layout
- Clear browser cache

#### 4. "Permission denied" khi upload file
```bash
chmod -R 775 storage/app/public/
```

### Debug Mode
Để debug, thêm vào `.env`:
```
APP_DEBUG=true
LOG_LEVEL=debug
```

## URLs Quan Trọng

- **Chat chính**: `/chat`
- **API messages**: `/api/chat/messages`
- **API send**: `/api/chat/send`
- **API search users**: `/api/chat/search-users`

## Next Steps

### Tính Năng Nâng Cao
1. **Real-time với WebSocket**
   - Cài đặt Laravel Echo + Pusher
   - Implement broadcasting cho tin nhắn real-time

2. **Emoji & Stickers**
   - Thêm emoji picker
   - Upload và quản lý stickers

3. **Group Chat**
   - Mở rộng để hỗ trợ group chat
   - Quản lý members

4. **Voice & Video**
   - Tích hợp WebRTC cho voice/video calls
   - Recording và playback

### Performance Optimization
1. **Caching**
   - Cache conversation lists
   - Cache user information
   - Use Redis for sessions

2. **CDN**
   - Upload files lên CDN
   - Optimize image delivery

3. **Database**
   - Partition large tables
   - Archive old messages

---

**🎉 Chúc mừng! Bạn đã setup thành công giao diện chat giống Zalo!**

Nếu gặp vấn đề, vui lòng kiểm tra:
1. Laravel logs: `storage/logs/laravel.log`
2. Browser console cho JavaScript errors
3. Network tab để kiểm tra API calls 
