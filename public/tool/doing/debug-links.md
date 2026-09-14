# Text Rotation Fix cho Draw.io Export

## Thay đổi đã thực hiện:

✅ **Text Centering với relative spacing:**

- **Horizontal text**: Căn giữa theo X, giữ nguyên Y position để duy trì spacing
- **Vertical text**: Căn giữa theo X, giữ nguyên Y position tương đối

```javascript
// Tất cả text đều căn giữa theo X
finalTextX = nodeX + (settings.width / 2) - (geometryWidth / 2);

// Giữ nguyên Y position để duy trì spacing tương đối
finalTextY = originalTextY; // No override, keeps name -> title -> birthday -> death spacing
```

✅ **Text Rotation và Geometry:**

1. **Rectangle nodes**: `horizontal=0` khi textRotation là vertical
2. **Multiple text cells**: Name, title, birthday, death - mỗi cái có định dạng riêng
3. **Geometry swap**: Width ↔ Height khi text vertical
4. **Relative positioning**: Y spacing được giữ nguyên

## Logic hoàn chỉnh:

```javascript
// 1. Tính toán Y position theo thứ tự (như cũ)
let nameTextY = nodeY + (settings.height / 2) - 15;
let titleTextY = nameTextY + levelFontStyles.nameSize + 8;
let birthdayTextY = titleTextY + (hasTitle ? levelFontStyles.titleSize + 8 : 0);
let deathTextY = birthdayTextY + (hasBirthday ? levelFontStyles.birthdaySize + 8 : 0);

// 2. Căn giữa tất cả text theo X
finalTextX = nodeX + (settings.width / 2) - (geometryWidth / 2);
finalTextY = originalTextY; // Giữ Y spacing tương đối

// 3. Swap geometry nếu vertical
if (textRotation === 'vertical-top' || textRotation === 'vertical-bottom') {
    geometryWidth = fontSize + padding;
    geometryHeight = originalTextWidth;
    textHorizontalStyle = ";horizontal=0";
}
```

## Kết quả:

- ✅ **Text được căn giữa** theo horizontal trong rectangles
- ✅ **Spacing tương đối** giữa name, title, birthday, death được giữ nguyên
- ✅ **Không bị đè lên nhau** - mỗi text có Y position riêng
- ✅ **Text rotation** hoạt động đúng với geometry phù hợp
- ✅ **Multiple formats** - mỗi loại text có font, size, color riêng

## Test:

1. Mở `doing.html`
2. Set text rotation cho level cuối về 'vertical-top' hoặc 'vertical-bottom'  
3. Export Draw.io
4. Kiểm tra:
   - Text căn giữa trong rectangles ✓
   - Spacing giữa các text đều nhau ✓
   - Không bị đè lên nhau ✓

1. Mở file `doing.html`
2. Load family tree data
3. Export Draw.io để kiểm tra:
   - Text có xoay vertical ở bottom row ✓
   - Links giữa các nodes có hiển thị không?
   - Marriage links có hiển thị không?

## Console logs để kiểm tra:

- `✓ Node X/Y: Name (ID: Z)` - nodes được tạo với ID đúng
- `✓ Marriage link X/Y: Name1 ♥ Name2` - marriage links được tạo
- `⚠️ Missing cell ID for link` - nếu vẫn có lỗi này thì cần debug thêm
