# Fix Lỗi PHP API: AttributeError: 'NoneType' object has no attribute 'get'

## Nguyên nhân lỗi

Lỗi này xảy ra do sự khác biệt trong cách gửi dữ liệu từ PHP và cách Flask nhận dữ liệu:

1. **PHP Code gửi form-data:**
```php
$postData = [
    'image_link' => 'https://events.dav.edu.vn/test_cloud_file?fid=4866',
];
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
```

2. **Flask Code ban đầu chỉ nhận JSON:**
```python
data = request.get_json()  # Trả về None nếu không phải JSON
image_link = data.get('image_link')  # Lỗi: None.get()
```

## Giải pháp

### 1. Sửa Flask Code (Đã được sửa)

Sửa các route để có thể nhận cả JSON và form-data:

```python
@app.route('/get_face_vector', methods=['POST'])
def get_face_vector():
    # Nhận dữ liệu từ JSON hoặc form-data
    data = request.get_json()
    if data is None:
        # Nếu không phải JSON, thử lấy từ form-data
        data = request.form.to_dict()
    
    image_link = data.get('image_link')
    # ... rest of code
```

### 2. Thêm Debug Logging

Thêm function debug để dễ troubleshoot:

```python
def log_request_info(endpoint_name):
    """Log request information for debugging"""
    content_type = request.headers.get('Content-Type', '')
    print(f"🔍 [{endpoint_name}] Content-Type: {content_type}")
    
    if request.is_json:
        print(f"🔍 [{endpoint_name}] JSON data: {request.get_json()}")
    else:
        print(f"🔍 [{endpoint_name}] Form data: {request.form.to_dict()}")
```

### 3. Cách gửi dữ liệu từ PHP

#### Option 1: Gửi form-data (Như hiện tại)
```php
$postData = [
    'image_link' => 'https://events.dav.edu.vn/test_cloud_file?fid=4866',
];
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
```

#### Option 2: Gửi JSON
```php
$postData = json_encode([
    'image_link' => 'https://events.dav.edu.vn/test_cloud_file?fid=4866',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($postData)
]);
```

## Test Cases

### Test với PHP
```bash
php test_php_api.php
```

### Test với PowerShell
```powershell
.\test_api_powershell.ps1
```

### Test với cURL
```bash
# Form-data
curl -X POST http://localhost:50000/get_face_vector \
  -d "image_link=https://events.dav.edu.vn/test_cloud_file?fid=4866"

# JSON
curl -X POST http://localhost:50000/get_face_vector \
  -H "Content-Type: application/json" \
  -d '{"image_link":"https://events.dav.edu.vn/test_cloud_file?fid=4866"}'
```

## Các Routes đã được sửa

1. `/get_face_vector` - Lấy face vector từ image URL
2. `/update_face` - Cập nhật face cache
3. `/reload_face_cache` - Reload cache từ server
4. `/detect_face` - Nhận diện khuôn mặt từ file upload

## Port Configuration

API hiện tại chạy trên port 50000 (đã thay đổi từ 8080 để phù hợp với PHP code).

Để thay đổi port:
```bash
$env:FLASK_PORT=3000 ; python face_api.py
```

## Troubleshooting

1. **Kiểm tra port có đang chạy:**
```bash
netstat -an | findstr :50000
```

2. **Kiểm tra log của Flask server để xem debug info**

3. **Test các endpoint đơn giản trước:**
```bash
curl http://localhost:50000/cache_status
``` 