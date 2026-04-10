# IMS - Database Migration Guide

## 🔄 Migration Setup with New Credentials

This guide will help you set up the IMS database with the new credentials.

**Database Configuration:**
- Database Name: `ims_db`
- Username: `root`
- Password: `riad23`

---

## Step 1: Verify MySQL is Running

### On Windows (XAMPP)
```bash
# Start Apache and MySQL from XAMPP Control Panel
# OR via command line:
cd C:\xampp
mysql-ctl start
```

### On Linux/Mac
```bash
# Start MySQL service
sudo systemctl start mysql
# or
sudo service mysql start
```

---

## Step 2: Create the Database

### Method 1: Using phpMyAdmin (Easiest)

1. Open browser: `http://localhost/phpmyadmin`
2. Look for "New" or "Create Database" button
3. Enter database name: `ims_db`
4. Select Collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Method 2: Using MySQL Command Line

Open terminal/command prompt and run:

```bash
mysql -u root -p riad23

# Once logged in, execute:
CREATE DATABASE ims_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Verify creation:
SHOW DATABASES;

# Exit MySQL:
exit;
```

### Method 3: Import SQL File with Database Creation

The `ims_database.sql` file already contains the CREATE DATABASE statement, so you can import it directly:

```bash
mysql -u root -p riad23 < database/ims_database.sql
```

---

## Step 3: Import Database Schema

### Option A: phpMyAdmin Import

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select the `ims_db` database from left sidebar
3. Click "Import" tab
4. Click "Choose File"
5. Select: `database/ims_database.sql`
6. Scroll down and click "Go"
7. Wait for completion ✓

### Option B: MySQL Command Line

```bash
# One-liner import
mysql -u root -p riad23 ims_db < database/ims_database.sql

# Verbose output (shows progress)
mysql -u root -p riad23 ims_db < database/ims_database.sql -v

# From within MySQL:
mysql -u root -p riad23
USE ims_db;
SOURCE database/ims_database.sql;
exit;
```

---

## Step 4: Verify Database Structure

### Check Tables in phpMyAdmin

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click on `ims_db` database
3. You should see 10 tables:
   - users
   - asset
   - assignment
   - employee
   - maintenance
   - vendor
   - equipment_request
   - department
   - disposal
   - purchase

### Check via MySQL Command

```bash
mysql -u root -p riad23 -e "USE ims_db; SHOW TABLES;"
```

Expected output:
```
+---------------------------+
| Tables_in_ims_db          |
+---------------------------+
| asset                     |
| assignment                |
| department                |
| disposal                  |
| employee                  |
| equipment_request         |
| maintenance               |
| purchase                  |
| user                      |
| vendor                    |
+---------------------------+
```

---

## Step 5: Create Admin User

### Using phpMyAdmin

1. Open phpMyAdmin → `ims_db` database
2. Click "users" table
3. Click "Insert" tab
4. Fill in the form:
   - User_ID: 1
   - Username: admin
   - Password: password123
   - Email: admin@ims.local
   - Role: Admin
5. Click "Go"

### Using MySQL Command

```bash
mysql -u root -p riad23 ims_db -e "INSERT INTO users (User_ID, Username, Password, Email, Role) VALUES (1, 'admin', 'password123', 'admin@ims.local', 'Admin');"
```

### Or MySQL CLI

```bash
mysql -u root -p riad23
USE ims_db;
INSERT INTO users (User_ID, Username, Password, Email, Role) VALUES (1, 'admin', 'password123', 'admin@ims.local', 'Admin');
exit;
```

---

## Step 6: Test Database Connection

### Test from Terminal

```bash
# Connect to database
mysql -u root -p riad23 ims_db

# Test queries
SHOW TABLES;
SELECT * FROM users;
SELECT COUNT(*) FROM asset;

# Exit
exit;
```

### Test from PHP

Access: `http://localhost/i_m_s/public/system-check.php`

This will show:
- ✓ Database connection successful
- ✓ All tables created
- ✓ Admin user found

---

## Step 7: Verify Configuration Files

### Check config/database.php

```php
<?php
$host = "localhost";
$user = "root";
$password = "riad23";      // ← Your password
$database = "ims_db";      // ← Your database name
```

### Check config/.env.example

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', 'riad23');      // ← Your password
define('DB_NAME', 'ims_db');          // ← Your database name
```

---

## Step 8: Load Sample Data (Optional)

To populate test data:

```bash
# Execute the SAMPLE_DATA.md SQL commands
# Either in phpMyAdmin or MySQL CLI

INSERT INTO department (Department_ID, Department_Name, Location) VALUES
(1, 'IT', 'Ground Floor'),
(2, 'Finance', 'First Floor'),
(3, 'HR', 'First Floor'),
(4, 'Operations', 'Ground Floor');

INSERT INTO employee (User_ID, Name, Designation, Contact_Number, Email, Department_ID) VALUES
(3, 'John Doe', 'IT Manager', '1234567890', 'john@company.com', 1),
(4, 'Jane Smith', 'Finance Officer', '0987654321', 'jane@company.com', 2);

# See SAMPLE_DATA.md for all sample inserts
```

---

## Step 9: Access the Application

1. Ensure Apache/PHP server is running
2. Open browser: `http://localhost/i_m_s/public/`
3. Login with:
   - Username: `admin`
   - Password: `password123`
4. You should see the dashboard ✓

---

## 🐛 Troubleshooting

### Error: "Access denied for user 'root'@'localhost'"

**Problem**: Wrong password or user not found

**Solution**:
```bash
# Verify MySQL is running
# Check password is correct: riad23
# Try connecting directly:
mysql -u root -p riad23

# If it fails, MySQL might not have the user
# Create the user:
mysql -u root
CREATE USER 'root'@'localhost' IDENTIFIED BY 'riad23';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
exit;
```

### Error: "Can't connect to MySQL server"

**Problem**: MySQL service not running

**Solution**:
```bash
# Windows XAMPP:
# Start from Control Panel or use:
cd C:\xampp
mysql-ctl start

# Linux:
sudo systemctl start mysql

# Mac:
brew services start mysql
```

### Error: "Unknown database 'ims_db'"

**Problem**: Database not created

**Solution**:
```bash
# Create database:
mysql -u root -p riad23 -e "CREATE DATABASE ims_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Verify:
mysql -u root -p riad23 -e "SHOW DATABASES;"
```

### Error: "Table doesn't exist"

**Problem**: Schema not imported

**Solution**:
```bash
# Import schema:
mysql -u root -p riad23 ims_db < database/ims_database.sql

# Verify tables:
mysql -u root -p riad23 -e "USE ims_db; SHOW TABLES;"
```

### Connection Failed at localhost/i_m_s/public/

**Problem**: PHP can't connect to database

**Solution**:
1. Check `config/database.php` has correct credentials
2. Run system check: `http://localhost/i_m_s/public/system-check.php`
3. Verify database exists: `mysql -u root -p riad23 -e "USE ims_db;"`
4. Check MySQL service is running

---

## 📊 Database Credentials Reference

```
Host:             localhost
Port:             3306 (default)
Database:         ims_db
User:             root
Password:         riad23
Root Password:    (leave empty if not set)
```

---

## 🔐 Security Notes

### For Development
Current setup is suitable for local development.

### For Production
1. Change admin password immediately
2. Use strong password for MySQL user
3. Create user with limited privileges
4. Use environment variables for credentials
5. Enable SSL/TLS for MySQL connection
6. Backup database regularly

---

## ✅ Migration Checklist

- [ ] MySQL service is running
- [ ] Database `ims_db` created
- [ ] Schema imported successfully
- [ ] All 10 tables exist in database
- [ ] Admin user created
- [ ] config/database.php has correct credentials
- [ ] System check passes all tests
- [ ] Can login at http://localhost/i_m_s/public/

---

## 🚀 Next Steps

Once migration is complete:
1. Login to dashboard
2. Create departments
3. Add employees
4. Add assets
5. Start managing inventory!

---

**Database Version**: 1.0  
**Last Updated**: April 10, 2026  
**Credentials**: root / riad23 / ims_db

For issues, refer to INSTALLATION.md or README.md
