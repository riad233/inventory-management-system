# Inventory Management System (IMS)

A comprehensive PHP-based MVC framework application for managing inventory, assets, assignments, maintenance records, and equipment requests.

## Features

- **User Authentication**: Secure login system for users
- **Asset Management**: Add, edit, view, and delete assets with detailed information
- **Asset Assignment**: Assign assets to employees and track returns
- **Maintenance Tracking**: Record and manage asset maintenance 
- **Vendor Management**: Maintain vendor information and contact details
- **Equipment Requests**: Employees can request equipment; admins can approve/reject
- **Dashboard**: Real-time statistics and overview of system activities
- **Responsive Design**: Bootstrap-based responsive UI

## Project Structure

```
i_m_s/
├── app/
│   ├── controllers/       # Application controllers
│   ├── models/           # Database models
│   └── views/            # View files (UI)
├── config/               # Configuration files
├── core/                 # Core framework files
├── database/             # Database schema
├── public/               # Public-facing files (CSS, JS)
├── routes/               # Routing configuration
├── storage/              # Logs and temporary files
└── README.md            # This file
```

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL/MariaDB
- Apache web server (or similar)

### Setup Steps

1. **Clone/Download the Project**
   ```bash
   # Extract the project files to your web root
   # Usually in: C:\xampp\htdocs\ or /var/www/html/
   ```

2. **Create Database**
   ```bash
   # Open phpMyAdmin or MySQL command line
   # Create a new database named 'ims_db'
   CREATE DATABASE ims_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import Database Schema**
   ```bash
   # Import the database schema
   # File: database/ims_database.sql
   mysql -u root -p riad23 ims_db < database/ims_database.sql
   ```

4. **Create Default User (Optional)**
   ```sql
   INSERT INTO users (Username, Password, Email, Role) 
   VALUES ('admin', 'password123', 'admin@example.com', 'Admin');
   ```

5. **Database Connection**
   - Host: `localhost`
   - User: `root`
   - Password: `riad23`
   - Database: `ims_db`
   - File: `config/database.php` (already configured)

6. **Access the Application**
   - Open browser: `http://localhost/i_m_s/public/`
   - Login with credentials created in step 4

## Usage

### Dashboard
- View summary statistics
- See recent assets and assignments
- Quick access to all modules

### Assets Management
- **Add Asset**: Click "Assets" → "Add Asset" to create new asset
- **View Assets**: View all assets in the system
- **Edit Asset**: Update asset details
- **Delete Asset**: Remove assets from system

### Assignment Management
- **Assign Asset**: Assign assets to employees with return dates
- **Return Asset**: Mark assets as returned with condition status
- **View Assignments**: Track all asset assignments and returns

### Maintenance
- **Record Maintenance**: Log maintenance issues for assets
- **Update Status**: Change maintenance status and end dates
- **Track Costs**: Record maintenance costs

### Vendors
- **Add Vendor**: Create new supplier/vendor records
- **Edit Vendor**: Update vendor information
- **Delete Vendor**: Remove vendors

### Equipment Requests
- **Submit Request**: Employees request new equipment
- **Approve/Reject**: Admins can approve or reject requests
- **Track Status**: Monitor request status

## Database Schema

### Tables
- `users`: User login and role information
- `asset`: Asset inventory records
- `assignment`: Asset to employee assignments
- `employee`: Employee information
- `maintenance`: Asset maintenance records
- `vendor`: Supplier/vendor information
- `equipment_request`: Equipment request records
- `department`: Department information
- `disposal`: Asset disposal records
- `purchase`: Asset purchase records

## Security Considerations

⚠️ **Important**: This application currently uses basic security. For production:
1. Use prepared statements with parameterized queries (protect against SQL injection)
2. Hash passwords with bcrypt or similar
3. Implement CSRF tokens
4. Use HTTPS/SSL
5. Add input validation and sanitization
6. Implement rate limiting
7. Add user roles and permissions

## Technologies Used

- **Backend**: PHP 8.2+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5
- **Icons**: Font Awesome 6.0
- **Architecture**: MVC Pattern

## File Descriptions

### Key Files
- `public/index.php`: Application entry point
- `routes/web.php`: Route configuration
- `core/Router.php`: URL routing logic
- `core/Controller.php`: Base controller class
- `core/Model.php`: Base model class
- `config/database.php`: Database connection
- `config/config.php`: Application configuration

## Troubleshooting

### Database Connection Error
- Check MySQL is running
- Verify database credentials in `config/database.php`
- Ensure database 'ims' exists

### Page Not Found
- Check file paths are correct
- Verify URI routing in routes/web.php
- Check controller and method names

### CSS/JS Not Loading
- Verify public folder is accessible
- Check file paths in HTML files
- Hard refresh browser (Ctrl+Shift+R)

## Future Enhancements

- [ ] User role-based access control
- [ ] Report generation and export
- [ ] Asset barcode scanning
- [ ] Email notifications
- [ ] Multi-language support
- [ ] Advanced search and filtering
- [ ] Asset depreciation tracking
- [ ] API endpoints

## Support

For issues or questions:
1. Check the database table structures
2. Review error logs in `storage/logs/`
3. Verify file permissions
4. Check PHP error display is enabled

## License

This project is provided as-is for educational and business purposes.

## Author

Inventory Management System v1.0

---

**Last Updated**: April 2026

