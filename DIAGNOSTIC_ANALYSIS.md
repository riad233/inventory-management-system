# Alert System - Comprehensive Diagnostic Analysis
**Date:** April 19, 2026

---

## 🔍 SYSTEM COMPONENTS ANALYSIS

### 1. **DATABASE LAYER**

#### Products Table Check:
- **File:** `database/ims_database.sql` (Line 274-283)
- **Status:** ✓ Schema defined
- **Table Name:** `products`
- **Columns:** 
  - `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
  - `name` (VARCHAR 255)
  - `quantity` (INT, DEFAULT 0)
  - `created_at` (DATETIME)
  - `updated_at` (DATETIME)

#### Sample Data Check:
- **Location:** Lines 339-349
- **Sample Records:**
  - Laptop Dell XPS 13: quantity = 2 (LOW STOCK)
  - Wireless Mouse Logitech: quantity = 8 (LOW STOCK)
  - USB-C Hub: quantity = 0 (OUT OF STOCK)
  - Mechanical Keyboard: quantity = 5 (LOW STOCK)
  - Monitor 24 inch: quantity = 12 (NORMAL)
  - HDMI Cable: quantity = 1 (OUT OF STOCK)
  - USB 3.0 Cable: quantity = 15 (NORMAL)
  - Power Bank 20000mAh: quantity = 3 (LOW STOCK)
  - Laptop Stand: quantity = 4 (LOW STOCK)
  - Desk Lamp LED: quantity = 9 (LOW STOCK)

**Expected Alert Count:** 7 alerts (2 Out of Stock + 5 Low Stock)

#### Alert Rules:
- **Out of Stock:** quantity < 3
- **Low Stock:** quantity >= 3 AND quantity <= 10

---

### 2. **PHP MODEL LAYER**

#### Alert Model (`app/models/Alert.php`)
- **Purpose:** Database queries for alert data
- **Location:** `app/models/Alert.php`
- **Base Class:** Model (inherits database connection)

**Methods:**

1. **getAll()** (Lines 13-49)
   - Returns all products matching alert conditions
   - SQL: CASE statement for alert_type classification
   - Error handling: Via Logger class
   - Returns: Array of alerts with alert_type

2. **getCount()** (Lines 54-78)
   - Returns count of products with alerts
   - SQL: COUNT(*) aggregation
   - Returns: Integer count

3. **getOutOfStock()** (Lines 83-108)
   - Returns only out of stock products (qty < 3)
   - SQL: WHERE quantity < 3

4. **getLowStock()** (Lines 113-138)
   - Returns only low stock products (3 <= qty <= 10)
   - SQL: WHERE quantity >= 3 AND quantity <= 10

**Security:** ✓ All queries use prepared statements

---

### 3. **PHP CONTROLLER LAYER**

#### AlertController (`app/controllers/AlertController.php`)

**Authentication Check:** ✓ Session validation on both endpoints

**Endpoints:**

1. **getAlerts()** (Lines 12-41)
   - Endpoint: `?url=alert/getAlerts`
   - Header: `Content-Type: application/json`
   - Auth Check: ✓ Requires `$_SESSION['username']`
   - Response Format:
     ```json
     {
       "success": true,
       "count": 7,
       "alerts": [...]
     }
     ```

2. **getCount()** (Lines 46-65)
   - Endpoint: `?url=alert/getCount`
   - Header: `Content-Type: application/json`
   - Auth Check: ✓ Requires `$_SESSION['username']`
   - Response Format:
     ```json
     {
       "success": true,
       "count": 7
     }
     ```

**Error Handling:** ✓ Try-catch blocks with logging

---

### 4. **ROUTING LAYER**

#### Router (`core/Router.php`)
- **URL Format:** `?url=controller/method`
- **Public Controllers:** `auth`, `home` (NO ROLE CHECK)
- **Protected Controllers:** ALL OTHERS (REQUIRE AUTH + ROLE CHECK)

**⚠️ CRITICAL ISSUE FOUND:**
```php
if (!in_array($role, ['Admin', 'Manager'], true)) {
    http_response_code(403);
    die('Forbidden');
}
```

**Alert controller is NOT in publicControllers list!**
- Requires `$_SESSION['username']`
- Requires role to be 'Admin' or 'Manager'
- If user is 'Employee' role → 403 Forbidden

---

### 5. **HTML/VIEW LAYER**

#### Layout (`app/views/layout.php`, Lines 81-90)
- **Bell Icon:** ✓ Present
- **Badge Span:** ✓ id="alertBadge", default value = "0"
- **Dropdown Container:** ✓ id="alertDropdown"
- **Dropdown Content:** ✓ id="alertDropdownContent"

**HTML Structure:**
```html
<div class="alert-bell" id="alertBellContainer">
    <button class="alert-bell-btn" id="alertBellBtn" title="View alerts">
        🔔
        <span class="alert-badge" id="alertBadge">0</span>
    </button>
    <div class="alert-dropdown" id="alertDropdown">
        <div class="alert-dropdown-header">Alerts</div>
        <div class="alert-dropdown-content" id="alertDropdownContent">
            <div class="alert-empty">Loading...</div>
        </div>
    </div>
</div>
```

---

### 6. **CSS STYLING**

#### Layout CSS (`public/css/layout.css`)
- **Alert Bell Styles:** ✓ Present
- **Alert Badge Styles:** ✓ Present
- **Alert Dropdown Styles:** ✓ Present
- **Alert Item Styles:** ✓ Present
- **Mobile Responsive:** ✓ Media queries added
- **Issue:** Removed duplicate `.alert-badge` CSS definition

---

### 7. **JAVASCRIPT LAYER**

#### Layout JS (`public/js/layout.js`, Lines 169-285)

**Global Functions (Exposed to window object):**
1. `window.loadAlerts()` - Fetches alerts from API
2. `window.updateAlertBadge(count)` - Updates badge number
3. `window.populateAlertDropdown(alerts)` - Renders dropdown HTML
4. `window.escapeHtml(text)` - XSS prevention

**Event Listeners:**
- Bell click: Toggles dropdown, calls loadAlerts()
- Document click: Closes dropdown
- Dropdown click: Prevents bubble (keeps dropdown open)
- Page load: Calls loadAlerts()
- Auto-refresh: Every 30 seconds calls getCount endpoint

**API Calls:**
- `?url=alert/getAlerts` - Full alert data on demand
- `?url=alert/getCount` - Badge count only for auto-refresh

---

## 🚨 ISSUES IDENTIFIED

### Issue #1: Alert Controller Not Protected Properly
**Severity:** HIGH
**Location:** `core/Router.php` (Lines 19-26)
**Problem:**
- Alert controller requires 'Admin' or 'Manager' role
- If logged-in user is 'Employee', gets 403 Forbidden
- API returns 403 instead of JSON error
- JavaScript receives HTML error page instead of JSON

**Current Code:**
```php
$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['Admin', 'Manager'], true)) {
    http_response_code(403);
    die('Forbidden');
}
```

**Solution:** Alert controller should be accessible to authenticated users regardless of role

---

### Issue #2: API Returns HTML on 403 Error
**Severity:** HIGH
**Location:** `core/Router.php` + `AlertController.php`
**Problem:**
- When 403 error occurs in Router, it outputs plain text "Forbidden"
- JavaScript expects JSON
- JSON.parse() fails on HTML response
- Dropdown shows "Error loading alerts" message

**Evidence:**
- JavaScript tries `response.json()` after 403
- But response body is "Forbidden" (plain text)
- JSON parse error occurs silently

---

### Issue #3: Session Data Not Persisted
**Severity:** CRITICAL
**Location:** `config/config.php`
**Problem:**
- Session might not be started
- Session might expire
- Session data might be incomplete

---

### Issue #4: Database Not Imported
**Severity:** CRITICAL
**Location:** Database connection
**Problem:**
- SQL file contains schema but might not be imported
- Products table might not exist in database
- All queries would fail silently

---

### Issue #5: Model Inheritance Issue
**Severity:** MEDIUM
**Location:** `app/models/Alert.php` + `core/Model.php`
**Problem:**
- Alert model inherits from Model
- Model base class must provide `$this->conn`
- If base class not working properly, queries fail

---

## ✅ VERIFICATION CHECKLIST

Need to verify:
- [ ] Database `ims_db` exists
- [ ] Table `products` exists
- [ ] Table `products` has 10 sample records
- [ ] Session is started
- [ ] `$_SESSION['username']` is set
- [ ] `$_SESSION['role']` is set to 'Admin' or 'Manager'
- [ ] `core/Model.php` provides `$this->conn`
- [ ] Database connection is working
- [ ] API endpoint returns valid JSON
- [ ] JavaScript console has no errors
- [ ] Browser DevTools Network tab shows successful API calls

---

## 📋 TESTING PLAN

### Test 1: Database Connectivity
```php
// Check if products table exists and has data
// Check row count
// Check data format
```

### Test 2: Model Functionality
```php
// Create Alert model
// Call getAll()
// Call getCount()
// Verify prepared statements work
```

### Test 3: Controller Functionality
```php
// Call alert/getAlerts endpoint
// Call alert/getCount endpoint
// Check JSON response format
// Check status codes
```

### Test 4: Frontend Integration
```javascript
// Check if JavaScript functions exist
// Check if event listeners attached
// Check if fetch calls work
// Check if JSON parsing works
// Check if DOM updates work
```

### Test 5: End-to-End Flow
```
1. User logs in
2. Navigate to any page with layout.php
3. Check if bell icon loads
4. Check if badge shows correct count
5. Click bell - dropdown appears
6. Check if dropdown shows alert items
7. Click outside - dropdown closes
8. Wait 30 seconds - badge auto-updates
```

---

## 🔧 FIXES TO APPLY

### Fix #1: Allow Alert Controller for All Authenticated Users
**File:** `core/Router.php`
**Change:** Make 'alert' controller accessible to all authenticated users (not just Admin/Manager)

### Fix #2: Proper Error Handling in Router
**File:** `core/Router.php`
**Change:** Return JSON error responses for AJAX requests

### Fix #3: Ensure Database is Imported
**Action:** Import `database/ims_database.sql` into MySQL

### Fix #4: Verify Session Configuration
**File:** `config/config.php`
**Change:** Ensure `session_start()` is called and works properly

### Fix #5: Add Debugging Output
**Action:** Create diagnostic endpoints to test each layer

---

## 📊 CURRENT STATE (Based on Screenshot)

- **Bell Icon:** Visible ✓
- **Badge Count:** Shows "0" ❌
- **Expected Count:** Should show "7" (7 products with alerts)
- **User Status:** Logged in as "Admin" ✓

**Root Cause Analysis:**
The badge shows "0" because:
1. **Most Likely:** Database not imported (products table empty)
2. **Also Possible:** API returning 403 error (role checking)
3. **Also Possible:** JavaScript not running (console errors)
4. **Also Possible:** Session not set properly

---

