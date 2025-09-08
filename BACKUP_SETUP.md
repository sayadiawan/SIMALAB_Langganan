# Backup System Setup

Sistem backup otomatis untuk aplikasi Laravel Boyolali Labkes yang membuat backup database dan mengupload ke Google Drive dengan pembersihan otomatis.

## Fitur

- Backup otomatis harian pada jam 2:00 AM
- Backup database MySQL saja (lebih cepat dan efisien)
- Upload otomatis ke Google Drive (opsional)
- **Pembersihan otomatis**: Hanya menyimpan 2 file backup terakhir (lokal dan Google Drive)
- Periode retensi yang dapat dikonfigurasi

## Setup Instructions

### 1. Install Dependencies

Package yang diperlukan sudah diinstall:
- `spatie/laravel-backup` - Backup functionality
- `google/apiclient` - Google Drive API integration

### 2. Environment Variables

Tambahkan variabel berikut ke file `.env`:

```env
# Backup Configuration
BACKUP_MAIL_TO=admin@boyolali-labkes.com
BACKUP_ARCHIVE_PASSWORD=

# Google Drive Configuration (Opsional)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback
GOOGLE_REFRESH_TOKEN=your-google-refresh-token
GOOGLE_DRIVE_FOLDER_ID=your-google-drive-folder-id
```

**Catatan**: Jika Google Drive credentials tidak dikonfigurasi, backup akan tetap berjalan tapi hanya disimpan lokal.

### 3. Google Drive Setup (Opsional)

Untuk setup Google Drive dengan mudah, jalankan:
```bash
php artisan backup:setup-google-drive
```

Atau ikuti langkah manual:

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru atau pilih yang sudah ada
3. Aktifkan Google Drive API
4. Buat OAuth 2.0 credentials
5. Download credentials dan dapatkan:
   - Client ID
   - Client Secret
6. Buat folder di Google Drive untuk backup
7. Ambil folder ID dari URL
8. Generate refresh token (bisa menggunakan Google's OAuth 2.0 Playground)

### 4. System Requirements

Pastikan server memiliki:
- `mysqldump` command tersedia
- `zip` command tersedia
- Write permissions ke direktori storage

### 5. Test Backup System

Jalankan command berikut untuk test:

```bash
# Test backup functionality
php artisan backup:test

# Buat backup manual
php artisan backup:create

# Cleanup backup lama
php artisan backup:cleanup

# Cleanup Google Drive backup (jika dikonfigurasi)
php artisan backup:google cleanup

# Setup Google Drive (untuk panduan lengkap)
php artisan backup:setup-google-drive
```

### 6. Schedule Setup

Sistem backup otomatis dijadwalkan di `app/Console/Kernel.php`:

- Backup harian pada jam 2:00 AM
- Cleanup mingguan pada hari Minggu jam 3:00 AM
- Cleanup Google Drive mingguan pada hari Minggu jam 3:30 AM

Pastikan cron job sudah diset untuk menjalankan Laravel scheduler:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Configuration

### Backup Retention

Sistem ini menggunakan strategi **"Keep Latest 2"**:
- Hanya menyimpan 2 file backup terakhir di sistem lokal
- Hanya menyimpan 2 file backup terakhir di Google Drive
- File lama akan otomatis dihapus saat backup baru dibuat

Anda dapat mengubah jumlah file yang disimpan di `app/Services/BackupService.php`:
```php
// Di method createBackup()
$this->cleanupLocalBackups(2); // Ubah angka 2 sesuai kebutuhan

// Di method uploadToGoogleDrive()
$this->cleanupGoogleDriveBackups(2); // Ubah angka 2 sesuai kebutuhan
```

### Backup Content

Sistem ini hanya backup:
- Database MySQL (semua tabel dan data)

**Tidak termasuk**:
- File aplikasi (vendor, node_modules, dll)
- Log files
- Cache files
- Session files

## Commands Available

```bash
php artisan backup:test              # Test backup functionality
php artisan backup:create            # Buat backup manual
php artisan backup:cleanup           # Cleanup backup lama
php artisan backup:google cleanup    # Cleanup Google Drive backup
php artisan backup:setup-google-drive # Panduan setup Google Drive
```

## Troubleshooting

### Common Issues

1. **Permission Denied**: Pastikan web server memiliki write permissions ke direktori storage
2. **Database Backup Failed**: Pastikan `mysqldump` tersedia dan database credentials benar
3. **Zip Creation Failed**: Pastikan `zip` command terinstall
4. **Google Drive Upload Failed**: Cek Google API credentials dan refresh token

### Logs

Cek Laravel logs untuk pesan backup:
```bash
tail -f storage/logs/laravel.log
```

### Manual Testing

Test komponen individual:
```bash
# Test database backup
mysqldump -h localhost -u username -p database_name > test.sql

# Test zip creation
zip -r test.zip test.sql
```

## Security Notes

- Simpan Google API credentials dengan aman
- Gunakan password yang kuat untuk backup archives
- Rotasi refresh token secara berkala
- Monitor backup logs untuk masalah
- Pertimbangkan enkripsi data backup sensitif

## File Locations

- **Backup files**: `storage/app/backups/`
- **Temporary files**: `storage/app/backup-temp/`
- **Logs**: `storage/logs/laravel.log`

## Backup Strategy

Sistem ini menggunakan strategi **"Latest 2"** yang berarti:
- Setiap kali backup baru dibuat, file lama akan dihapus
- Hanya 2 file backup terakhir yang disimpan
- Ini menghemat ruang penyimpanan dan memastikan backup selalu fresh
- Backup otomatis setiap hari jam 2:00 AM
- Cleanup otomatis setiap kali backup baru dibuat 