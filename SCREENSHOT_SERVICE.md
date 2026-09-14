# Screenshot Service - Server-side DOM to Image

Giải pháp render DOM to Image ở server-side sử dụng Puppeteer, không giới hạn pixel size như browser.

## 🚀 Cài đặt

### Bước 1: Cài đặt dependencies

```bash
# Copy package.json
cp package-screenshot.json package.json

# Install
npm install
```

### Bước 2: Chạy service

```bash
# Production
npm start

# Development (auto-reload)
npm run dev
```

Service sẽ chạy tại: `http://localhost:3000`

## 📖 Sử dụng

### Client-side (JavaScript)

#### Cách 1: Drop-in replacement (không cần sửa code hiện tại)

```html
<!-- Thay vì dom-to-image -->
<!-- <script src="dom-to-image.js"></script> -->

<!-- Dùng screenshot-client -->
<script src="/js/screenshot-client.js"></script>

<script>
// Code cũ vẫn chạy như bình thường!
domtoimage.toPng(document.getElementById('my-element'))
    .then(function(dataUrl) {
        console.log('Image generated:', dataUrl);
    });
</script>
```

#### Cách 2: Dùng ScreenshotClient class

```javascript
const client = new ScreenshotClient({
    serviceUrl: 'http://localhost:3000',
    scale: 2,  // Retina quality
    format: 'png'
});

// Capture to PNG
client.toPng(element, { width: 1920, height: 1080 })
    .then(dataUrl => {
        // Download
        const link = document.createElement('a');
        link.download = 'screenshot.png';
        link.href = dataUrl;
        link.click();
    });

// Capture to JPEG
client.toJpeg(element, { quality: 90 })
    .then(dataUrl => { ... });

// Capture to Blob
client.toBlob(element)
    .then(blob => {
        // Upload to server
        const formData = new FormData();
        formData.append('file', blob);
        fetch('/upload', { method: 'POST', body: formData });
    });
```

### Server-side API

#### POST /screenshot

Capture full page hoặc với kích thước cụ thể.

**Request:**
```json
{
  "html": "<html><body><h1>Hello</h1></body></html>",
  "width": 1920,
  "height": 1080,
  "scale": 2,
  "format": "png",
  "quality": 90,
  "fullPage": true,
  "backgroundColor": "#ffffff"
}
```

**Response:** Binary image data (PNG/JPEG)

**Example với cURL:**
```bash
curl -X POST http://localhost:3000/screenshot \
  -H "Content-Type: application/json" \
  -d '{
    "html": "<html><body><div style=\"width:1000px;height:500px;background:red;\">Test</div></body></html>",
    "width": 1000,
    "height": 500,
    "format": "png"
  }' \
  --output screenshot.png
```

#### POST /screenshot-element

Capture một element cụ thể bằng CSS selector.

**Request:**
```json
{
  "html": "<html><body><div id=\"target\">Hello</div></body></html>",
  "selector": "#target",
  "scale": 2,
  "format": "png"
}
```

#### GET /health

Health check endpoint.

```bash
curl http://localhost:3000/health
```

## 🔧 Tích hợp vào code hiện tại

### File: `clsTreeTopDown_src_glx.001.js`

**Before (dòng 2257-2325):**
```javascript
domtoimage.toPng(this.divTest, {
    quality: 1,
    width: 2000,
    height: 3000
}).then(function(dataUrl) {
    // ... existing code
});
```

**After (chỉ cần thêm script):**
```html
<!-- Thêm vào HTML head -->
<script src="/js/screenshot-client.js"></script>
```

**Không cần sửa code JavaScript!** Screenshot-client tự động override `domtoimage`.

## ⚙️ Options

### Client Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `serviceUrl` | string | `http://localhost:3000` | Screenshot service URL |
| `scale` | number | `1` | Device scale factor (2 = retina) |
| `format` | string | `png` | Image format: 'png' or 'jpeg' |
| `quality` | number | `90` | JPEG quality (0-100) |
| `fullPage` | boolean | `true` | Capture full page or viewport only |
| `width` | number | auto | Force width (px) |
| `height` | number | auto | Force height (px) |
| `backgroundColor` | string | `#ffffff` | Background color |

## 🎯 So sánh với dom-to-image

| Feature | dom-to-image (client) | Screenshot Service |
|---------|----------------------|-------------------|
| Max pixel size | ⚠️ Giới hạn browser (~16384px) | ✅ Không giới hạn |
| Performance | ⚠️ Chậm với DOM lớn | ✅ Nhanh hơn (Chrome engine) |
| Memory usage | ⚠️ Browser crash nếu quá lớn | ✅ Server-side handling |
| Accuracy | ⚠️ Thiếu styles đôi khi | ✅ Render chính xác 100% |
| Dependencies | ❌ External library | ✅ Native Puppeteer |

## 🐛 Troubleshooting

### Lỗi: "ECONNREFUSED" hoặc "Failed to fetch"

**Nguyên nhân:** Screenshot service chưa chạy.

**Giải pháp:**
```bash
npm start
```

### Lỗi: "HTML content is required"

**Nguyên nhân:** Element không có nội dung.

**Giải pháp:** Kiểm tra element tồn tại và có HTML.

### Lỗi: Canvas size exceeded

**Nguyên nhân:** Kích thước quá lớn ngay cả với Puppeteer.

**Giải pháp:** Giảm `scale` hoặc split thành nhiều phần:
```javascript
// Split large element into chunks
const chunks = splitElement(largeElement, 5000); // 5000px per chunk
for (const chunk of chunks) {
    await client.toPng(chunk);
}
```

## 📦 Production Deployment

### Sử dụng PM2

```bash
# Install PM2
npm install -g pm2

# Start service
pm2 start task-cli/screenshot-service.js --name screenshot-service

# Auto-start on boot
pm2 startup
pm2 save
```

### Docker

```dockerfile
FROM node:18

RUN apt-get update && apt-get install -y \
    chromium \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY package.json ./
RUN npm install

COPY . .

ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium

EXPOSE 3000
CMD ["npm", "start"]
```

## 🔒 Security

### CORS

Mặc định service cho phép tất cả origins. Production nên giới hạn:

```javascript
// task-cli/screenshot-service.js
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', 'https://yourdomain.com');
    // ...
});
```

### Rate Limiting

Thêm rate limiting để tránh abuse:

```bash
npm install express-rate-limit
```

```javascript
const rateLimit = require('express-rate-limit');

const limiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 100 // limit each IP to 100 requests per windowMs
});

app.use('/screenshot', limiter);
```

## 📊 Performance Tips

1. **Reuse browser instance** - Đã implemented (single browser instance)
2. **Close pages** - Đã implemented (auto-close sau screenshot)
3. **Limit concurrent requests** - Thêm queue nếu cần:

```javascript
const PQueue = require('p-queue');
const queue = new PQueue({ concurrency: 5 });

app.post('/screenshot', async (req, res) => {
    await queue.add(() => handleScreenshot(req, res));
});
```

4. **Cache results** - Cache HTML hash → image:

```javascript
const crypto = require('crypto');
const cache = new Map();

const hash = crypto.createHash('md5').update(html).digest('hex');
if (cache.has(hash)) {
    return res.send(cache.get(hash));
}
```

## 📝 License

MIT
