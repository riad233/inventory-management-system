<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";

class AuthController extends Controller {
    public function login() {
        global $conn;
        $error = null;
        
        if(isset($_POST['login'])) {
            require_csrf();
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $userModel = $this->model('User');
            $user = $userModel->login($username);

            if($user && password_verify($password, $user['Password'])) {
                $_SESSION['username'] = $user['Username'];
                $_SESSION['user_id'] = $user['User_ID'];
                $_SESSION['role'] = $user['Role'];
                header("Location: ?url=dashboard/index");
                exit;
            }

            $error = "Invalid Login Credentials";
        }
        $this->viewPlain('auth/login', ['error' => $error]);
    }

    public function logout() {
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

            if ($new !== $confirm) {
                $error = "New password and confirmation do not match";
            } else {
                $userModel = $this->model('User');
                $userId = $_SESSION['user_id'] ?? 0;
                $user = $userModel->getById($userId);

                if (!$user || !password_verify($current, $user['Password'])) {
                    $error = "Current password is incorrect";
                } elseif ($userModel->updatePassword($userId, $new)) {
                    $success = "Password updated successfully";
                } else {
                    $error = "Failed to update password";
                }
            }
        }

        $this->view('auth/change_password', ['error' => $error, 'success' => $success]);
    }
}
?>
