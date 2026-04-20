# IMS CLEANUP CHECKLIST - CATEGORIZED
**Date:** April 20, 2026  
**Purpose:** Actionable cleanup guide with clear categorization

---

## CATEGORY A: SAFE TO DELETE ✅
*No dependencies, no runtime impact, verified unused*

### A1. ORPHANED MODEL FILE
```
File: app/models/Request.php
Type: PHP Class
Status: ❌ UNUSED
Reason: 
  - No controller instantiates this model
  - References non-existent database tables (requests, products)
  - Functionality replaced by EquipmentRequest.php
  - Creates naming confusion during development
References: ZERO (verified)
Risk: NONE - No code depends on this
Recommendation: DELETE IMMEDIATELY
Timeline: Can be done now
```

### A2. UNUSED PAGINATION HELPER
```
File: config/paginator.php
Type: PHP Class (Pagination Helper)
Status: ❌ UNUSED
Reason:
  - Defined but never instantiated anywhere
  - Never called in any controller or view
  - Not referenced in any file
References: ZERO (verified)
Risk: NONE - Dead code only
Recommendation: DELETE IMMEDIATELY
Timeline: Can be done now
```

### A3. UNUSED AUTHORIZATION METHODS
```
File: config/authorization.php
Status: ⚠️ PARTIALLY UNUSED (5 methods, 1 used, 4 unused)

Methods to DELETE:
  1. isEmployee() - Line 33 - Never called
  2. requireAdminOrManager() - Line 65 - Redundant with requireAdmin()
  3. isDepartmentManager() - Line 78 - Never called
  4. ownsResource() - Line 97 - Never called

Methods to KEEP:
  ✓ requireAdmin() - Line 54 - Used in AssetController

Risk: NONE - Only unused helper functions
Recommendation: Delete unused methods while keeping requireAdmin()
Timeline: Can be done now
```

### A4. UNUSED VALIDATOR METHODS
```
File: config/validator.php
Status: ⚠️ PARTIALLY UNUSED (15 methods, ~2 unused)

Methods to DELETE:
  1. futureDate() - Line ~180 - Never called
  2. getPostData() - Line ~220 - Never called

Methods to KEEP:
  ✓ positive() - Line ~90 - Used in MaintenanceController (1 use)
  ✓ minLength() - Used in AuthController for password validation
  ✓ All others are actively used for validation

Risk: NONE - Unused utility functions
Recommendation: Delete unused methods
Timeline: Can be done now
```

### A5. UNUSED CSS CLASSES
```
File: public/css/layout.css
Status: ❌ UNUSED STYLES (4 classes)

Classes to DELETE:
  1. .btn-action - Line ~140
  2. .btn-action-edit - Line ~145
  3. .btn-action-delete - Line ~155
  4. .main-content.full-width - Line ~120

Search Result: ZERO matches in any view file

Risk: NONE - Only CSS definitions, not used
Recommendation: Delete unused class definitions
Timeline: Can be done now

Note: Keep all other layout.css classes - they are used
```

---

## CATEGORY B: NEEDS REVIEW ⚠️
*Potentially used indirectly, uncertain, requires manual verification*

### B1. JAVASCRIPT FILES PARTIALLY INTEGRATED
```
Files:
  1. public/js/layout.js
  2. public/js/list-search-init.js

Type: JavaScript
Status: ⚠️ PARTIAL INTEGRATION

Current Usage:
  - Individually included in specific views:
    * app/views/asset/view_asset.php (line 74-75)
    * app/views/assignment/view_assignment.php (line 81-82)
    * app/views/maintenance/view_maintenance.php (line 72-73)
    * app/views/request/view_request.php (line 80-81)
  - NOT included in main layout.php

Issue: Inconsistent script loading - some pages have search, some don't

Risk: MEDIUM - Search functionality might break if removed incorrectly

Options:
  Option 1: Move to layout.php for global availability
  Option 2: Keep individual includes for specific pages only
  Option 3: Document why they're not global

Recommendation: REVIEW & DECIDE
  - If search is needed globally → Move to layout.php
  - If search is page-specific → Document and keep as-is
  - If unused on some pages → Remove from those pages only

Timeline: Requires decision
```

### B2. UNUSED DATABASE TABLES & COLUMNS
```
Status: ⚠️ UNCERTAIN (tables exist, no code uses them)

Unused Tables in ims_database.sql:
  1. disposal - Table defined, no Model, no Controller
  2. purchase - Table defined, no Model, no Controller
  3. stock_transactions - Table defined, no Model, no Controller
  4. vendor_status - Reference table, no usage
  5. products - Product management table, only legacy Request.php references
  6. requests - Request management table, only legacy Request.php references

Unused Columns:
  1. asset.Status - Allows 4 values but code uses only 2
  2. asset.Warranty_Expiry - Stored but never queried
  3. equipment_request.Approval_Date - Stored but never displayed
  4. equipment_request.Approved_By - Stored but never displayed
  5. maintenance.Repair_Start_Date - Never populated

Risk: MEDIUM-HIGH
  - May be planned features (disposal, purchases, stock tracking)
  - May be needed for reports/analytics not visible in code
  - Removing schema is difficult once data exists
  - Some columns might be used by external reporting tools

Recommendation: REVIEW BEFORE DELETION
  1. Check if disposal/purchase features are planned
  2. Verify no external reports depend on these tables
  3. Consult stakeholders before removing schema
  4. Consider keeping with documentation as "Reserved for Future"

Timeline: Requires stakeholder input before cleanup
```

### B3. FORM FIELD DEFINITIONS
```
Status: ⚠️ STORED BUT NOT USED

Fields stored in database but not displayed in UI:
  1. asset.Status - Database: 4 values, UI: 2 values (Active/Inactive only)
  2. equipment_request.Approval_Date - Stored but never shown
  3. equipment_request.Approved_By - Stored but never shown

Risk: MEDIUM
  - May be needed for future approval workflow
  - May be populated but hidden intentionally
  - Removing requires UI changes

Recommendation: REVIEW
  1. Clarify business intent - are these for future use?
  2. If unused and no future plans → Remove from schema and UI
  3. If planned features → Document and keep

Timeline: Requires stakeholder review
```

---

## CATEGORY C: MUST KEEP ✅
*Core system files, dependencies, critical functionality*

### C1. CORE FRAMEWORK FILES
```
Files: ✅ ALL MUST KEEP
  ✓ core/Router.php - URL routing engine (CRITICAL)
  ✓ core/Controller.php - Base controller class (CRITICAL)
  ✓ core/Model.php - Base model class (CRITICAL)

Reason: These form the foundation of the entire MVC framework
Risk: Breaking any of these breaks the entire application
```

### C2. ALL ACTIVE CONTROLLERS
```
Files: ✅ ALL MUST KEEP (9 files)
  ✓ app/controllers/AssetController.php
  ✓ app/controllers/AssignmentController.php
  ✓ app/controllers/AuthController.php
  ✓ app/controllers/DashboardController.php
  ✓ app/controllers/EmployeeController.php
  ✓ app/controllers/HomeController.php
  ✓ app/controllers/MaintenanceController.php
  ✓ app/controllers/RequestController.php
  ✓ app/controllers/VendorController.php

Reason: All are routed in core/Router.php and actively used
Risk: Removing any breaks the corresponding module
```

### C3. ALL ACTIVE MODELS
```
Files: ✅ ALL MUST KEEP (8 files)
  ✓ app/models/Asset.php
  ✓ app/models/Assignment.php
  ✓ app/models/Department.php
  ✓ app/models/Employee.php
  ✓ app/models/EquipmentRequest.php
  ✓ app/models/Maintenance.php
  ✓ app/models/User.php
  ✓ app/models/Vendor.php

Reason: Used by corresponding controllers for database access
Risk: Removing breaks data access for that module
Note: Request.php is NOT included (it's orphaned - see Category A)
```

### C4. ALL CONFIGURATION FILES
```
Files: ✅ ALL MUST KEEP (most)
  ✓ config/config.php - Global configuration (CRITICAL)
  ✓ config/database.php - Database connection (CRITICAL)
  ✓ config/authorization.php - Authorization helpers (KEEP but clean)
  ✓ config/validator.php - Input validation (KEEP but clean)
  ✓ config/logger.php - Error logging (CRITICAL)
  ✓ config/env.php - Environment loader (CRITICAL)
  ✓ config/dropdown_helper.php - Form dropdown data (KEEP)
  ✓ config/.env - Environment variables (CRITICAL)
  ✓ config/.env.example - Configuration template (KEEP)

Files: ❌ CAN DELETE
  ✗ config/paginator.php - Never used (see Category A)

Reason: Configuration is core to application startup and runtime
Risk: Removing critical config breaks application
```

### C5. ALL VIEWS
```
Files: ✅ ALL MUST KEEP
  ✓ app/views/layout.php - Master template (CRITICAL)
  ✓ app/views/auth/login.php - Authentication (CRITICAL)
  ✓ app/views/auth/change_password.php - User settings
  ✓ All dashboard, asset, assignment, employee, maintenance, request, vendor views

Reason: Used by corresponding controllers to render HTML
Risk: Removing any view breaks that page
Note: Some views are duplicated (add/edit pairs) but both are necessary
```

### C6. PUBLIC ASSETS (CSS & JS)
```
Files: ✅ MUST KEEP (most)
  ✓ public/css/layout.css - Main styling (CRITICAL - used in layout.php)
  ✓ public/css/login.css - Login page styling (CRITICAL - used in login.php)
  ✓ public/css/dashboard.css - Dashboard styling (CRITICAL)
  ✓ public/js/table-search.js - Search functionality (KEEP)
  ✓ public/js/list-search-init.js - Search initialization (KEEP)
  ✓ public/js/layout.js - Layout functionality (KEEP but integrate)
  ✓ public/img/ - Logo and images (KEEP)
  ✓ public/uploads/ - User uploads (KEEP)

Cleanup Allowed:
  ✗ Unused CSS classes in layout.css (see Category A)

Risk: Removing CSS/JS breaks styling and functionality
```

### C7. DATABASE FILES
```
Files: ✅ MUST KEEP
  ✓ database/ims_database.sql - Database schema (CRITICAL)

Status: Keep but review
  - Remove or document unused tables (Category B)
  - Update schema version if changes are made

Risk: Removing breaks database setup
```

### C8. ROUTING & ENTRY POINTS
```
Files: ✅ ALL MUST KEEP
  ✓ routes/web.php - Route dispatcher (CRITICAL)
  ✓ public/index.php - Public entry point (CRITICAL)
  ✓ index.php - Root entry point (CRITICAL)
  ✓ public/router.php - Router handler (CRITICAL)

Reason: These are the application entry points
Risk: Removing any breaks application access
```

### C9. ESSENTIAL CONFIG FILES
```
Files: ✅ ALL MUST KEEP
  ✓ .htaccess - Apache configuration
  ✓ .gitignore - Git configuration
  ✓ README.md - Documentation

Risk: Removing breaks deployment and versioning
```

---

## ISSUES FOUND (Not Cleanup, But Important)

### ISSUE 1: Missing Input Validation ⚠️
```
Files Affected:
  1. app/controllers/AssignmentController.php - Line 18
     Method: assign()
     Issue: No validation of POST data before database insert
     Risk: MEDIUM - Invalid data could corrupt database
     Fix: Add Validator calls before $this->model->create()
  
  2. app/controllers/RequestController.php - Line 60
     Method: edit()
     Issue: No validation before database update
     Risk: MEDIUM - Invalid data could corrupt database
     Fix: Add Validator calls before $this->model->update()
  
  3. app/controllers/MaintenanceController.php - Line 60
     Method: updateStatus()
     Issue: POST variable accessed without validation
     Risk: MEDIUM - Invalid status values could break logic
     Fix: Add validation before status update

Classification: NOT CLEANUP (these are bugs/improvements)
Priority: HIGH - Should be fixed for data integrity
Timeline: Should be addressed in Phase 2
```

### ISSUE 2: Code Duplication (Technical Debt)
```
Status: ⚠️ NOT CRITICAL BUT SHOULD REFACTOR

Type 1: Duplicate Sanitization Logic
  Files: AssetController, VendorController, EmployeeController, MaintenanceController
  Pattern: htmlspecialchars() + trim() repeated in add() and edit() methods
  Solution: Create shared method in base Controller class

Type 2: Duplicate CRUD Methods
  Files: All 9 models (Asset, Assignment, Department, Employee, EquipmentRequest, Maintenance, User, Vendor)
  Pattern: getAll(), getById(), create(), update(), delete() repeated
  Solution: Move to base Model class

Type 3: Duplicate Form HTML
  Files: Add/edit view pairs (6 pairs affected)
  Pattern: Same form structure, different action URL
  Solution: Create reusable form partials

Classification: NOT CLEANUP (these are refactoring opportunities)
Priority: MEDIUM - Nice to have but improves maintainability
Timeline: Can be addressed in Phase 3
```

---

## CLEANUP ACTION PLAN

### 🔴 PHASE 1: IMMEDIATE (Safe Deletions - 1-2 hours)

**DELETE THESE FILES:**
- [ ] `app/models/Request.php`
- [ ] `config/paginator.php`

**REMOVE THESE CODE BLOCKS:**
- [ ] Delete `isEmployee()` from `config/authorization.php`
- [ ] Delete `requireAdminOrManager()` from `config/authorization.php`
- [ ] Delete `isDepartmentManager()` from `config/authorization.php`
- [ ] Delete `ownsResource()` from `config/authorization.php`
- [ ] Delete `futureDate()` from `config/validator.php`
- [ ] Delete `getPostData()` from `config/validator.php`
- [ ] Delete unused CSS classes from `public/css/layout.css`

**FIX THESE ISSUES:**
- [ ] Add `<script src="js/table-search.js">` to `app/views/layout.php`
- [ ] Add `<script src="js/list-search-init.js">` to `app/views/layout.php`
- [ ] Add `<script src="js/layout.js">` to `app/views/layout.php`

**RESULT:** ~10 LOC removed, 0 features broken

---

### 🟠 PHASE 2: VALIDATION (Bug Fixes - 2-3 hours)

**ADD VALIDATION TO:**
- [ ] `AssignmentController.assign()` - Add Validator calls
- [ ] `RequestController.edit()` - Add Validator calls
- [ ] `MaintenanceController.updateStatus()` - Add Validator calls

**RESULT:** Improved data integrity, bug fixes

---

### 🟡 PHASE 3: REFACTORING (Optional - 4-6 hours)

**REFACTOR THESE:**
- [ ] Extract duplicate sanitization to `core/Controller.php`
- [ ] Move common CRUD methods to `core/Model.php`
- [ ] Create reusable form components for add/edit views
- [ ] Document and clean up CSS class names

**RESULT:** 15-20% LOC reduction, improved maintainability

---

### 📋 PHASE 4: REVIEW (Manual Steps - 2-4 hours)

**REVIEW AND DECIDE:**
- [ ] Unused database tables (disposal, purchase, stock_transactions)
- [ ] Unused database columns (Status, Warranty_Expiry, etc.)
- [ ] Document future features or remove schema

**RESULT:** Clarified roadmap, documented decisions

---

## RISK MATRIX

| Item | Impact if Removed | Probability | Overall Risk |
|------|-------------------|------------|--------------|
| Request.php | NONE - Orphaned | NONE | ✅ SAFE |
| paginator.php | NONE - Never used | NONE | ✅ SAFE |
| Unused auth methods | NONE - Helper only | NONE | ✅ SAFE |
| Unused validator methods | NONE - Helper only | NONE | ✅ SAFE |
| Unused CSS classes | NONE - Not used | NONE | ✅ SAFE |
| JS file integration | HIGH - Breaks search | MEDIUM | ⚠️ FIX FIRST |
| Unused DB tables | MEDIUM - May affect reports | LOW | ⚠️ REVIEW |
| Unused DB columns | MEDIUM - May affect logic | LOW | ⚠️ REVIEW |

---

## SUMMARY

| Category | Count | Action | Risk | Timeline |
|----------|-------|--------|------|----------|
| Safe to Delete | 7 items | DELETE NOW | ✅ NONE | 1-2 hrs |
| Needs Review | 3 issues | REVIEW & DECIDE | ⚠️ MEDIUM | 2-4 hrs |
| Must Keep | 80+ items | PROTECT | ✅ NONE | N/A |
| Validation Issues | 3 items | ADD VALIDATION | ⚠️ MEDIUM | 2-3 hrs |
| Refactoring Opps | 4 areas | IMPROVE | ⚠️ MEDIUM | 4-6 hrs |

**Total Cleanup Time:** 11-19 hours (depending on phases)  
**Recommended Approach:** Do Phase 1 immediately, plan Phases 2-4 for next sprint

---

**Analysis Status:** ✅ COMPLETE  
**Ready for:** Implementation  
**Date:** April 20, 2026
