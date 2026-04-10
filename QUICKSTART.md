# IMS - Quick Start Guide

## 🚀 Getting Started in 5 Minutes

### Quick Setup
1. **Extract files** to your web root
2. **Create database** `ims_db` in MySQL
3. **Import** `database/ims_database.sql`
4. **Create user** (see step below)
5. **Login** and start using!

### Create First Admin User (SQL)
```sql
INSERT INTO users (Username, Password, Email, Role) 
VALUES ('admin', 'password123', 'admin@ims.local', 'Admin');
```

### Database Connection
- **Host**: localhost
- **User**: root
- **Password**: riad23
- **Database**: ims_db

### Default Login
- **URL**: `http://localhost/i_m_s/public/`
- **Username**: `admin`
- **Password**: `password123`

---

## 📋 Quick Reference

### Main Functions

| Feature | How to Reach | What to Do |
|---------|------------|-----------|
| **Assets** | Dashboard → Assets | Add/Edit/View assets |
| **Assignments** | Dashboard → Assignments | Assign assets to employees, track returns |
| **Maintenance** | Dashboard → Maintenance | Log maintenance issues and costs |
| **Vendors** | Dashboard → Vendors | Manage supplier information |
| **Requests** | Dashboard → Requests | Handle equipment requests |

### Keyboard Shortcuts
- `Ctrl+Shift+R` - Force refresh CSS/JS
- `F12` - Open Developer Console

---

## 🔍 Useful URLs

```
Login Page:      http://localhost/i_m_s/public/
Dashboard:       http://localhost/i_m_s/public/?url=dashboard/index
Assets:          http://localhost/i_m_s/public/?url=asset/index
Assignments:     http://localhost/i_m_s/public/?url=assignment/index
Maintenance:     http://localhost/i_m_s/public/?url=maintenance/index
Vendors:         http://localhost/i_m_s/public/?url=vendor/index
Requests:        http://localhost/i_m_s/public/?url=request/index
```

---

## 💡 Common Tasks

### Add a New Asset
1. Go to Assets → Add Asset
2. Fill in asset details
3. Set status (Available/In Use/Maintenance)
4. Click "Add Asset"

### Assign an Asset
1. Go to Assignments → Assign Asset
2. Select asset and employee
3. Set expected return date
4. Click "Assign"

### Record Maintenance
1. Go to Maintenance → Add Maintenance
2. Select asset
3. Enter estimated cost
4. Click "Add Record"

### Manage Vendors
1. Go to Vendors
2. Click "Add Vendor"
3. Enter vendor details
4. Click "Add Vendor"

### Handle Requests
1. Go to Requests
2. Review pending requests
3. Click "Approve" or "Reject"
4. System records the decision

---

## ⚙️ System Components

```
User Roles:
├── Admin      - Full access to all features
└── Manager    - Limited access to reports and approvals

Asset Statuses:
├── Available  - Ready for use
├── In Use     - Currently assigned
├── Maintenance - Being repaired
└── Disposed   - No longer in use

Request Status:
├── Pending    - Awaiting approval
├── Approved   - Approved, ready for fulfillment
└── Rejected   - Rejected request
```

---

## 📊 Dashboard Overview

The main dashboard shows:
- **Total Assets**: All assets in system
- **Pending Returns**: Assets not yet returned
- **Maintenance**: Pending maintenance issues
- **Assignments**: Total active assignments

---

## 🆘 Quick Troubleshoot

| Issue | Solution |
|-------|----------|
| Login fails | Check database connection, verify admin user exists |
| 404 errors | Restart Apache, check .htaccess in public folder |
| CSS missing | Hard refresh (Ctrl+Shift+R), check file permissions |
| No database | Create 'ims' database, import schema |

---

## 📚 Additional Help

- **Full Installation**: See `INSTALLATION.md`
- **Sample Data**: See `SAMPLE_DATA.md`
- **Database Schema**: See `database/ims_database.sql`

---

**Need more help?** Check the full documentation in README.md

**Version**: 1.0 | **Last Updated**: April 2026
