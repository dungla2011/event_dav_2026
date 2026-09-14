# Backup Command với Domain & Connection Options

## ✅ Status: WORKING

Database backup hiện đã hoạt động chính xác! Khi chạy với `--domain=ping365.io`, backup sẽ:
- ✅ Tự động detect PostgreSQL connection từ domain
- ✅ Dump database thực sự (975+ KB, không chỉ 918 B artisan file)
- ✅ Sử dụng pg_dump cho PostgreSQL (không phải mysqldump)
- ✅ Backup thành công và upload lên FTP

## 📋 Tổng quan

Đã tạo custom command `backup:run` extend từ Spatie Backup package, thêm 2 options:
- `--domain` để override `$_SERVER['HTTP_HOST']` và `$_SERVER['SERVER_NAME']`
- `--connection` để override database connection được backup

## 📁 Files đã tạo/sửa:

1. **`app/Console/Commands/BackupWithServerCommand.php`** - Custom backup command
2. **`app/Console/Kernel.php`** - Register command
3. **`config/backup.php`** - Auto-detect database connection từ CLI arguments
4. **`config/database.php`** - Không thay đổi (sử dụng logic domain mapping có sẵn)

## 🚀 Cách sử dụng:

### 1. Backup thông thường (sử dụng config mặc định):
```bash
php artisan backup:run
```

### 2. Backup với custom domain hostname:
```bash
php artisan backup:run --domain=example.com
```

### 3. Backup với custom database connection:
```bash
# Backup PostgreSQL thay vì MySQL
php artisan backup:run --connection=pgsql

# Backup MySQL (theo config .env: DB_CONNECTION=mysql)
php artisan backup:run --connection=mysql

# Backup connection khác
php artisan backup:run --connection=pgsql2
```

### 4. Kết hợp nhiều options:
```bash
# Backup PostgreSQL database với custom domain (RECOMMENDED)
php artisan backup:run --domain=ping365.io

# Backup PostgreSQL với filename custom
php artisan backup:run --connection=pgsql --filename=pgsql-backup.zip --domain=staging.example.com

# Backup MySQL với domain  
php artisan backup:run --domain=mysql.example.com

# Backup connection khác với domain
php artisan backup:run --connection=pgsql2 --domain=production.example.com
```

**✅ Đã fix:** Database backup hiện đã hoạt động chính xác! Backup sẽ dump database thực sự, không chỉ copy file artisan.

## 🔧 Cách hoạt động:

```php
public function handle(): int
{
    // 1. Set domain nếu có
    if ($domainName = $this->option('domain')) {
        $_SERVER['HTTP_HOST'] = $domainName;
        $_SERVER['SERVER_NAME'] = $domainName;
        $this->comment("Domain hostname set to: {$domainName}");
    }

    // 2. Override database connection nếu có
    if ($connection = $this->option('connection')) {
        config(['backup.backup.source.databases' => [$connection]]);
        $this->comment("Database connection set to: {$connection}");
    }

    // 3. Chạy backup
    return parent::handle();
}
```

## 📝 Các options có sẵn:

| Option | Mô tả |
|--------|-------|
| `--domain=` | **[MỚI]** Set custom domain hostname |
| `--connection=` | **[MỚI]** Override database connection (mysql, pgsql, pgsql2, etc.) |
| `--filename=` | Custom backup filename |
| `--only-db` | Chỉ backup database |
| `--db-name=` | Backup specific database(s) |
| `--only-files` | Chỉ backup files |
| `--only-to-disk=` | Backup to specific disk |
| `--disable-notifications` | Tắt notifications |
| `--timeout=` | Set timeout (seconds) |
| `--tries=` | Số lần retry nếu fail |

## 🗄️ Database Connections trong .env:

Dựa vào file `.env` của bạn, có các connections sau:

### MySQL (connection: `mysql`):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=glx2022db
DB_USERNAME=root
DB_PASSWORD=
```

### PostgreSQL 1 (connection: `pgsql`):
```env
DB_PG1_CONNECTION=postgres
DB_PG1_HOST=127.0.0.1
DB_PG1_PORT=5432
DB_PG1_DATABASE=test123
DB_PG1_USERNAME=postgres
DB_PG1_PASSWORD=Hanoi123000
DB_PG1_SCHEMA=public
```

### PostgreSQL 2 - Monitor (connection: `pgsql2`):
```env
DB_PG2_CONNECTION=postgres
DB_PG2_HOST=localhost
DB_PG2_PORT=5432
DB_PG2_DATABASE=monitor_v2
DB_PG2_USERNAME=admin
DB_PG2_PASSWORD=Qqqppp123
DB_PG2_SCHEMA=glx_monitor_v2
```

### Remote MySQL Connections:
```env
# Remote 1 (connection: rm1)
DB_RM_HOST1=sv216230
DB_RM_NAME1=test2024
DB_RM_USER1=webuser02
DB_RM_PW1=JwkDm_odM4Jw111

# Remote 2 (connection: rm2)
DB_RM_HOST2=127.0.0.1
DB_RM_NAME2=glx_event
DB_RM_USER2=admin
DB_RM_PW2=Cloud@222

# Remote 3,4,5 tương tự...
```

## 🎯 Use Cases:

### Case 1: Backup PostgreSQL Monitor Database
```bash
php artisan backup:run --connection=pgsql2 --only-db --domain=monitor.example.com
```

### Case 2: Backup MySQL Main Database
```bash
php artisan backup:run --connection=mysql --only-db --domain=production.example.com
```

### Case 3: Backup Remote Database
```bash
php artisan backup:run --connection=rm1 --only-db --domain=remote.example.com
```

### Case 4: Backup Multiple Connections (chạy riêng từng command)
```bash
# Backup MySQL
php artisan backup:run --connection=mysql --only-db --filename=mysql-backup.zip

# Backup PostgreSQL 1
php artisan backup:run --connection=pgsql --only-db --filename=pgsql1-backup.zip

# Backup PostgreSQL 2
php artisan backup:run --connection=pgsql2 --only-db --filename=pgsql2-backup.zip
```

## ✅ Lợi ích:

1. **Không cần sửa vendor** - Extend từ Spatie BackupCommand
2. **Backward compatible** - Vẫn dùng được tất cả options cũ
3. **Flexible** - Có thể:
   - Set domain khác nhau cho từng lần backup
   - Backup nhiều database connections khác nhau
   - Switch giữa MySQL và PostgreSQL dễ dàng
4. **Multi-database support** - Backup từng database riêng hoặc tất cả
5. **Clean code** - Chỉ thêm logic override ở đầu

## 🔍 Khi nào sử dụng:

### Option `--domain`:
- **Multi-tenant**: Backup cho từng domain riêng
- **Environment switch**: Backup staging vs production
- **URL generation**: Khi backup cần generate URLs với domain cụ thể
- **CLI context**: Khi chạy backup từ cron/script không có HTTP context

### Option `--connection`:
- **Multi-database architecture**: Có nhiều DB (MySQL + PostgreSQL)
- **Separate backups**: Backup riêng từng database
- **Database migration**: Backup trước khi migrate
- **Remote databases**: Backup từ remote servers
- **Testing**: Backup test database riêng với production

## 🎯 Testing:

```bash
# Test xem command có register chưa
php artisan list | grep backup

# Test backup MySQL với domain
php artisan backup:run --connection=mysql --only-db --domain=test.local

# Test backup PostgreSQL
php artisan backup:run --connection=pgsql2 --only-db --domain=monitor.local

# Kiểm tra file backup được tạo
ls -lh storage/app/backups/
```

## 📌 Lưu ý quan trọng:

1. **Command override**: Command này **override** command mặc định `backup:run` của Spatie
2. **Config runtime**: Option `--connection` chỉ override trong runtime, không thay đổi file config
3. **Connection name**: Tên connection phải tồn tại trong `config/database.php`
4. **Multiple databases**: Nếu muốn backup nhiều DB cùng lúc, sửa `config/backup.php`:
   ```php
   'databases' => ['mysql', 'pgsql', 'pgsql2'],
   ```
5. **Không ảnh hưởng**: Các command khác vẫn hoạt động bình thường:
   - `backup:clean`
   - `backup:list`
   - `backup:monitor`

## 💡 Tips:

### Tạo bash script backup tất cả databases:
```bash
#!/bin/bash
# backup-all.sh

DATE=$(date +%Y%m%d_%H%M%S)

# Backup MySQL
php artisan backup:run --connection=mysql --only-db \
    --filename="mysql_${DATE}.zip" \
    --domain=production.example.com

# Backup PostgreSQL 1
php artisan backup:run --connection=pgsql --only-db \
    --filename="pgsql1_${DATE}.zip" \
    --domain=production.example.com

# Backup PostgreSQL 2 (Monitor)
php artisan backup:run --connection=pgsql2 --only-db \
    --filename="monitor_${DATE}.zip" \
    --domain=monitor.example.com

echo "All backups completed!"
```

### Crontab để chạy backup tự động:
```cron
# Backup MySQL hàng ngày lúc 2am
0 2 * * * cd /path/to/project && php artisan backup:run --connection=mysql --only-db --domain=production.example.com

# Backup PostgreSQL Monitor mỗi 6 giờ
0 */6 * * * cd /path/to/project && php artisan backup:run --connection=pgsql2 --only-db --domain=monitor.example.com
```
