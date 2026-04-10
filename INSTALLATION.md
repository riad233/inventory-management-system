# IMS - Installation & Setup Guide

## System Requirements

- **PHP**: 7.4 or higher (8.0+ recommended)
- **MySQL/MariaDB**: 5.7 or higher
- **Web Server**: Apache 2.4+ (with mod_rewrite enabled)
- **Disk Space**: At least 100 MB
- **RAM**: Minimum 512 MB

## Step-by-Step Installation

### 1. Download/Extract Project Files

```bash
# If you have the ZIP file, extract it to your web root:
# Windows (XAMPP):  C:\xampp\htdocs\i_m_s\
# Linux (Apache):    /var/www/html/i_m_s\
# macOS (XAMPP):     /Applications/XAMPP/htdocs/i_m_s/
```

### 2. Create Database

**Option A: Using phpMyAdmin**
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "New" or "Create Database"
3. Database name: `ims_db`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

**Option B: Using MySQL Command Line**
```bash
mysql -u root -p
CREATE DATABASE ims_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

### 3. Import Database Schema

**Using phpMyAdmin:**
1. Select the `ims_db` database
2. Go to "Import" tab
3. Choose file: `database/ims_database.sql`
4. Click "Go"

**Using MySQL command line:**
```bash
mysql -u root -p riad23 ims_db < database/ims_database.sql
```

### 4. Create Admin User (Initial Setup)

Run this SQL in phpMyAdmin or MySQL:
```sql
INSERT INTO users (User_ID, Username, Password, Email, Role) VALUES
(1, 'admin', 'admin123', 'admin@ims.local', 'Admin');
```

### 5. Configure Database Connection

Edit: `config/database.php`

```php
<?php
$host = "localhost";      // Database host
$user = "root";           // Database user
$password = "riad23";     // Database password
$database = "ims_db";     // Database name
```

**Current Configuration:**
- Host: `localhost`
- User: `root`
- Password: `riad23`
- Database: `ims_db`

### 6. Set File Permissions

**Linux/macOS:**
```bash
chmod -R 755 /var/www/html/i_m_s/
chmod -R 777 /var/www/html/i_m_s/storage/
```

**Windows:** Usually automatic, but ensure write permissions on `storage/logs/`

### 7. Verify Installation

1. Open browser: `http://localhost/i_m_s/public/`
2. You should see the login page
3. Login with:
   - Username: `admin`
   - Password: `admin123`
4. Change password immediately in production

### 8. Optional: Load Sample Data

1. Run the SQL commands from `SAMPLE_DATA.md`
2. This creates test departments, employees, assets, etc.

## Security Checklist

After installation, follow these steps:

### ✅ Essential
- [ ] Change default admin password
- [ ] Create separate admin user with strong password
- [ ] Remove/rename `SAMPLE_DATA.md` after importing data
- [ ] Set proper file permissions (no world-writable files)
- [ ] Enable HTTPS/SSL certificate

### ⚠️ Important
- [ ] Enable PHP error logging (set error_log in php.ini)
- [ ] Disable PHP error display in production
- [ ] Backup database regularly
- [ ] Keep PHP and MySQL updated
- [ ] Review access logs regularly

### 🔒 Advanced
- [ ] Implement prepared statements for all DB queries
- [ ] Add input validation/sanitization
- [ ] Use CSRF tokens
- [ ] Implement rate limiting
- [ ] Setup firewall rules
- [ ] Enable mod_security on Apache

## Troubleshooting

### Error: "Connection Failed"
**Problem**: Database connection error
**Solution**:
```bash
# Check MySQL is running
# Verify credentials in config/database.php
# Ensure database 'ims' exists
mysql -u root -p -e "SHOW DATABASES;"
```

### Error: "Table doesn't exist"
**Problem**: Database schema not imported
**Solution**:
```bash
# Reimport the database schema
mysql -u root -p ims < database/ims_database.sql
```

### Error: "Page not found" / 404
**Problem**: URL routing issue
**Solution**:
1. Verify mod_rewrite is enabled: `a2enmod rewrite`
2. Check .htaccess is in `public/` folder
3. Verify Apache configuration allows .htaccess overrides

### Error: "Permission denied"
**Problem**: File permission issues
**Solution**:
```bash
# Fix permissions
sudo chmod -R 755 /var/www/html/i_m_s/
sudo chown -R www-data:www-data /var/www/html/i_m_s/
```

### CSS/Images Not Loading
**Problem**: Asset files not loading
**Solution**:
1. Check file paths in HTML files
2. Verify CSS folder: `public/css/`
3. Hard refresh: Ctrl+Shift+R or Cmd+Shift+R
4. Check browser console for 404 errors

### Session/Login Not Working
**Problem**: Session errors
**Solution**:
1. Ensure PHP session.save_path is writable:
   ```bash
   # Check PHP session directory
   php -i | grep session.save_path
   # Usually /tmp or /var/lib/php/sessions
   chmod 777 /tmp
   ```
2. Verify sessions folder exists
3. Check php.ini settings

## Performance Optimization

### Database
```sql
-- Add indexes for frequently searched fields
CREATE INDEX idx_username ON users(Username);
CREATE INDEX idx_asset_status ON asset(Status);
CREATE INDEX idx_assignment_user ON assignment(User_ID);
```

### PHP Configuration (php.ini)
```ini
; Increase limits for large imports
max_execution_time = 300
memory_limit = 256M
upload_max_filesize = 50M
post_max_size = 50M

; Enable OPcache for faster execution
opcache.enable = 1
opcache.memory_consumption = 128
```

### Apache Configuration
```apache
<Directory /var/www/html/i_m_s>
    # Enable compression
    mod_deflate.c
    
    # Set proper caching
    Header set Cache-Control "max-age=3600"
</Directory>
```

## Database Backup & Restore

### Backup
```bash
# Backup entire database
mysqldump -u root -p ims > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup with data verification
mysqldump -u root -p --single-transaction --quick --lock-tables=false ims > backup.sql
```

### Restore
```bash
# Restore from backup
mysql -u root -p ims < backup.sql

# Restore while preserving existing data (use with caution)
mysql -u root -p ims < backup.sql
```

## Additional Resources

- **PHP Documentation**: https://www.php.net/manual/
- **MySQL Documentation**: https://dev.mysql.com/doc/
- **Bootstrap Documentation**: https://getbootstrap.com/docs/
- **Apache Rewrite Guide**: https://httpd.apache.org/docs/current/mod/mod_rewrite.html

## Support

If you encounter issues:

1. Check error logs:
   ```bash
   # PHP error log
   tail -f /var/log/php-fpm/error.log
   
   # Apache error log
   tail -f /var/log/apache2/error.log
   
   # MySQL error log
   tail -f /var/log/mysql/error.log
   ```

2. Check browser console (F12) for JavaScript errors
3. Verify all file permissions are correct
4. Ensure database credentials match your setup
5. Check internet connection for CDN resources (Bootstrap, Font Awesome)

---

**Last Updated**: April 2026
**Version**: 1.0
