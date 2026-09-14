# 🔧 Quick Fix: PostgreSQL Support

## ✅ Fixed

Đã fix lỗi `DATE_FORMAT()` không tồn tại trong PostgreSQL.

Controller giờ tự động detect database driver:
- **MySQL**: Dùng `DATE_FORMAT(time, '%Y-%m-%d %H:%i:00')`
- **PostgreSQL**: Dùng `TO_CHAR(time, 'YYYY-MM-DD HH24:MI:00')`

## 🚀 Cách chạy

### 1. Insert test data (QUAN TRỌNG!)

```bash
# Via browser
http://your-domain/tool1/admin/insert-monitor-test-data.php

# Or via PHP CLI
php public/tool1/admin/insert-monitor-test-data.php
```

Script sẽ tạo:
- ✅ ~1,700+ records trong `monitor_checks` (3 monitors x 48 hours x 12 checks/hour)
- ✅ ~4,600+ records trong `monitor_system_metrics` (8 metrics x 48 hours x 12 checks/hour)

### 2. Test API endpoints

```bash
# Uptime status
curl "http://your-domain/api/monitor-graph/uptime?monitor_id=1&period=24h"

# Response time
curl "http://your-domain/api/monitor-graph/response-time?monitor_id=1&period=24h"

# System metrics
curl "http://your-domain/api/monitor-graph/system-metrics?metric_type=cpu_usage&period=24h"

# Available metrics
curl "http://your-domain/api/monitor-graph/metric-types"
```

### 3. View demo page

```
http://your-domain/monitor-demo.html
```

## 📊 Test Data Details

### Monitors:
- **Monitor ID 1**: Website (95% uptime, 50-300ms response)
- **Monitor ID 2**: API (85% uptime, 100-500ms response)
- **Monitor ID 3**: Database (99% uptime, 10-100ms response)

### System Metrics:
- `cpu_usage`: 40-80%
- `memory_usage`: 4-12 GB
- `disk_io`: 20-80 MB/s
- `network_bandwidth`: 50-200 Mbps
- `db_connections`: 20-80 connections
- `active_users`: 800-2000 users
- `request_rate`: 500-1500 req/min
- `error_rate`: 0.5-5%

## 🔍 Debug

Nếu vẫn lỗi:

```bash
# Check database driver
php artisan tinker
>>> DB::connection()->getDriverName()
=> "pgsql"  // or "mysql"

# Test query directly
>>> DB::table('monitor_checks')->where('monitor_id', 1)->count()

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## ✅ Done!

Refresh browser và test lại! 🎉
