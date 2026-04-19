<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/config/validator.php";
require_once ROOT_PATH . "/config/logger.php";

class AuthController extends Controller {
    public function login() {
        global $conn;
        $error = null;
        
        if(isset($_POST['login'])) {
            require_csrf();
            $username = Validator::sanitizeString($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username)) {
                $error = "Username is required";
            } elseif (empty($password)) {
                $error = "Password is required";
            } else {
                try {
                    $userModel = $this->model('User');
                    $user = $userModel->login($username);

                    if($user && password_verify($password, $user['Password'])) {
                        session_regenerate_id(true);
                        $_SESSION['username'] = $user['Username'];
                        $_SESSION['user_id'] = $user['User_ID'];
                        $_SESSION['role'] = $user['Role'];
                        Logger::info("User login", ['username' => $username, 'role' => $user['Role']]);
                        header("Location: ?url=dashboard/index");
                        exit;
                    }

                    $error = "Invalid login credentials";
                    Logger::warning("Failed login attempt", ['username' => $username]);
                } catch (Exception $e) {
                    Logger::error("Login error", ['error' => $e->getMessage()]);
                    $error = "An error occurred during login";
                }
            }
        }
        $this->viewPlain('auth/login', ['error' => $error]);
    }

    public function logout() {
        $username = $_SESSION['username'] ?? 'unknown';
        Logger::info("User logout", ['username' => $username]);
        session_destroy();
        header("Location: ?url=home/index");
        exit;
    }

    public function changePassword() {
        $error = null;
        $success = null;

        if (isset($_POST['submit'])) {
            require_csrf();
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            // Validate inputs
            Validator::reset();
            Validator::required('current_password', $current, 'Current Password');
            Validator::required('new_password', $new, 'New Password');
            Validator::required('confirm_password', $confirm, 'Confirm Password');
            Validator::minLength('new_password', $new, 8, 'New Password');
            
            if (!Validator::passes()) {
                $error = implode('; ', Validator::getErrors());
            } elseif ($new !== $confirm) {
                $error = "New password and confirmation do not match";
            } else {
                try {
                    $userModel = $this->model('User');
                    $userId = $_SESSION['user_id'] ?? 0;
                    $user = $userModel->getById($userId);

                    if (!$user || !password_verify($current, $user['Password'])) {
                        $error = "Current password is incorrect";
                        Logger::warning("Invalid current password attempt", ['user_id' => $userId]);
                    } elseif (password_verify($new, $user['Password'])) {
                        $error = "New password must be different from current password";
                    } elseif ($userModel->updatePassword($userId, $new)) {
                        $success = "Password updated successfully";
                        Logger::info("Password changed", ['user_id' => $userId]);
                    } else {
                        $error = "Failed to update password";
                        Logger::error("Password update failed", ['user_id' => $userId]);
                    }
                } catch (Exception $e) {
                    Logger::error("Change password error", ['user_id' => $_SESSION['user_id'] ?? 0, 'error' => $e->getMessage()]);
                    $error = "An error occurred";
                }
            }
        }

        $this->view('auth/change_password', ['error' => $error, 'success' => $success]);
    }
}
?>
