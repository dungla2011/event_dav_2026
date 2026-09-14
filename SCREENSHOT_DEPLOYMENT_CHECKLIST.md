# ✅ Screenshot Service Deployment Checklist

## Pre-deployment

- [ ] Server có Node.js >= 14 installed
- [ ] Server có đủ RAM (recommend 2GB+)
- [ ] Port 3000 available (hoặc chọn port khác)
- [ ] User có sudo permissions
- [ ] Laravel application đang chạy bình thường

## Installation Steps

### 1. Upload Files

- [ ] Upload toàn bộ Laravel project lên server
- [ ] Đảm bảo các file mới có mặt:
  - [ ] `task-cli/screenshot-service.js`
  - [ ] `package-screenshot.json`
  - [ ] `app/Http/Controllers/ScreenshotController.php`
  - [ ] `setup-screenshot-production.sh`

### 2. Run Setup Script

```bash
cd /path/to/laravel01
bash setup-screenshot-production.sh
```

- [ ] Script chạy thành công
- [ ] Không có error messages
- [ ] Service đã start

### 3. Verify Installation

```bash
# Check PM2 status
pm2 status
```

- [ ] `screenshot-service` status là `online`
- [ ] Uptime > 0
- [ ] CPU và Memory sử dụng hợp lý

```bash
# Test health endpoint
curl http://localhost:3000/health
```

- [ ] Response có `"status":"ok"`
- [ ] Response có `"browser":"connected"`

```bash
# Test Laravel API
curl https://mytree.vn/api/screenshot/health
```

- [ ] Status code 200
- [ ] Response có `"status":"ok"`

### 4. Configuration

- [ ] `.env` có dòng `SCREENSHOT_SERVICE_URL=http://localhost:3000`
- [ ] Laravel cache cleared (`php artisan config:cache`)
- [ ] Routes cached (`php artisan route:cache`)

### 5. System Dependencies

```bash
# Check Chromium dependencies
ldd $(which chromium-browser) | grep "not found"
```

- [ ] Không có "not found" errors
- [ ] Nếu có, chạy lại dependencies install trong `setup-screenshot-production.sh`

### 6. PM2 Configuration

```bash
# Check auto-start
pm2 list
```

- [ ] Service có trong list
- [ ] Status = `online`

```bash
# Test auto-start (optional)
sudo reboot
# Wait for server restart, then:
pm2 list
```

- [ ] Service tự động start lại sau reboot

### 7. Firewall

```bash
# Check firewall rules
sudo ufw status
# or
sudo iptables -L
```

- [ ] Port 3000 KHÔNG được expose ra internet
- [ ] Chỉ localhost có thể access port 3000

### 8. PHP Configuration

```bash
# Check PHP settings
php -i | grep -E "memory_limit|max_execution_time|upload_max_filesize"
```

- [ ] `memory_limit` >= 256M (recommend 512M)
- [ ] `max_execution_time` >= 60 (recommend 120)
- [ ] `upload_max_filesize` >= 50M

### 9. Nginx Configuration (if applicable)

```bash
# Test Nginx config
sudo nginx -t
```

- [ ] Config syntax OK
- [ ] No errors

```bash
# Reload if needed
sudo systemctl reload nginx
```

## Testing

### Unit Tests

```bash
cd /path/to/laravel01
bash test-screenshot-integration.sh https://mytree.vn
```

- [ ] Test 1: Health check ✅
- [ ] Test 2: Screenshot generation ✅
- [ ] Test 3: PM2 status ✅
- [ ] Test 4: Laravel config ✅

### Manual Tests

#### Test 1: Simple Screenshot

```bash
curl -X POST http://localhost:3000/screenshot \
  -H "Content-Type: application/json" \
  -d '{
    "html": "<html><body><h1>Test</h1></body></html>",
    "width": 800,
    "height": 600,
    "format": "png"
  }' \
  --output /tmp/test.png
```

- [ ] File `/tmp/test.png` được tạo
- [ ] File size > 0
- [ ] File là PNG hợp lệ (`file /tmp/test.png`)

#### Test 2: Laravel API

```bash
# Test từ browser console trên mytree.vn
fetch('/api/screenshot/health')
  .then(r => r.json())
  .then(d => console.log(d))
```

- [ ] Response có `status: "ok"`
- [ ] Không có CORS errors

#### Test 3: Real Tree Export

1. [ ] Truy cập `https://mytree.vn/my-tree?pid=11461493758623744`
2. [ ] Tree hiển thị bình thường
3. [ ] Click nút "Tải xuống"
4. [ ] Toast notification xuất hiện
5. [ ] Loader hiển thị
6. [ ] File PNG download tự động
7. [ ] File PNG có kích thước hợp lý
8. [ ] Mở file PNG, tree hiển thị đúng
9. [ ] Quality tốt (Retina 2x)
10. [ ] Không bị crop/mất phần nào

## Monitoring Setup

### 1. PM2 Monitoring

```bash
# Enable PM2 monitoring
pm2 install pm2-logrotate
```

- [ ] Log rotation enabled
- [ ] Max log size: 10M

```bash
pm2 set pm2-logrotate:max_size 10M
pm2 set pm2-logrotate:retain 30
```

### 2. Log Locations

- [ ] PM2 logs: `~/.pm2/logs/screenshot-service-out.log`
- [ ] PM2 errors: `~/.pm2/logs/screenshot-service-error.log`
- [ ] Laravel logs: `/path/to/laravel01/storage/logs/laravel.log`

### 3. Monitoring Commands

```bash
# Real-time logs
pm2 logs screenshot-service

# Memory usage
pm2 monit

# Process info
pm2 info screenshot-service
```

## Performance Tuning

### Memory Configuration

- [ ] Node.js memory limit set (if needed):
```bash
pm2 delete screenshot-service
pm2 start task-cli/screenshot-service.js \
  --name screenshot-service \
  --node-args="--max-old-space-size=4096"
pm2 save
```

### Concurrency

- [ ] Default: 5 concurrent requests
- [ ] Tăng nếu cần trong `screenshot-service.js`

### Cache

- [ ] Browser reused (single instance)
- [ ] Pages closed sau screenshot

## Security Checklist

- [ ] Service chỉ listen `127.0.0.1:3000` (localhost)
- [ ] Port 3000 không exposed ra internet
- [ ] CSRF token trong Laravel requests
- [ ] Rate limiting enabled (10/min)
- [ ] Input validation (max 20000x20000px)
- [ ] Error messages không leak sensitive info

## Backup

### PM2 Configuration

```bash
# Save PM2 config
pm2 save
# Backup file: ~/.pm2/dump.pm2
```

- [ ] PM2 config backed up

### Dependencies

```bash
# Backup package files
tar -czf screenshot-service-backup.tar.gz \
  package.json \
  package-lock.json \
  task-cli/screenshot-service.js
```

- [ ] Backup created

## Post-deployment

### 24h Monitoring

- [ ] Day 1: Check logs for errors
- [ ] Day 1: Monitor memory usage
- [ ] Day 1: Test with real users

### Week 1 Monitoring

- [ ] Week 1: Check PM2 uptime
- [ ] Week 1: Review error logs
- [ ] Week 1: Adjust memory if needed

### Month 1 Monitoring

- [ ] Month 1: Review performance metrics
- [ ] Month 1: Optimize if needed
- [ ] Month 1: Update documentation

## Rollback Plan

If service fails:

1. [ ] Stop service: `pm2 stop screenshot-service`
2. [ ] Code sẽ tự động fallback về dom-to-image (client-side)
3. [ ] Fix issues
4. [ ] Restart: `pm2 restart screenshot-service`

## Documentation

- [ ] Team members biết cách check logs
- [ ] Team members biết cách restart service
- [ ] Emergency contacts documented
- [ ] Troubleshooting guide accessible

## Final Checks

- [ ] All tests passed ✅
- [ ] Production URL works ✅
- [ ] Monitoring setup ✅
- [ ] Team trained ✅
- [ ] Documentation complete ✅
- [ ] Rollback plan tested ✅

---

## Sign-off

| Role | Name | Date | Signature |
|------|------|------|-----------|
| Developer | ___________ | ______ | _________ |
| Tester | ___________ | ______ | _________ |
| DevOps | ___________ | ______ | _________ |
| Product Owner | ___________ | ______ | _________ |

---

**🎉 Deployment Complete!**

Next: Monitor for 24h and review logs.
