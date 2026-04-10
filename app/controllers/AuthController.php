<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";

class AuthController extends Controller {
    public function login() {
        $error = null;
        
        if(isset($_POST['login'])) {
            global $conn;
            $username = $_POST['username'];
            $password = $_POST['password'];

            $query = "SELECT * FROM users WHERE Username='$username' AND Password='$password'";
            $result = mysqli_query($conn, $query);

            if(mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user['User_ID'];
                $_SESSION['role'] = $user['Role'];
                header("Location: ?url=dashboard/index");
                exit;
            } else {
                $error = "Invalid Login Credentials";
            }
        }
        $this->view('auth/login', ['error' => $error]);
    }

    public function logout() {
        session_destroy();
        header("Location: ?url=auth/login");
        exit;
    }
}
?>
