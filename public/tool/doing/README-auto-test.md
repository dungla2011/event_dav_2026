# SVG Export Auto Test

## Mô tả

Hệ thống auto test để kiểm tra tính năng export SVG có khớp với SVG gốc hay không. Test sẽ so sánh vị trí các text elements giữa display SVG và export SVG.

## Test Cases

### Các trường hợp được test:

1. **Horizontal Text**
   - Name only
   - Name + Title + Birthday
   - With/without images
   - Different alignments (left, center, right)

2. **Vertical Text** 
   - Name only
   - Name + Title + Birthday
   - With/without images
   - Different alignments (left, center, right)

3. **Mixed Configurations**
   - Different text offsets
   - Different node dimensions
   - Various image positions

## Cài đặt

### 1. Cài đặt dependencies

Chạy file batch:
```cmd
setup-test.bat
```

Hoặc chạy manual:
```cmd
npm install puppeteer
```

### 2. Chạy tests

#### Option 1: Sử dụng batch file
```cmd
run-test.bat
```

#### Option 2: Chạy trực tiếp

**Quick Test (2-3 cases):**
```cmd
node quick-test-export.js
```

**Full Test (tất cả cases):**
```cmd
node test-export-svg.js
```

**Single Test (debug 1 case):**
```cmd
node test-single.js
```

## Output

### Console Output
- Real-time progress
- Pass/Fail status for each test
- Summary statistics

### Files Generated
- `test-report-{timestamp}.json` - Detailed JSON report
- `test-summary-{timestamp}.txt` - Human-readable summary
- `debug-result-{timestamp}.json` - Single test debug info

## Cách hoạt động

1. **Initialize**: Mở browser với Puppeteer, load doing.html
2. **Apply Config**: Set các option (showTitle, textRotation, alignment, etc.)
3. **Extract Display Positions**: Lấy vị trí text từ display SVG
4. **Export**: Trigger export function
5. **Extract Export Positions**: Lấy vị trí text từ exported SVG
6. **Compare**: So sánh vị trí với tolerance 1px
7. **Report**: Generate report với pass/fail status

## Test Configuration

Mỗi test case bao gồm:

```javascript
{
    name: "test-name",
    config: {
        showTitle: true/false,
        showBirthday: true/false,
        showImage: true/false,
        showDeathDate: true/false,
        textRotation: "horizontal|vertical-bottom|vertical-top",
        textAlignment: "left|center|right", 
        nodeWidth: number,
        nodeHeight: number,
        textAlignmentOffset: number,
        textAlignmentOffsetY: number,
        imagePosition: "left|right|top|bottom"
    }
}
```

## Thêm Test Case mới

Edit file `test-export-svg.js`, thêm vào array `testCases`:

```javascript
{
    name: "my-new-test",
    config: {
        // your config here
    }
}
```

## Troubleshooting

### Browser không mở được
- Kiểm tra Puppeteer đã cài đúng chưa
- Thử set `headless: true` trong code

### Test fail do timeout
- Tăng `waitForTimeout` values
- Kiểm tra data.json có load được không

### Positions không match
- Check console log để xem exact coordinates
- Kiểm tra export function có lỗi không  
- Verify các margin/offset calculations

## Scripts trong package.json

```json
{
  "scripts": {
    "test": "node test-export-svg.js",
    "test:quick": "node quick-test-export.js", 
    "test:single": "node test-single.js"
  }
}
```

## Dependencies

- **puppeteer**: Browser automation
- **Node.js**: Runtime environment

## Notes

- Test chỉ so sánh 3 nodes đầu tiên để tránh quá nhiều data
- Tolerance mặc định là 1px (có thể adjust)
- Browser sẽ mở ở mode visible để debug dễ hơn
- Exported SVG được inject vào DOM để test có thể access
