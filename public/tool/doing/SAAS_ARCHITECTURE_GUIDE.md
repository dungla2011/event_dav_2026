# Family Tree SaaS Architecture Guide
## Phân chia Client-Side và Server-Side để Bảo Mật

---

## 📋 TÓM TẮT PHÂN CHIA

### **PHẦN CÔNG KHAI (Client-Side - JavaScript)**
- UI Rendering (D3.js visualization)
- User interaction (clicks, drags, zooms)
- Local storage management (localStorage)
- Real-time preview

### **PHẦN BẢO MẬT (Server-Side - PHP/Laravel)**
- Data validation & authentication
- Algorithm logic (tree layout, calculations)
- Configuration storage
- Export generation (SVG, PDF, XML)
- File generation & download

---

## 🔒 PHẦN NÊN TÁCH RA THÀNH API SERVER

### **1️⃣ TREE LAYOUT CALCULATION (Tính toán layout cây)**

**Hiện tại:** Client-side (JavaScript)
```javascript
function customTreeLayout(root) { /* Thuật toán layout */ }
function customLinkPath(d) { /* Tính toán đường nối */ }
```

**Nên chuyển sang:** Server-side PHP/API

**Lý do:**
- Bảo vệ thuật toán riêng
- Độc quyền, khó clone
- Tái sử dụng cho nhiều nền tảng

**Cách triển khai:**
```php
// api/family-tree/calculate-layout.php
POST /api/family-tree/calculate-layout
Input:
{
    "tree_data": [...nodes with parent_id],
    "config": {
        "nodeSpacing": 20,
        "levelSpacing": 150,
        "nodeWidth": 160,
        "nodeHeight": 220
    }
}

Output:
{
    "nodes": [
        {
            "id": "123",
            "x": 100,
            "y": 200,
            "depth": 2,
            "parent_id": "456"
        }
    ],
    "links": [
        {
            "source": "456",
            "target": "123",
            "path": "M100,100 Q200,150 300,200"
        }
    ]
}
```

---

### **2️⃣ EXPORT GENERATION (Tạo SVG, PDF, XML)**

**Hiện tại:** Client-side
```javascript
function exportCorelDrawSVGLegacy() { /* Tạo SVG */ }
function createCorelDrawCompatibleSVG() { /* Render SVG */ }
function exportDrawIOXML() { /* Tạo XML */ }
```

**Nên chuyển sang:** Server-side PHP

**Lý do:**
- Mã export lớn, phức tạp (dễ reverse-engineer)
- Xử lý file server-side an toàn hơn
- Có thể generate PDF server-side

**Cách triển khai:**

```php
// api/family-tree/export.php
POST /api/family-tree/export
Input:
{
    "pid": "family_tree_id",
    "format": "svg" | "pdf" | "xml",
    "include_config": {
        "showTitle": true,
        "showBirthday": true,
        "showImage": true,
        "fontSettings": {...}
    }
}

Output:
{
    "download_url": "/downloads/family_tree_123_uuid.svg",
    "expires_at": "2025-10-27T10:00:00Z",
    "file_size": 2048576
}
```

**Hoặc trực tiếp download:**
```
GET /api/family-tree/export/123/svg?token=xyz
// Trả về file SVG ngay lập tức
Content-Type: image/svg+xml
Content-Disposition: attachment; filename="family_tree.svg"
```

---

### **3️⃣ FONT STYLING & VISUALIZATION CONFIG**

**Hiện tại:** Client localStorage
```javascript
localStorage.setItem(`familyTree_globalConfig.${treeId}`, ...)
```

**Nên chuyển sang:** Server Database

**Lý do:**
- Dữ liệu người dùng nên lưu server
- Sync trên nhiều thiết bị
- Tính năng: "Lưu template", "Share config"

**Cách triển khai:**

```php
// api/family-tree/config/save.php
POST /api/family-tree/{pid}/config
Headers: Authorization: Bearer <token>
Body:
{
    "type": "global" | "level",
    "level": 1,  // nếu type = level
    "settings": {
        "nodeWidth": 160,
        "nodeHeight": 220,
        "fontSettings": {...},
        "displayOptions": {...}
    }
}

Response: { "success": true, "config_id": "cfg_123" }

// api/family-tree/config/get.php
GET /api/family-tree/{pid}/config?type=global&level=1
Response:
{
    "config_id": "cfg_123",
    "type": "global",
    "settings": {...},
    "created_at": "2025-10-26T10:00:00Z",
    "updated_at": "2025-10-26T15:30:00Z"
}
```

---

### **4️⃣ DATA VALIDATION & TRANSFORMATION**

**Hiện tại:** Client-side cơ bản
```javascript
function processData() { /* Xử lý dữ liệu */ }
```

**Nên chuyển sang:** Server-side

**Lý do:**
- Validate dữ liệu trước khi lưu
- Detect dữ liệu độc hại
- Normalize format

**Cách triển khai:**

```php
// api/family-tree/validate.php
POST /api/family-tree/{pid}/validate
Body:
{
    "nodes": [...],
    "validation_type": "structure" | "relationships"
}

Response:
{
    "valid": true,
    "errors": [],
    "warnings": [
        "Node 123 có 2 cha"
    ],
    "statistics": {
        "total_nodes": 500,
        "total_marriages": 150,
        "max_depth": 5
    }
}
```

---

### **5️⃣ MARRIAGE LINK DETECTION**

**Hiện tại:** Client-side
```javascript
function detectMarriageLinks(nodes) { /* Logic detect */ }
```

**Nên chuyển sang:** Server-side

**Cách triển khai:**

```php
// api/family-tree/analyze/marriages.php
POST /api/family-tree/{pid}/analyze/marriages
Response:
{
    "marriages": [
        {
            "person_id": "123",
            "spouse_id": "456",
            "children": ["789", "101112"],
            "level": 2
        }
    ],
    "total_marriages": 150,
    "total_children": 500
}
```

---

## 🏗️ KIẾN TRÚC SAAS ĐỀ NGHỊ

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENT BROWSER (Vue/React)              │
│  - D3.js visualization                                      │
│  - User interaction (zoom, pan, click)                     │
│  - Real-time preview (no business logic)                   │
└────────────────────┬────────────────────────────────────────┘
                     │
        ┌────────────┴───────────┐
        │ HTTPS API Calls        │
        ▼                        ▼
┌──────────────────────────────────────────────────────────────┐
│              LARAVEL BACKEND (Server-Side PHP)              │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  Routes:                                                     │
│  - POST /api/family-tree/{pid}/layout             [Controller]
│  - POST /api/family-tree/{pid}/export             [Service]
│  - GET  /api/family-tree/{pid}/config             [Controller]
│  - POST /api/family-tree/{pid}/config/save        [Service]
│  - POST /api/family-tree/{pid}/validate           [Service]
│  - POST /api/family-tree/{pid}/analyze/marriages  [Service]
│                                                              │
│  Services:                                                   │
│  - TreeLayoutService    (Calculate positions)              │
│  - ExportService        (SVG, PDF, XML generation)         │
│  - ConfigService        (Store/load settings)              │
│  - DataValidationService (Validate relationships)          │
│  - AnalysisService      (Detect patterns)                  │
│                                                              │
│  Models:                                                     │
│  - GiaPha               (Family tree)                       │
│  - GiaPhaNhan           (Nodes)                             │
│  - GiaPhaNhanConfig     (Per-level settings)                │
│  - ExportCache          (Cache exported files)              │
│                                                              │
└──────────────────────────────────────────────────────────────┘
        │
        ▼
┌──────────────────────────────────────┐
│      DATABASE (MySQL/PostgreSQL)    │
│  - User configurations              │
│  - Export history                   │
│  - Cached results                   │
└──────────────────────────────────────┘
```

---

## 📦 FILE STRUCTURE ĐỀ NGHỊ

```
app/
├── Http/
│   └── Controllers/
│       └── FamilyTree/
│           ├── LayoutController.php
│           ├── ExportController.php
│           └── ConfigController.php
│
├── Services/
│   ├── FamilyTree/
│   │   ├── TreeLayoutService.php      ⭐ (Core algorithm)
│   │   ├── ExportService.php          ⭐ (SVG/PDF generation)
│   │   ├── SvgGeneratorService.php    (SVG specific)
│   │   ├── PdfGeneratorService.php    (PDF specific via mPDF)
│   │   ├── XmlGeneratorService.php    (XML/Draw.io specific)
│   │   ├── ConfigService.php
│   │   ├── DataValidationService.php
│   │   └── AnalysisService.php
│   │
│   └── Cache/
│       └── TreeCacheService.php
│
├── Models/
│   ├── GiaPha.php
│   ├── GiaPhaNhan.php
│   ├── GiaPhaNhanConfig.php
│   └── FamilyTreeExportCache.php
│
└── Jobs/
    ├── GenerateExportJob.php          (Queue for large exports)
    └── CacheTreeLayoutJob.php         (Pre-calculate layouts)

routes/
└── api.php
    - Route::group(['prefix' => 'family-tree', 'middleware' => 'auth'], ...)

tests/
└── Feature/
    ├── FamilyTreeLayoutTest.php
    └── FamilyTreeExportTest.php
```

---

## 🚀 IMPLEMENTATION ROADMAP

### **Phase 1: Export (HIGH PRIORITY)**
```
✅ Move SVG generation to ExportService
✅ Add PDF export (mPDF + Dompdf)
✅ Implement caching + CDN
```

### **Phase 2: Layout Calculation (MEDIUM PRIORITY)**
```
⏳ Move customTreeLayout to TreeLayoutService
⏳ API endpoint: POST /api/family-tree/{pid}/layout
⏳ Client caches received layout (prevents re-calc)
```

### **Phase 3: Configuration (LOW PRIORITY)**
```
⏳ Move localStorage to API
⏳ Multi-device sync
⏳ Template saving
```

---

## 💾 API EXAMPLES

### **Example 1: Generate SVG Export**
```bash
curl -X POST https://api.mytree.com/api/family-tree/123/export \
  -H "Authorization: Bearer token_xyz" \
  -H "Content-Type: application/json" \
  -d '{
    "format": "svg",
    "include_images": true,
    "include_dates": true,
    "quality": "high"
  }' \
  -o family_tree.svg
```

### **Example 2: Calculate Layout**
```bash
curl -X POST https://api.mytree.com/api/family-tree/123/layout \
  -H "Authorization: Bearer token_xyz" \
  -H "Content-Type: application/json" \
  -d '{
    "config": {
      "nodeSpacing": 20,
      "levelSpacing": 150,
      "orientation": "horizontal"
    }
  }'

# Response:
{
  "nodes": [...],
  "links": [...],
  "hash": "abc123"  // For caching
}
```

### **Example 3: Save Configuration**
```bash
curl -X POST https://api.mytree.com/api/family-tree/123/config \
  -H "Authorization: Bearer token_xyz" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "global",
    "settings": {
      "nodeWidth": 160,
      "nodeBgColor": "#ffffff",
      "fontSettings": {...}
    }
  }'
```

---

## 🔐 SECURITY CONSIDERATIONS

1. **Authentication**: Middleware check `$uid` matches `$pObj->user_id`
2. **Rate Limiting**: Prevent export spam
   ```php
   'exports' => \Illuminate\Cache\RateLimiter::perMinute(10),
   ```
3. **File Validation**: Scan generated files before download
4. **Storage**: Keep exports in `/storage` (outside web root)
5. **Expiration**: Auto-delete exports after 24 hours
6. **IP Whitelist**: Optional for enterprise clients

---

## 📊 PERFORMANCE OPTIMIZATION

### **Caching Strategy**
```php
// Cache layout for 1 hour (invalidate on data change)
Cache::remember("family_tree_{$pid}_layout", now()->addHour(), fn() => 
    (new TreeLayoutService)->calculate($tree)
);

// Queue large exports (>1000 nodes)
if ($tree->nodes()->count() > 1000) {
    GenerateExportJob::dispatch($pid, $format);
} else {
    // Generate immediately
}
```

### **Database Indexes**
```sql
ALTER TABLE gia_pha_nhan ADD INDEX idx_parent_id (parent_id);
ALTER TABLE gia_pha_nhan ADD INDEX idx_gia_pha_id (gia_pha_id);
```

---

## 🎯 BENEFITS

| Aspect | Before | After |
|--------|--------|-------|
| **Security** | Code visible in browser | Protected on server |
| **Scalability** | Limited by client RAM | Server can handle 10K+ nodes |
| **Updates** | Deploy to all users | Update once on server |
| **Features** | Limited to browser | Full backend power (PDF, etc) |
| **Licensing** | Hard to enforce | Per-request licensing |
| **Analytics** | No data | Track all exports, configs |
| **Multi-platform** | Web only | Mobile app, desktop app |

---

## 📝 MIGRATION CHECKLIST

- [ ] Create API routes with authentication
- [ ] Create ExportService (SVG generation)
- [ ] Create TreeLayoutService (Layout calculation)
- [ ] Create ConfigService (Settings storage)
- [ ] Create database migrations for new tables
- [ ] Update JavaScript to call APIs instead of client-side functions
- [ ] Add error handling & retry logic
- [ ] Add rate limiting
- [ ] Add caching layer
- [ ] Add file cleanup jobs
- [ ] Write tests
- [ ] Document API
- [ ] Deploy to production

---

## 🔗 RELATED SERVICES

Similar SaaS implementations:
- **TinyMCE**: Rich text editor → API for persistence
- **Figma**: Design tool → Server-side rendering export
- **Miro**: Whiteboard → Cloud layout calculations
- **Lucidchart**: Diagram tool → PDF/image export APIs

---

**Questions?** Review the specific implementation sections above for your chosen priority.
