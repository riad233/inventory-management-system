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
- `getAll()` - Get all maintenance records
- `getById($id)` - Get specific maintenance record
- `create($data)` - Create maintenance record
- `updateStatus($id, $status, $end_date)` - Update status
- `update($id, $data)` - Update record
- `delete($id)` - Delete record

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
- `getAll()` - Get all employees
- `getById($id)` - Get specific employee
- `create($data)` - Create employee
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
| `?url=auth/login` | AuthController | login() |
| `?url=auth/logout` | AuthController | logout() |

---

**Note**: All URL parameters are passed as function parameters. For data modification, use POST parameters.

**Version**: 1.0 | **Last Updated**: April 2026
