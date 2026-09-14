# Giao Diện Chat Giống Zalo PC/Web

## Tổng Quan

Đây là một hệ thống chat real-time với giao diện giống Zalo PC/Web được xây dựng trên Laravel, sử dụng các API CrmMessage có sẵn.

## Tính Năng

### 🎨 Giao Diện
- **Thiết kế giống Zalo PC/Web**: Layout 2 cột với sidebar conversations và main chat area
- **Responsive**: Tương thích với mobile và desktop
- **Theme màu Zalo**: Sử dụng color scheme chính thức của Zalo
- **Smooth animations**: Hiệu ứng mượt mà khi chuyển đổi

### 💬 Chat Features
- **Real-time messaging**: Gửi và nhận tin nhắn real-time
- **File sharing**: Upload và chia sẻ file, hình ảnh
- **Message status**: Hiển thị trạng thái tin nhắn (đã gửi, đã đọc)
- **Conversation management**: Quản lý danh sách cuộc trò chuyện
- **Search users**: Tìm kiếm và bắt đầu chat với users khác
- **Message history**: Lịch sử tin nhắn với pagination
- **Auto-refresh**: Tự động cập nhật tin nhắn mới

### 🔍 Tìm Kiếm
- **Search conversations**: Tìm kiếm trong danh sách cuộc trò chuyện
- **Search users**: Tìm kiếm users để bắt đầu chat mới
- **Real-time search**: Kết quả tìm kiếm hiển thị ngay lập tức

## Cấu Trúc Files

```
app/Http/Controllers/
├── ChatController.php              # Controller chính cho chat

resources/views/chat/
├── layout.blade.php               # Layout chính với CSS/JS
├── index.blade.php               # Trang danh sách conversations
└── conversation.blade.php        # Trang chat chi tiết

routes/
└── web_chat.php                  # Routes cho chat system
```

## API Endpoints

### Web Routes
- `GET /chat` - Trang chính hiển thị danh sách conversations
- `GET /chat/conversation/{thread_id}` - Trang chat với conversation cụ thể

### API Routes
- `GET /api/chat/messages` - Lấy tin nhắn của conversation
- `POST /api/chat/send` - Gửi tin nhắn mới
- `GET /api/chat/search-users` - Tìm kiếm users
- `POST /api/chat/start-conversation` - Bắt đầu conversation mới
- `POST /api/chat/mark-read` - Đánh dấu tin nhắn đã đọc
- `POST /api/chat/upload` - Upload file/hình ảnh

## Cài Đặt

### 1. Database
Hệ thống sử dụng model `CrmMessage` có sẵn với các trường:
- `thread_id`: ID của cuộc trò chuyện
- `content`: Nội dung tin nhắn
- `uid_from`: ID người gửi
- `id_to`: ID người nhận
- `msg_type`: Loại tin nhắn (text, file, image)
- `status`: Trạng thái tin nhắn
- `log`: Thông tin bổ sung (JSON)

### 2. Storage
Tạo symbolic link cho storage:
```bash
php artisan storage:link
```

### 3. Permissions
Đảm bảo thư mục storage có quyền ghi:
```bash
chmod -R 775 storage/
chmod -R 775 public/storage/
```

## Sử Dụng

### 1. Truy Cập Chat
- Đăng nhập vào hệ thống
- Truy cập `/chat` để vào giao diện chat chính

### 2. Bắt Đầu Chat Mới
- Click nút "+" trong sidebar
- Tìm kiếm user muốn chat
- Click vào user để bắt đầu conversation

### 3. Gửi Tin Nhắn
- Nhập tin nhắn trong ô input
- Nhấn Enter hoặc click nút gửi
- Tin nhắn sẽ hiển thị ngay lập tức

### 4. Gửi File/Hình Ảnh
- Click icon paperclip để gửi file
- Click icon image để gửi hình ảnh
- File sẽ được upload và hiển thị trong chat

## Customization

### 1. Thay Đổi Màu Sắc
Chỉnh sửa CSS variables trong `layout.blade.php`:
```css
:root {
    --zalo-primary: #0068ff;        /* Màu chính */
    --zalo-secondary: #f0f2f5;      /* Màu phụ */
    --zalo-message-sent: #0068ff;   /* Màu tin nhắn đã gửi */
    --zalo-message-received: #e4e6ea; /* Màu tin nhắn nhận */
}
```

### 2. Thêm Tính Năng
- Emoji picker
- Voice messages
- Video calls
- Group chat
- Message reactions

### 3. Real-time Updates
Để có real-time updates tốt hơn, có thể tích hợp:
- **Laravel Echo + Pusher**: Cho real-time broadcasting
- **WebSockets**: Cho connection persistent
- **Socket.io**: Cho real-time bidirectional communication

## Troubleshooting

### 1. Tin nhắn không hiển thị
- Kiểm tra authentication
- Kiểm tra permissions cho routes
- Kiểm tra database connection

### 2. File upload không hoạt động
- Kiểm tra storage link: `php artisan storage:link`
- Kiểm tra permissions thư mục storage
- Kiểm tra file size limits trong php.ini

### 3. Search không hoạt động
- Kiểm tra CSRF token
- Kiểm tra JavaScript console cho errors
- Kiểm tra API endpoints

## Performance Tips

### 1. Database Optimization
- Index trên `thread_id`, `uid_from`, `id_to`
- Index trên `created_at` cho sorting
- Pagination cho message history

### 2. Caching
- Cache user information
- Cache conversation lists
- Use Redis for session storage

### 3. File Storage
- Sử dụng CDN cho file storage
- Optimize images trước khi upload
- Implement file cleanup cho old files

## Security

### 1. Authentication
- Tất cả routes đều require authentication
- Kiểm tra permissions trước khi access conversation

### 2. File Upload
- Validate file types và sizes
- Scan files cho malware
- Store files outside web root

### 3. XSS Protection
- Escape HTML trong tin nhắn
- Validate user inputs
- Use CSRF protection

## Browser Support

- **Chrome**: 70+
- **Firefox**: 65+
- **Safari**: 12+
- **Edge**: 79+
- **Mobile browsers**: iOS Safari 12+, Chrome Mobile 70+

## Demo Data

Để test hệ thống, có thể tạo demo data:

```php
// Tạo users demo
$user1 = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password')
]);

$user2 = User::create([
    'name' => 'Jane Smith', 
    'email' => 'jane@example.com',
    'password' => bcrypt('password')
]);

// Tạo conversation demo
$message = new CrmMessage();
$message->thread_id = 'chat_1_2';
$message->content = 'Xin chào!';
$message->uid_from = 1;
$message->id_to = 2;
$message->msg_type = 'text';
$message->status = 'sent';
$message->save();
```

## Liên Hệ

Nếu có vấn đề hoặc cần hỗ trợ, vui lòng tạo issue hoặc liên hệ team phát triển.

---

**Phiên bản**: 1.0.0  
**Ngày cập nhật**: {{ date('d/m/Y') }}  
**Tương thích**: Laravel 8+, PHP 8.0+ 