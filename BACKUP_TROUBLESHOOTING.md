# Backup System Troubleshooting Guide

## Masalah Umum dan Solusi

### 1. Error: "mysqldump: command not found"

**Gejala:**
```
Backup failed: Database backup failed
sh: 9278: command not found
Usage: mysqldump [OPTIONS] database [tables]
```

**Solusi:**

#### Ubuntu/Debian:
```bash
sudo apt-get update
sudo apt-get install mysql-client
```

#### CentOS/RHEL:
```bash
sudo yum install mysql
# atau
sudo dnf install mysql
```

#### XAMPP/LAMPP:
Jika menggunakan XAMPP/LAMPP, pastikan path mysqldump sudah benar:
```bash
# Cek path mysqldump
which mysqldump
# atau
find /opt -name mysqldump 2>/dev/null
```

#### Manual Installation:
```bash
# Download MySQL client
wget https://dev.mysql.com/get/Downloads/MySQL-8.0/mysql-8.0.xx-linux-glibc2.12-x86_64.tar.gz
tar -xzf mysql-8.0.xx-linux-glibc2.12-x86_64.tar.gz
sudo mv mysql-8.0.xx-linux-glibc2.12-x86_64 /opt/mysql
export PATH=$PATH:/opt/mysql/bin
```

### 2. Error: "zip: command not found"

**Solusi:**

#### Ubuntu/Debian:
```bash
sudo apt-get install zip unzip
```

#### CentOS/RHEL:
```bash
sudo yum install zip unzip
# atau
sudo dnf install zip unzip
```

### 3. Error: "Permission denied"

**Solusi:**
```bash
# Berikan permission write ke direktori storage
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/
# atau untuk user yang menjalankan web server
sudo chown -R $USER:$USER storage/
```

### 4. Error: "Database connection failed"

**Solusi:**
1. Cek konfigurasi database di `.env`
2. Pastikan MySQL server berjalan
3. Cek credentials database

### 5. Error: "Google Drive upload failed"

**Solusi:**
1. Cek Google Drive credentials di `.env`
2. Pastikan refresh token masih valid
3. Cek folder ID di Google Drive

## Commands untuk Troubleshooting

### 1. Check Requirements
```bash
php artisan backup:check-requirements
```

### 2. Test Backup (tanpa Google Drive)
```bash
php artisan backup:test
```

### 3. Test Database Connection
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

### 4. Check Logs
```bash
tail -f storage/logs/laravel.log
```

### 5. Manual Database Backup Test
```bash
# Test mysqldump manual
mysqldump -h localhost -u username -p database_name > test.sql

# Test dengan credentials dari .env
mysqldump -h 127.0.0.1 -u dannu -p labkesboyolali > test.sql
```

## Konfigurasi Server

### 1. Install Dependencies
```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install mysql-client zip unzip

# CentOS/RHEL
sudo yum install mysql zip unzip
```

### 2. Setup Cron Job
```bash
# Edit crontab
crontab -e

# Tambahkan line berikut
* * * * * cd /path/to/boyolali-labkes && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Check Cron Status
```bash
# Cek apakah cron berjalan
sudo systemctl status cron

# Cek log cron
sudo tail -f /var/log/cron
```

## Debug Mode

Untuk debugging lebih detail, tambahkan di `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## Common Server Issues

### 1. SELinux (CentOS/RHEL)
Jika menggunakan SELinux:
```bash
# Allow web server to execute mysqldump
sudo setsebool -P httpd_exec_mem 1
sudo setsebool -P httpd_can_network_connect 1
```

### 2. Firewall
```bash
# Allow MySQL port
sudo ufw allow 3306
# atau
sudo firewall-cmd --permanent --add-port=3306/tcp
```

### 3. Memory Issues
Jika backup gagal karena memory:
```bash
# Cek memory usage
free -h

# Optimize MySQL untuk backup
# Tambahkan di my.cnf
[mysqldump]
quick
single-transaction
```

## Support

Jika masih mengalami masalah:
1. Jalankan `php artisan backup:check-requirements`
2. Cek log di `storage/logs/laravel.log`
3. Pastikan semua dependencies terinstall
4. Cek permission direktori storage 