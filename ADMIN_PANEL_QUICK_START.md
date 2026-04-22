# Quick Start - Admin Panel
**IMS Admin Control Panel**

---

## 🎯 What's New

### Admin Panel Features
- ✅ **Admin Dashboard** - System overview & settings
- ✅ **User Management** - Create, edit, delete users
- ✅ **Activity Logs** - Track system activities
- ✅ **Stock Alerts** - Monitor inventory levels
- ✅ **Role-Based Access** - Admin-only visibility

---

## 📍 How to Access

### For Admin Users:
1. Login to IMS
2. Look for **"Admin"** link in:
   - **Top Navbar** (right side, next to Requests)
   - **Sidebar Navigation** (bottom, under Admin section)
3. Click to access Admin Control Panel

### Admin Section Only Shows If:
- Your role is "Admin" in database
- Current login session is active
- Not visible to Manager or Employee users

---

## 🔧 Admin Panel Modules

### 1. Admin Dashboard (Settings)
**Path:** ?url=admin/index

**Shows:**
- Total users count
- Total assets count
- Total employees count
- Total vendors count
- System settings overview
- Quick access links to other modules

**Features:**
- Professional stat cards
- Current configuration display
- One-click access to all admin features

---

### 2. User Management
**Path:** ?url=admin/users

**Shows:**
- Table of all system users
- Username, Email, Role for each user
- Action buttons: Edit, Delete

**Quick Actions:**
- **Add New User:** Click "Add New User" button
  - Fill username (min 3 chars)
  - Enter email
  - Set password (min 6 chars)
  - Confirm password
  - Choose role (Admin/Manager/Employee)
  - Click "Add User"

- **Edit User:** Click "Edit" button next to user
  - Change user role
  - Click "Update User"

- **Delete User:** Click "Delete" button
  - Confirm deletion (modal popup)
  - User removed from system

---

### 3. Activity Logs
**Path:** ?url=admin/logs

**Shows:**
- Recent system activities
- Timestamp of each action
- User who performed action
- Action description
- Module affected
- Status (success/warning)

**Usage:**
- Monitor who did what and when
- Track admin actions
- Audit trail for compliance

---

### 4. Stock Alerts
**Path:** ?url=dashboard/stockAlerts

**Shows:**
- Out of Stock items (Qty < 3) - RED
- Low Stock items (Qty 3-10) - YELLOW
- Item names and quantities
- Quick link to view assets

**Usage:**
- Click from navbar bell icon
- Access from dashboard
- Track inventory issues

---

## 👤 User Roles Explained

| Role | Permissions | Admin Access |
|------|-----------|--------------|
| **Admin** | Full system access | ✅ Yes |
| **Manager** | Manage assets & assignments | ❌ No |
| **Employee** | View own assignments | ❌ No |

---

## 🔐 Security Features

### Password Protection
- Passwords hashed with PASSWORD_DEFAULT (bcrypt)
- Minimum 6 characters
- Never shown in plaintext
- Confirmation required on create

### Access Control
- Admin-only pages blocked for non-admins
- CSRF tokens on all forms
- Session validation
- Activity logging

### Form Protection
- CSRF tokens (Cross-Site Request Forgery protection)
- Input validation
- Error messages
- Confirmation dialogs on delete

---

## ✨ Key Features

### Dynamic Navigation
- Admin section appears in sidebar for Admin users only
- Section divider and title
- Active page highlighting
- Mobile-responsive

### Efficient Queries
- Dashboard uses optimized count queries
- No N+1 query problems
- Fast page loads
- Professional performance

### Professional UI
- Bootstrap 5 styling
- Gradient backgrounds
- Color-coded badges
- Responsive design
- Mobile-friendly

### Error Handling
- Validation error messages
- Success confirmations
- Exception catching and logging
- User-friendly error display

---

## 📊 Dashboard Stats

The admin dashboard displays:
1. **Total Users** - All registered users
2. **Total Assets** - All inventory items
3. **Total Employees** - Employee records
4. **Total Vendors** - Vendor contacts

Each stat is clickable and leads to the respective module.

---

## 🎮 Quick Reference

| Task | Path | Steps |
|------|------|-------|
| View admin panel | ?url=admin/index | Click Admin link |
| Add user | ?url=admin/addUser | Click "Add New User" button |
| Edit user | ?url=admin/editUser/[ID] | Click "Edit" next to user |
| Delete user | ?url=admin/deleteUser/[ID] | Click "Delete" button |
| View users | ?url=admin/users | Click "User Management" |
| View logs | ?url=admin/logs | Click "Activity Logs" |
| Stock alerts | ?url=dashboard/stockAlerts | Click bell icon or link |

---

## 🐛 Troubleshooting

### Admin Section Not Showing
**Solution:** 
- Check your user role is "Admin" in database
- Clear browser cache (Ctrl+Shift+Del)
- Refresh page (F5)

### "Access Denied" Error
**Solution:**
- Only Admin users can access admin panel
- Ask an admin to promote your account
- Try logging in as different user

### User Creation Failed
**Solution:**
- Check all fields filled correctly
- Username must be 3+ characters
- Email must be valid format
- Passwords must match
- Check error messages for specific issue

### Stock Alerts Not Showing
**Solution:**
- Verify products in database
- Stock alert threshold is Qty < 3 (out) and 3-10 (low)
- Refresh page
- Check dashboard for stock data

---

## 📞 Support

For issues or questions:
1. Check browser console (F12 → Console tab)
2. Review error logs in storage/logs/
3. Check browser's Network tab for failed requests
4. Verify database connection in config/database.php

---

## 🎯 Pro Tips

1. **Batch Operations:** Add multiple users by repeating add process
2. **Role Management:** Change user roles anytime without password reset
3. **Activity Tracking:** Regularly check logs for system usage
4. **Stock Monitoring:** Check stock alerts before inventory runs out
5. **Security:** Always use strong passwords for admin accounts

---

## ✅ Status: READY TO USE

All admin features are fully tested and ready for production use.

Last Updated: April 22, 2026
