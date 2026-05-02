<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/config/validator.php';
require_once ROOT_PATH . '/config/logger.php';
require_once ROOT_PATH . '/config/authorization.php';

class SuperAdminController extends Controller {

    /** Guard: Only SuperAdmin may use this controller. */
    private function guard(): void {
        if (!AuthorizationHelper::isSuperAdmin()) {
            http_response_code(403);
            $view = 'errors/403';
            $data = ['title' => '403 – Access Denied'];
            require ROOT_PATH . '/app/views/layout.php';
            exit;
        }
    }

    // ── Dashboard ────────────────────────────────────────────────────
    public function index(): void {
        $this->guard();

        $userModel = $this->model('User');
        $permModel = $this->model('Permission');

        $allUsers  = $userModel->getAll() ?? [];
        $roleCount = [];
        foreach ($allUsers as $u) {
            $r = $u['Role'] ?? 'Unknown';
            $roleCount[$r] = ($roleCount[$r] ?? 0) + 1;
        }

        $data = [
            'title'      => 'SuperAdmin Panel',
            'total_users' => count($allUsers),
            'role_counts' => $roleCount,
            'total_perms' => count($permModel->getAllKeys()),
        ];

        $this->view('superadmin/index', $data);
    }

    // ── Permission Matrix ────────────────────────────────────────────
    public function permissions(): void {
        $this->guard();

        $permModel  = $this->model('Permission');
        $success    = null;
        $error      = null;

        // The editable roles (not SuperAdmin – it always has everything)
        $editableRoles = ['Admin', 'Manager', 'Employee'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf();

            foreach ($editableRoles as $role) {
                // Posted checkboxes: perms[Admin][] = 'asset.view', etc.
                $keys = isset($_POST['perms'][$role]) && is_array($_POST['perms'][$role])
                    ? array_values($_POST['perms'][$role])
                    : [];

                // Validate: every submitted key must be a known permission
                $validKeys = $permModel->getAllKeys();
                $keys = array_filter($keys, fn($k) => in_array($k, $validKeys, true));

                if (!$permModel->setRolePermissions($role, array_values($keys))) {
                    $error = "Failed to save permissions for $role. Check logs.";
                    break;
                }
            }

            if (!$error) {
                $success = 'Permissions saved successfully.';
                Logger::info('SuperAdmin updated permissions', [
                    'by' => $_SESSION['username'] ?? 'unknown',
                ]);
            }
        }

        $data = [
            'title'          => 'Manage Permissions',
            'grouped'        => $permModel->getAllGrouped(),
            'rolePerms'      => $permModel->getAllRolePermissions(),
            'editableRoles'  => $editableRoles,
            'success'        => $success,
            'error'          => $error,
        ];

        $this->view('superadmin/permissions', $data);
    }

    // ── User List ────────────────────────────────────────────────────
    public function users(): void {
        $this->guard();

        $userModel = $this->model('User');
        $users     = $userModel->getAll() ?? [];

        $data = [
            'title' => 'User Management',
            'users' => $users,
            'roles' => ['SuperAdmin', 'Admin', 'Manager', 'Employee'],
        ];

        $this->view('superadmin/users', $data);
    }

    // ── Add User ─────────────────────────────────────────────────────
    public function addUser(): void {
        $this->guard();

        $errors = [];

        if (isset($_POST['submit'])) {
            require_csrf();

            $username = trim($_POST['username'] ?? '');
            $email    = trim($_POST['email']    ?? '');
            $password = $_POST['password']         ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';
            $role     = $_POST['role']             ?? 'Employee';

            // Validate
            if (strlen($username) < 3)                    $errors['username'] = 'Username must be at least 3 characters';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email']  = 'Valid email required';
            if (strlen($password) < 6)                    $errors['password'] = 'Password must be at least 6 characters';
            if ($password !== $confirm)                   $errors['confirm_password'] = 'Passwords do not match';
            if (!in_array($role, ['SuperAdmin','Admin','Manager','Employee'], true))
                                                          $errors['role']     = 'Invalid role selected';

            if (empty($errors)) {
                try {
                    $userModel = $this->model('User');
                    $userModel->create([
                        'username' => Validator::sanitizeString($username),
                        'email'    => Validator::sanitizeEmail($email),
                        'password' => $password,
                        'role'     => $role,
                    ]);
                    Logger::info('SuperAdmin created user', [
                        'by'       => $_SESSION['username'],
                        'username' => $username,
                        'role'     => $role,
                    ]);
                    header('Location: ?url=superadmin/users&msg=User created successfully');
                    exit;
                } catch (Exception $e) {
                    Logger::error('SuperAdmin::addUser error', ['error' => $e->getMessage()]);
                    $errors['general'] = 'Failed to create user: ' . $e->getMessage();
                }
            }
        }

        $this->view('superadmin/add_user', [
            'title'  => 'Add User',
            'errors' => $errors,
            'roles'  => ['SuperAdmin', 'Admin', 'Manager', 'Employee'],
        ]);
    }

    // ── Edit User ────────────────────────────────────────────────────
    public function editUser(int|string $id): void {
        $this->guard();

        $id = (int)$id;
        if ($id < 1) { http_response_code(400); die('Invalid ID'); }

        $userModel = $this->model('User');
        $user      = $userModel->getById($id);
        if (!$user) { http_response_code(404); die('User not found'); }

        // Prevent SuperAdmin from accidentally demoting themselves
        // if there would be no SuperAdmin left – basic safety
        $errors = [];

        if (isset($_POST['submit'])) {
            require_csrf();

            $username = Validator::sanitizeString(trim($_POST['username'] ?? ''));
            $email    = Validator::sanitizeEmail(trim($_POST['email']    ?? ''));
            $role     = $_POST['role'] ?? $user['Role'];
            $newPass  = $_POST['new_password']     ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';

            if (strlen($username) < 3) $errors['username'] = 'Username must be at least 3 characters';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required';
            if (!in_array($role, ['SuperAdmin','Admin','Manager','Employee'], true))
                $errors['role'] = 'Invalid role';

            if ($newPass !== '' && strlen($newPass) < 6)
                $errors['new_password'] = 'New password must be at least 6 characters';
            if ($newPass !== '' && $newPass !== $confirm)
                $errors['confirm_password'] = 'Passwords do not match';

            if (empty($errors)) {
                try {
                    $userModel->update($id, [
                        'username' => $username,
                        'email'    => $email,
                        'role'     => $role,
                    ]);
                    if ($newPass !== '') {
                        $userModel->updatePassword($id, $newPass);
                    }
                    Logger::info('SuperAdmin edited user', [
                        'by'      => $_SESSION['username'],
                        'user_id' => $id,
                    ]);
                    header('Location: ?url=superadmin/users&msg=User updated successfully');
                    exit;
                } catch (Exception $e) {
                    Logger::error('SuperAdmin::editUser error', ['error' => $e->getMessage()]);
                    $errors['general'] = 'Failed to update user: ' . $e->getMessage();
                }
            }
        }

        $this->view('superadmin/edit_user', [
            'title'  => 'Edit User',
            'user'   => $user,
            'errors' => $errors,
            'roles'  => ['SuperAdmin', 'Admin', 'Manager', 'Employee'],
        ]);
    }

    // ── Delete User ──────────────────────────────────────────────────
    public function deleteUser(int|string $id): void {
        $this->guard();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
        require_csrf();

        $id = (int)$id;
        if ($id < 1) { http_response_code(400); die('Invalid ID'); }

        // Prevent deleting own account
        if ((int)($_SESSION['user_id'] ?? 0) === $id) {
            header('Location: ?url=superadmin/users&msg=Cannot delete your own account');
            exit;
        }

        $userModel = $this->model('User');
        if ($userModel->delete($id)) {
            Logger::info('SuperAdmin deleted user', [
                'by' => $_SESSION['username'], 'user_id' => $id,
            ]);
            header('Location: ?url=superadmin/users&msg=User deleted');
        } else {
            header('Location: ?url=superadmin/users&msg=Failed to delete user');
        }
        exit;
    }
}
