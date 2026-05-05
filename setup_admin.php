<?php
// Setup admin user
require_once __DIR__ . '/config/database.php';

$username = 'admin';
$password = 'password123';
$email = 'admin@ims.local';
$role = 'SuperAdmin';

// Hash the password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Check if admin user already exists
$check_sql = "SELECT User_ID FROM users WHERE Username = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    echo "Admin user already exists. Updating password...\n";
    $update_sql = "UPDATE users SET Password = ?, Role = ? WHERE Username = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sss", $password_hash, $role, $username);
    if ($update_stmt->execute()) {
        echo "✓ Admin user password updated\n";
    } else {
        echo "✗ Failed to update admin user: " . $update_stmt->error . "\n";
    }
} else {
    echo "Creating new admin user...\n";
    $insert_sql = "INSERT INTO users (Username, Password, Email, Role) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ssss", $username, $password_hash, $email, $role);
    if ($insert_stmt->execute()) {
        echo "✓ Admin user created successfully\n";
        echo "  Username: $username\n";
        echo "  Password: $password\n";
        echo "  Email: $email\n";
        echo "  Role: $role\n";
    } else {
        echo "✗ Failed to create admin user: " . $insert_stmt->error . "\n";
    }
}

$conn->close();
?>
