# Hướng dẫn cài đặt reCAPTCHA cho form đăng ký

## ✅ Đã hoàn thành:

### 1. Backend - Controller đã được cập nhật
- ✅ Thêm method `verifyRecaptcha()` trong `LoginController.php`
- ✅ Tích hợp validation reCAPTCHA vào hàm `register()`
- ✅ Sử dụng config `recaptcha.api_secret_key` và `recaptcha.score_threshold`

## 📋 Các bước tiếp theo:

### 2. Lấy reCAPTCHA Keys từ Google

1. Truy cập: https://www.google.com/recaptcha/admin
2. Đăng nhập Google Account
3. Nhấn **"+"** để tạo site mới
4. Điền thông tin:
   - **Label**: Tên dự án của bạn (ví dụ: "My Laravel App")
   - **reCAPTCHA type**: 
     - **v2 Checkbox**: Hiện ô checkbox "I'm not a robot" (dễ implement)
     - **v3**: Invisible, dựa trên score (phức tạp hơn, trải nghiệm người dùng tốt hơn)
   - **Domains**: Thêm domain của bạn (ví dụ: `example.com`, `localhost` cho dev)
5. Nhấn **Submit**
6. Copy **Site Key** và **Secret Key**

### 3. Cập nhật file `.env`

Thêm vào file `.env`:

```env
# Google reCAPTCHA
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here
RECAPTCHA_VERSION=v2
# Hoặc v3 nếu dùng reCAPTCHA v3
```

### 4. Frontend - Thêm reCAPTCHA vào Blade View

Tìm file view register (thường là `resources/views/login/register.blade.php`):

#### **Cho reCAPTCHA v2 (Checkbox):**

```blade
<!-- Trong <head> -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- Trong form, trước nút Submit -->
<div class="form-group">
    <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.api_site_key') }}"></div>
    @error('recaptcha')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<button type="submit" class="btn btn-primary">Đăng ký</button>
```

#### **Cho reCAPTCHA v3 (Invisible):**

```blade
<!-- Trong <head> -->
<script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.api_site_key') }}"></script>

<script>
document.getElementById('register-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    grecaptcha.ready(function() {
        grecaptcha.execute('{{ config('recaptcha.api_site_key') }}', {action: 'register'})
            .then(function(token) {
                // Add token to form
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'g-recaptcha-response';
                input.value = token;
                document.getElementById('register-form').appendChild(input);
                
                // Submit form
                document.getElementById('register-form').submit();
            });
    });
});
</script>

<!-- Form với id -->
<form id="register-form" method="POST" action="/register">
    @csrf
    
    <!-- Các field khác -->
    
    @error('recaptcha')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    
    <button type="submit" class="btn btn-primary">Đăng ký</button>
</form>
```

### 5. Cập nhật file config/recaptcha.php (đã có sẵn)

File này đã tồn tại, chỉ cần thêm score_threshold nếu chưa có:

```php
// Thêm vào file config/recaptcha.php nếu chưa có
'score_threshold' => env('RECAPTCHA_SCORE_THRESHOLD', 0.5),
```

### 6. Test

1. **Kiểm tra .env**: Đảm bảo `RECAPTCHA_SITE_KEY` và `RECAPTCHA_SECRET_KEY` đã được thiết lập
2. **Clear cache**: 
   ```bash
   php artisan config:cache
   php artisan cache:clear
   ```
3. **Truy cập form đăng ký** và test:
   - Với v2: Checkbox sẽ hiển thị
   - Với v3: Invisible, tự động chạy khi submit

4. **Test validation**:
   - Submit form mà không check reCAPTCHA → Sẽ báo lỗi
   - Submit form sau khi check → Sẽ đăng ký thành công

## 🔧 Troubleshooting

### Lỗi "reCAPTCHA secret key not configured"
- Kiểm tra `.env` có `RECAPTCHA_SECRET_KEY` chưa
- Chạy `php artisan config:cache`

### Lỗi "Invalid domain for site key"
- Thêm domain vào reCAPTCHA admin console
- Với local development, thêm `localhost` hoặc `127.0.0.1`

### reCAPTCHA không hiển thị
- Kiểm tra `RECAPTCHA_SITE_KEY` trong `.env`
- Kiểm tra network tab trong browser để xem có load được script không
- Kiểm tra console browser có lỗi JavaScript không

## 📊 Cấu trúc code đã implement

```
LoginController.php
├── register()
│   ├── verifyRecaptcha() ← Kiểm tra reCAPTCHA
│   ├── Validation khác
│   └── Create user
│
└── verifyRecaptcha($recaptchaResponse)
    ├── Kiểm tra empty
    ├── Call Google API
    ├── Check success
    └── Check score (nếu v3)
```

## 🎯 Các tham số có thể điều chỉnh

1. **Score threshold** (v3 only): Trong file `.env`
   ```env
   RECAPTCHA_SCORE_THRESHOLD=0.5
   ```
   - 0.0 - 0.3: Có thể là bot
   - 0.3 - 0.7: Nghi ngờ
   - 0.7 - 1.0: Có thể là người thật

2. **Timeout**: Trong `config/recaptcha.php`
   ```php
   'curl_timeout' => 10, // seconds
   ```

## 📚 Tài liệu tham khảo

- reCAPTCHA Admin: https://www.google.com/recaptcha/admin
- reCAPTCHA Docs: https://developers.google.com/recaptcha/docs/display
- Laravel HTTP Client: https://laravel.com/docs/http-client

## ✨ Lợi ích

✅ Chống spam/bot đăng ký tài khoản
✅ Bảo vệ form khỏi automated attacks
✅ Dễ dàng tích hợp và cấu hình
✅ Hoàn toàn miễn phí (Google reCAPTCHA)
