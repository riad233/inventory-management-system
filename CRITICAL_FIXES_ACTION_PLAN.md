# 🚨 CRITICAL ISSUES - ACTION PLAN
**Priority:** MUST FIX BEFORE PRODUCTION  
**Estimated Time:** 2 hours total  
**Risk Level:** HIGH (CVSS scores 7.5 - 8.6)

---

## 📌 EXECUTIVE SUMMARY

Your IMS has **3 critical security issues** that must be fixed before production deployment. Each is straightforward to fix:

| # | Issue | Time | Risk | Status |
|---|-------|------|------|--------|
| 1 | CSRF Token Missing from Router | 30 min | 🔴 Critical | ❌ NOT FIXED |
| 2 | Cache Files World-Readable | 10 min | 🔴 Critical | ❌ NOT FIXED |
| 3 | SQL Injection in ORDER BY | 1 hour | 🔴 High | ⚠️ PARTIAL |

**Total Time to Fix:** 2 hours  
**Complexity:** LOW (mostly configuration changes)

---

## 🔴 ISSUE #1: CSRF TOKEN NOT VALIDATED (30 minutes)

### Problem Description
**Location:** `core/Router.php`  
**Severity:** CRITICAL (CVSS 8.1)  
**Impact:** All POST requests vulnerable to Cross-Site Request Forgery attacks

### Current Behavior
```php
// Router.php currently:
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}
// Routes to controller
```

**Problem:** Only checks if user is logged in, NOT if request is legitimate

### Attack Scenario
```
1. Attacker creates malicious website
2. Victim visits while logged into IMS
3. Malicious site sends: POST /delete-asset with asset_id=123
4. Browser automatically includes session cookie
5. Asset deleted without victim's knowledge ❌
```

### Solution

**Step 1: Verify config.php generates tokens correctly**
```php
// config/config.php - ALREADY HAS THIS:
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
```

**Step 2: Update Router.php to validate tokens**

Add this in `core/Router.php` before `$controllerFile = ...`:

```php
// Add CSRF validation for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // For JSON requests, check header
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    
    if (empty($token) || !isset($_SESSION['csrf_token'])) {
        http_response_code(403);
        die("CSRF token validation failed");
    }
    
    // Use hash_equals for timing-attack resistance
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die("CSRF token validation failed");
    }
    
    // Regenerate token after validation (security best practice)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
```

**Step 3: Ensure all POST forms include token**

In all form views (already partially done):
```php
<!-- In every form that POSTs data -->
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
    <!-- other form fields -->
</form>
```

### Verification
- [ ] Add CSRF validation code to Router.php
- [ ] Test: Submit form → should work
- [ ] Test: Remove token from form → should fail
- [ ] Test: Use wrong token → should fail
- [ ] Test: Token should regenerate after valid POST

### Files to Modify
- `core/Router.php` - Add validation logic

### Expected Outcome
✅ All POST requests now require valid CSRF token  
✅ Eliminates CSRF vulnerability  
✅ No breaking changes to user workflows

---

## 🔴 ISSUE #2: CACHE FILES WORLD-READABLE (10 minutes)

### Problem Description
**Location:** `config/cache.php` line 21  
**Severity:** HIGH (CVSS 7.5)  
**Impact:** Sensitive cached data readable by other users on server

### Current Behavior
```php
// config/cache.php currently:
@mkdir(self::$cacheDir, 0755, true);  // ❌ WORLD READABLE
```

**Problem:** 0755 permissions mean any user can read cache files  
**Contains:** User data, session tokens, database results

### Attack Scenario
```
1. Attacker gains shell access to server
2. Attacker reads storage/cache/ directory
3. Cache contains: user names, emails, salaries, passwords hashes
4. Attacker extracts sensitive information ❌
```

### Solution

**Change single line in `config/cache.php`:**

```php
// BEFORE (UNSAFE):
@mkdir(self::$cacheDir, 0755, true);

// AFTER (SAFE):
@mkdir(self::$cacheDir, 0700, true);  // Owner read/write/execute only
```

### Permission Meanings
```
0755 = rwxr-xr-x  (Owner: RWX, Group: RX, Other: RX) ❌ DANGEROUS
0700 = rwx------ (Owner: RWX, Group: —, Other: —)   ✅ SECURE
```

### Verification
```bash
# After fix, verify permissions:
ls -la storage/cache/
# Should show: drwx------ (not drwxr-xr-x)
```

### Files to Modify
- `config/cache.php` - Line 21 (change 0755 to 0700)

### Expected Outcome
✅ Cache directory only readable by application owner  
✅ Sensitive data protected from other users  
✅ No performance impact

---

## 🔴 ISSUE #3: SQL INJECTION IN ORDER BY (1 hour)

### Problem Description
**Location:** `core/Model.php` - paginate() and count methods  
**Severity:** HIGH (CVSS 8.6)  
**Impact:** Advanced SQL queries potentially vulnerable to injection

### Current Behavior
```php
// Model.php currently:
$query = "SELECT * FROM {$this->table} ORDER BY {$order}";
// ❌ Direct string interpolation - SQL injection possible
```

**Problem:** While basic queries use prepared statements, ORDER BY uses direct string  
**Risk:** Only occurs if $order comes from user input or untrusted source

### Attack Scenario
```
1. Attacker provides sort parameter: "name; DROP TABLE assets;--"
2. Query becomes: "ORDER BY name; DROP TABLE assets;--"
3. Database executes DROP TABLE ❌ (depends on database setup)
```

### Solution

**Option A: Allowlist Approach (Recommended)**

```php
// In Model.php, update paginate() method:
public function paginate($page = 1, $perPage = 50, $order = 'id DESC') {
    // Validate/sanitize order parameter
    $allowed_columns = ['id', 'name', 'date', 'status', 'created_at'];
    $allowed_directions = ['ASC', 'DESC'];
    
    // Parse order string (handle "column ASC" format)
    $order_parts = explode(' ', trim($order));
    $order_column = $order_parts[0] ?? 'id';
    $order_dir = $order_parts[1] ?? 'DESC';
    
    // Validate
    if (!in_array($order_column, $allowed_columns)) {
        $order_column = 'id';  // Fallback to safe value
    }
    if (!in_array(strtoupper($order_dir), $allowed_directions)) {
        $order_dir = 'DESC';  // Fallback to safe value
    }
    
    // Use backticks to quote identifiers
    $order = "`{$order_column}` " . strtoupper($order_dir);
    
    // Now safe to use in query:
    $query = "SELECT * FROM `{$this->table}` ORDER BY {$order}";
    // ... rest of code
}
```

**Option B: Parameterization (If database supports it)**

Not all databases support parameterized ORDER BY, so Option A is preferred.

### Implementation Checklist
- [ ] Add allowed_columns array to each model
- [ ] Update paginate() method with validation
- [ ] Update count() if it uses ORDER BY
- [ ] Test: Use valid column name → should work
- [ ] Test: Use invalid column → should fallback to 'id'
- [ ] Test: Attempt SQL injection → should be neutralized

### Files to Modify
- `core/Model.php` - paginate() and count() methods (if applicable)

### Expected Outcome
✅ ORDER BY columns validated against allowlist  
✅ Eliminates SQL injection via sort parameter  
✅ No breaking changes (invalid columns fallback safely)

---

## ✅ FIX IMPLEMENTATION CHECKLIST

### Pre-Implementation
- [ ] Read all three issues above
- [ ] Understand each vulnerability
- [ ] Create backup: `git commit -m "Backup before security fixes"`

### Implementation Phase 1: CSRF (30 minutes)

**File: `core/Router.php`**

- [ ] Locate the route() method
- [ ] Find line: `if (!isset($_SESSION['user_id'])) { die("Unauthorized"); }`
- [ ] Add CSRF validation code after that check
- [ ] Test all POST forms still work
- [ ] Test malicious CSRF attempt is blocked

```php
// Add this after line that checks user_id
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    
    if (empty($token) || !isset($_SESSION['csrf_token'])) {
        http_response_code(403);
        die("CSRF token validation failed");
    }
    
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die("CSRF token validation failed");
    }
    
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
```

### Implementation Phase 2: Cache Permissions (10 minutes)

**File: `config/cache.php`**

- [ ] Find line with: `@mkdir(self::$cacheDir, 0755, true);`
- [ ] Change 0755 to 0700
- [ ] Delete existing cache directory: `rm -rf storage/cache/`
- [ ] Application will recreate with correct permissions
- [ ] Verify: `ls -la storage/cache/` shows drwx------

```php
// Line to change:
@mkdir(self::$cacheDir, 0700, true);  // was 0755
```

### Implementation Phase 3: SQL Injection Prevention (1 hour)

**File: `core/Model.php`**

- [ ] Find paginate() method
- [ ] Add allowed_columns array at top of class or in method
- [ ] Add validation logic before ORDER BY usage
- [ ] Test with valid column names
- [ ] Test with invalid/malicious input
- [ ] Ensure fallback to safe defaults

```php
// In paginate() method, add:
$allowed_columns = ['id', 'name', 'date', 'status', 'created_at'];
$allowed_directions = ['ASC', 'DESC'];

// Parse and validate order
$order_parts = explode(' ', trim($order));
$order_column = in_array($order_parts[0] ?? '', $allowed_columns) ? $order_parts[0] : 'id';
$order_dir = in_array(strtoupper($order_parts[1] ?? ''), $allowed_directions) ? strtoupper($order_parts[1]) : 'DESC';

$order = "`{$order_column}` {$order_dir}";
```

### Post-Implementation Testing

- [ ] Run all CRUD operations
- [ ] Test with real data
- [ ] Check browser console for errors
- [ ] Verify database operations complete
- [ ] Test on different browsers
- [ ] Monitor error logs for issues

### Deployment

- [ ] Create git commit: `git commit -m "Fix CSRF, cache permissions, SQL injection"`
- [ ] Push to repository: `git push`
- [ ] Deploy to production
- [ ] Verify application works
- [ ] Monitor logs for errors

---

## 📊 IMPACT SUMMARY

After fixing these 3 issues:

| Metric | Before | After | Change |
|--------|--------|-------|--------|
| Critical Vulnerabilities | 3 | 0 | ✅ 100% fixed |
| Security Score | 6.3/10 | 8.5/10 | ✅ +2.2 points |
| CSRF Protected | ❌ No | ✅ Yes | ✅ Fixed |
| Cache Secure | ❌ No | ✅ Yes | ✅ Fixed |
| SQL Injection Safe | ⚠️ Partial | ✅ Yes | ✅ Fixed |

---

## 🎯 ESTIMATED TIME BREAKDOWN

```
CSRF Implementation ................ 30 min
  - Code writing: 15 min
  - Testing: 15 min

Cache Permission Fix ............... 10 min
  - Code change: 2 min
  - Testing: 8 min

SQL Injection Prevention ........... 60 min
  - Code review: 15 min
  - Implementation: 30 min
  - Testing: 15 min

Total Time: 2 hours (100 minutes)
```

---

## 🚀 DEPLOYMENT READINESS

### Before Deploying
- [ ] All 3 issues fixed
- [ ] All tests pass
- [ ] No error logs
- [ ] Backup created

### After Deploying
- [ ] Monitor error logs
- [ ] Test all workflows
- [ ] Verify security fixes working
- [ ] Track application performance

---

## ❓ FREQUENTLY ASKED QUESTIONS

**Q: Can I deploy without fixing these issues?**  
A: Not recommended. These are critical security vulnerabilities. Fix first, then deploy.

**Q: Will these fixes break anything?**  
A: No. They only affect security mechanisms, not core functionality.

**Q: How long to test after fixes?**  
A: Approximately 30 minutes for thorough testing of all CRUD operations.

**Q: Do I need to update the database?**  
A: No. These are code-level fixes only. No schema changes required.

**Q: What if I miss applying these fixes?**  
A: Application remains functional but vulnerable to CSRF attacks, data exposure, and potential SQL injection.

---

## 📞 SUPPORT & RESOURCES

For more details on each issue:
- **CSRF Protection:** See COMPREHENSIVE_ANALYSIS_RATING.md - Issue #1
- **Cache Security:** See COMPREHENSIVE_ANALYSIS_RATING.md - Issue #2  
- **SQL Injection:** See COMPREHENSIVE_ANALYSIS_RATING.md - Issue #3

---

**Action Items Version:** 1.0  
**Created:** April 21, 2026  
**Status:** READY FOR IMPLEMENTATION  

**Next Step:** Start with CSRF implementation (largest time savings through early completion)
