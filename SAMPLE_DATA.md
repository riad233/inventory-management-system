# Sample Data for IMS

## Insert Test Users
```sql
INSERT INTO users (User_ID, Username, Password, Email, Role, Created_Date) VALUES
(1, 'admin', 'admin123', 'admin@ims.local', 'Admin', NOW()),
(2, 'manager', 'manager123', 'manager@ims.local', 'Manager', NOW());
```

## Insert Departments
```sql
INSERT INTO department (Department_ID, Department_Name, Location) VALUES
(1, 'IT', 'Ground Floor'),
(2, 'Finance', 'First Floor'),
(3, 'HR', 'First Floor'),
(4, 'Operations', 'Ground Floor');
```

## Insert Employees
```sql
INSERT INTO employee (User_ID, Name, Designation, Contact_Number, Email, Department_ID) VALUES
(3, 'John Doe', 'IT Manager', '1234567890', 'john@company.com', 1),
(4, 'Jane Smith', 'Finance Officer', '0987654321', 'jane@company.com', 2);
```

## Insert Vendors
```sql
INSERT INTO vendor (Vendor_ID, Vendor_Name, Contact_Person, Contact_Number, Email, Address) VALUES
(1, 'Tech Solutions Ltd', 'Mr. Khan', '1111111111', 'info@techsol.com', '123 Tech Street'),
(2, 'Office Supplies Inc', 'Ms. Ahmed', '2222222222', 'sales@officesup.com', '456 Supply Road');
```

## Insert Sample Assets
```sql
INSERT INTO asset (Asset_ID, Asset_Name, Category, Brand, Model, Serial_Number, Purchase_Date, Warranty_Expiry, Status) VALUES
(1, 'Dell Laptop', 'Computer', 'Dell', 'Inspiron 15', 'DL123456', '2023-01-15', '2025-01-15', 'Available'),
(2, 'HP Printer', 'Equipment', 'HP', 'LaserJet Pro', 'HP987654', '2022-06-20', '2024-06-20', 'In Use'),
(3, 'Office Chair', 'Furniture', 'Herman Miller', 'Aeron', 'HM456789', '2023-03-10', '2026-03-10', 'Available');
```

## Insert Sample Assignment
```sql
INSERT INTO assignment (Assignment_ID, Asset_ID, User_ID, Department_ID, Assigned_Date, Expected_Return_Date, Actual_Return_Date, Condition_on_Return) VALUES
(1, 1, 3, 1, '2024-01-01', '2025-01-01', NULL, NULL);
```

## Insert Sample Maintenance
```sql
INSERT INTO maintenance (Maintenance_ID, Asset_ID, Reported_Date, Repair_Start_Date, Repair_End_Date, Status, Cost) VALUES
(1, 2, '2024-03-15', '2024-03-16', NULL, 'Pending', 150.00);
```

## Insert Sample Equipment Request
```sql
INSERT INTO equipment_request (Request_ID, User_ID, Equipment_Type, Description, Request_Date, Status, Approval_Date, Approved_By) VALUES
(1, 3, 'Computer', 'Need new laptop for development', '2024-03-20', 'Pending', NULL, NULL);
```

---

Run these SQL commands in phpMyAdmin or MySQL command line to populate test data.
