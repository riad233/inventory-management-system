# IMS API & Function Reference

## Controllers

### AuthController
Manages user authentication and session management.

**Methods:**
- `login()` - Display login form and authenticate users
- `logout()` - Destroy session and redirect to login

**Usage:**
```php
// In routes: ?url=auth/login or ?url=auth/logout
```

---

### DashboardController
Provides dashboard with system statistics and overview.

**Methods:**
- `index()` - Display dashboard with statistics

**Data Passed to View:**
```php
[
    'total_assets' => int,
    'total_assignments' => int,
    'total_pending' => int,
    'total_maintenance' => int,
    'assets' => array,
    'assignments' => array,
    'maintenance' => array
]
```

---

### AssetController
Manages asset CRUD operations.

**Methods:**
- `index()` - List all assets
- `add()` - Show add form and create new asset
- `edit($id)` - Show edit form and update asset
- `delete($id)` - Delete asset

**POST Parameters (add/edit):**
```php
[
    'name' => string,
    'category' => string,
    'brand' => string,
    'model' => string,
    'serial' => string,
    'purchase_date' => date,
    'warranty' => date,
    'status' => string
]
```

---

### AssignmentController
Manages asset assignments to employees.

**Methods:**
- `index()` - List all assignments
- `assign()` - Show form and create assignment
- `returnAsset()` - Handle asset returns
- `delete($id)` - Delete assignment

**POST Parameters (assign):**
```php
[
    'asset_id' => int,
    'user_id' => int,
    'dept_id' => int,
    'exp_return_date' => date
]
```

**POST Parameters (return):**
```php
[
    'assignment_id' => int,
    'condition' => string // 'Good', 'Fair', 'Damaged'
]
```

---

### MaintenanceController
Handles maintenance records for assets.

**Methods:**
- `index()` - List all maintenance records
- `add()` - Show form and create maintenance record
- `updateStatus()` - Update maintenance status
- `delete($id)` - Delete maintenance record

**POST Parameters (add):**
```php
[
    'asset_id' => int,
    'maintenance_status' => string, // 'Pending', 'In Progress', 'Completed'
    'cost' => decimal
]
```

**POST Parameters (updateStatus):**
```php
[
    'maintenance_id' => int,
    'status' => string, // 'Pending', 'In Progress', 'Completed'
    'end_date' => date
]
```

---

### VendorController
Manages vendor/supplier information.

**Methods:**
- `index()` - List all vendors
- `add()` - Show form and create vendor
- `edit($id)` - Show edit form and update vendor
- `delete($id)` - Delete vendor

**POST Parameters (add/edit):**
```php
[
    'vendor_name' => string,
    'contact_person' => string,
    'contact_number' => string,
    'email' => string,
    'address' => string
]
```

---

### RequestController
Handles equipment requests from employees.

**Methods:**
- `index()` - List all requests
- `create()` - Show form and create request
- `approve($id)` - Approve request
- `reject($id)` - Reject request
- `delete($id)` - Delete request

**POST Parameters (create):**
```php
[
    'user_id' => int,
    'equipment_type' => string,
    'description' => string
]
```

---

## Models

### Base Class: Model
All models extend this base class and have access to:
```php
public $conn;  // Database connection
```

---

### User Model
User authentication and management.

**Methods:**
- `login($username, $password)` - Authenticate user
- `getAll()` - Get all users
- `getById($id)` - Get specific user
- `create($data)` - Create new user
- `update($id, $data)` - Update user
- `delete($id)` - Delete user

---

### Asset Model
Asset inventory management.

**Methods:**
- `getAll()` - Get all assets
- `getById($id)` - Get specific asset
- `add($data)` - Add new asset
- `update($id, $data)` - Update asset
- `delete($id)` - Delete asset

---

### Assignment Model
Asset assignment tracking.

**Methods:**
- `getAll()` - Get all assignments with asset and employee names
- `getById($id)` - Get specific assignment
- `create($data)` - Create assignment
- `returnAsset($id, $data)` - Mark asset as returned
- `update($id, $data)` - Update assignment
- `delete($id)` - Delete assignment

---

### Maintenance Model
Asset maintenance records.

**Methods:**
- `getAll()` - Get all maintenance records with asset names
- `getById($id)` - Get specific maintenance record
- `create($data)` - Create maintenance record
  - Requires: asset_id, cost
  - Optional: maintenance_status (defaults to 'Pending')
  - Sets Reported_Date automatically to current date
- `updateStatus($id, $status, $end_date)` - Update maintenance status and end date
- `update($id, $data)` - Update maintenance record
- `delete($id)` - Delete maintenance record

---

### Vendor Model
Vendor management.

**Methods:**
- `getAll()` - Get all vendors
- `getById($id)` - Get specific vendor
- `create($data)` - Create vendor
- `update($id, $data)` - Update vendor
- `delete($id)` - Delete vendor

---

### Employee Model
Employee information management.

**Methods:**
- `getAll()` - Get all employees with department names
- `getById($id)` - Get specific employee
- `create($data)` - Create employee and associated user account
  - Automatically creates a user with default password 'password123'
  - Username is generated from employee name
  - Assigns 'Employee' role to the user
- `update($id, $data)` - Update employee
- `delete($id)` - Delete employee

---

### EquipmentRequest Model
Equipment request handling.

**Methods:**
- `getAll()` - Get all requests with employee names
- `getById($id)` - Get specific request
- `create($data)` - Create request
- `approve($id, $approved_by)` - Approve request
- `reject($id)` - Reject request
- `update($id, $data)` - Update request
- `delete($id)` - Delete request

---

## Database Tables

### users
```sql
- User_ID (int, PK, AI)
- Username (varchar)
- Password (varchar)
- Email (varchar)
- Role (varchar)
- Created_Date (timestamp)
```

### asset
```sql
- Asset_ID (int, PK, AI)
- Asset_Name (varchar)
- Category (varchar)
- Brand (varchar)
- Model (varchar)
- Serial_Number (varchar, UNIQUE)
- Purchase_Date (date)
- Warranty_Expiry (date)
- Status (varchar)
```

### assignment
```sql
- Assignment_ID (int, PK, AI)
- Asset_ID (int, FK)
- User_ID (int, FK)
- Department_ID (int, FK)
- Assigned_Date (date)
- Expected_Return_Date (date)
- Actual_Return_Date (date, nullable)
- Condition_on_Return (varchar, nullable)
```

### And more... (see database/ims_database.sql)

---

## Utility Classes

### DropdownHelper
Centralized dropdown management for consistent form options across the system.

**Location:** `config/dropdown_helper.php`

**Methods:**

#### `DropdownHelper::load()`
Loads dropdown data from JSON file into memory.
```php
$data = DropdownHelper::load();
// Returns: array with all dropdown categories
```

#### `DropdownHelper::get($key)`
Get a specific dropdown array by key.
```php
$departments = DropdownHelper::get('departments');
// Returns: [
//   ['id' => 1, 'name' => 'IT'],
//   ['id' => 2, 'name' => 'Finance'],
//   ...
// ]
```

#### `DropdownHelper::renderOptions($key, $selected = null)`
Render HTML `<option>` elements for a select field.
```php
// Basic usage
<?php echo DropdownHelper::renderOptions('departments'); ?>

// With pre-selected value
<?php echo DropdownHelper::renderOptions('departments', $emp['Department_ID']); ?>
```

#### `DropdownHelper::getName($key, $id)`
Get the display name of an option by ID.
```php
$name = DropdownHelper::getName('departments', 1);
// Returns: "IT"
```

**Configuration File:** `config/dropdowns.json`

**Available Dropdowns:**
- `departments` - Organizational departments (IT, Finance, HR, Operations)
- `asset_categories` - Asset types (Laptop, Desktop, Printer, Router, UPS, Projector, Scanner)
- `asset_status` - Asset status values (Available, Assigned, Under Repair, Damaged, Disposed)
- `maintenance_status` - Maintenance states (Pending, In Progress, Completed)
- `disposal_reasons` - Asset disposal reasons (End of Life, Damaged, Obsolete, Lost, Replacement)
- `return_conditions` - Asset return condition (Good, Fair, Damaged, Not Working)
- `vendors` - Vendor list (10 vendors)

**Usage Example in Forms:**
```php
<?php require_once __DIR__ . '/../../../config/dropdown_helper.php'; ?>

<select name="dept_id" class="form-control" required>
  <option value="">Select Department</option>
  <?php echo DropdownHelper::renderOptions('departments'); ?>
</select>

<select name="vendor_id" class="form-control">
  <option value="">Select Vendor</option>
  <?php echo DropdownHelper::renderOptions('vendors', $current_vendor_id); ?>
</select>
```

---

## Global Functions

### Router::route($url)
Routes URL to appropriate controller and method.
```php
// Example: ?url=asset/index → AssetController::index()
// Example: ?url=asset/edit/5 → AssetController::edit(5)
```

### Controller::model($modelName)
Loads and returns a model instance.
```php
$assetModel = $this->model('Asset');
$assets = $assetModel->getAll();
```

### Controller::view($viewPath, $data)
Loads and renders a view with data.
```php
$this->view('asset/view_asset', ['assets' => $assets]);
```

---

## URL Routes

| URL Pattern | Maps To | Method |
|-------------|---------|--------|
| `?url=dashboard/index` | DashboardController | index() |
| `?url=asset/index` | AssetController | index() |
| `?url=asset/add` | AssetController | add() |
| `?url=asset/edit/5` | AssetController | edit(5) |
| `?url=asset/delete/5` | AssetController | delete(5) |
| `?url=assignment/index` | AssignmentController | index() |
| `?url=assignment/assign` | AssignmentController | assign() |
| `?url=assignment/returnAsset` | AssignmentController | returnAsset() |
| `?url=employee/index` | EmployeeController | index() |
| `?url=employee/add` | EmployeeController | add() |
| `?url=employee/edit/3` | EmployeeController | edit(3) |
| `?url=maintenance/index` | MaintenanceController | index() |
| `?url=maintenance/add` | MaintenanceController | add() |
| `?url=maintenance/updateStatus` | MaintenanceController | updateStatus() |
| `?url=vendor/index` | VendorController | index() |
| `?url=vendor/add` | VendorController | add() |
| `?url=vendor/edit/5` | VendorController | edit(5) |
| `?url=vendor/delete/5` | VendorController | delete(5) |
| `?url=request/index` | RequestController | index() |
| `?url=request/create` | RequestController | create() |
| `?url=auth/login` | AuthController | login() |
| `?url=auth/logout` | AuthController | logout() |

---

**Note**: All URL parameters are passed as function parameters. For data modification, use POST parameters.

**Version**: 1.1 | **Last Updated**: April 13, 2026
**Latest Updates**: Added DropdownHelper class, vendor dropdown support, employee auto-user creation
