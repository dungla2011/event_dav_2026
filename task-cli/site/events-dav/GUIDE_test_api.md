# 🧪 Hướng dẫn Test Face API

## 📋 Tổng quan

Dự án này cung cấp Face API với 3 endpoints chính:
- `POST /get_face_vector` - Lấy vector khuôn mặt từ URL ảnh
- `POST /detect_face` - Nhận diện khuôn mặt từ file upload
- `POST /update_face` - Cập nhật cache khuôn mặt

## 🚀 Cách chạy test

### 1. Chuẩn bị môi trường

```bash
# Kích hoạt virtual environment
venv\Scripts\Activate.ps1

# Cài đặt dependencies (nếu chưa có)
pip install -r requirements.txt

# Chạy Face API server
python face_api.py
```

### 2. Chạy test bằng Python

#### Test đầy đủ (test_face_api.py):
```bash
# Chạy tất cả test case
python test_face_api.py
```

#### Test đơn giản (simple_test.py):
```bash
# Chạy test cơ bản
python simple_test.py
```

### 3. Chạy test bằng PowerShell

```powershell
# Chạy test với PowerShell
.\test_curl.ps1
```

## 📁 Mô tả các file test

### 1. `test_face_api.py`
- **Mục đích**: Test toàn diện tất cả API endpoints
- **Tính năng**:
  - Test tất cả edge cases
  - Test với data hợp lệ và không hợp lệ
  - Test workflow đầy đủ
  - Tự động tạo ảnh test
  - Cleanup tự động

### 2. `simple_test.py`
- **Mục đích**: Test cơ bản, dễ hiểu
- **Tính năng**:
  - Test từng API một cách đơn giản
  - Output rõ ràng với emoji
  - Dễ customize cho test riêng

### 3. `test_curl.ps1`
- **Mục đích**: Test bằng PowerShell/REST API
- **Tính năng**:
  - Không cần Python để chạy test
  - Sử dụng Invoke-RestMethod
  - Test trực tiếp HTTP requests

## 🔧 API Documentation

### 1. GET_FACE_VECTOR
```http
POST /get_face_vector
Content-Type: application/json

{
  "image_link": "https://example.com/image.jpg"
}
```

**Response thành công:**
```json
{
  "status": "success",
  "vector": [0.1, 0.2, ..., 0.5]  // 512 chiều
}
```

**Response lỗi:**
```json
{
  "status": "fail",
  "vector": [],
  "error": "No face detected"
}
```

### 2. DETECT_FACE
```http
POST /detect_face
Content-Type: multipart/form-data

file: <image_file>
```

**Response thành công:**
```json
{
  "status": "success",
  "data": {
    "id": "user123",
    "url_confirm": "https://example.com/confirm"
  }
}
```

**Response lỗi:**
```json
{
  "status": "fail",
  "data": null,
  "error": "No face detected"
}
```

### 3. UPDATE_FACE
```http
POST /update_face
Content-Type: application/json

{
  "face_array": [
    {
      "id": "user1",
      "name": "Nguyen Van A",
      "face": [0.1, 0.2, ..., 0.5],  // 512 chiều
      "url_confirm": "https://example.com/confirm1"
    }
  ]
}
```

**Response:**
```json
{
  "status": "success"
}
```

## 🎯 Test Cases

### Test Cases cho GET_FACE_VECTOR:
1. ✅ URL ảnh hợp lệ có khuôn mặt
2. ❌ URL ảnh không tồn tại
3. ❌ Thiếu parameter image_link
4. ❌ Ảnh không có khuôn mặt

### Test Cases cho DETECT_FACE:
1. ✅ Upload file ảnh hợp lệ
2. ❌ Không có file upload
3. ❌ File không phải ảnh
4. ❌ Ảnh không có khuôn mặt
5. ✅ Nhận diện thành công với cache có data

### Test Cases cho UPDATE_FACE:
1. ✅ face_array hợp lệ
2. ❌ face_array thiếu trường bắt buộc
3. ❌ face vector không đúng 512 chiều
4. ❌ face_array không phải array
5. ❌ face_array rỗng

## 🚨 Troubleshooting

### Server không chạy
```bash
# Kiểm tra port 5000 có bị chiếm không
netstat -an | findstr :5000

# Chạy lại server
python face_api.py
```

### Lỗi Import
```bash
# Cài đặt lại dependencies
pip install -r requirements.txt

# Kiểm tra virtual environment
venv\Scripts\Activate.ps1
```

### Lỗi ONNX Runtime
```bash
# Cài đặt ONNX Runtime
pip install onnxruntime
```

## 📊 Kết quả mong đợi

### Khi server chạy bình thường:
- `GET_FACE_VECTOR`: Trả về vector 512 chiều
- `UPDATE_FACE`: Cập nhật cache thành công
- `DETECT_FACE`: Nhận diện dựa trên cache

### Khi chưa có cache:
- `DETECT_FACE`: Trả về "Cache is empty"

### Khi không có khuôn mặt:
- Tất cả API: Trả về "No face detected"

## 🔄 Workflow test đầy đủ

1. **Khởi động server**: `python face_api.py`
2. **Cập nhật cache**: POST `/update_face`
3. **Test lấy vector**: POST `/get_face_vector`
4. **Test nhận diện**: POST `/detect_face`
5. **Kiểm tra kết quả**: So sánh với mong đợi

## 💡 Tips

- Luôn chạy server trước khi test
- Sử dụng ảnh có khuôn mặt rõ ràng để test
- Kiểm tra log server để debug
- Test từng API riêng lẻ trước khi test workflow
- Sử dụng Postman để test thủ công nếu cần 