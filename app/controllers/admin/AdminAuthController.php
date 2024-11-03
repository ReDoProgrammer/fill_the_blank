<?php

require_once 'app/controllers/AuthController.php';

class AdminAuthController extends AuthController
{
    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $this->loginWithRole($username, $password, 'admin');
        } else {
            // Kiểm tra xem admin đã đăng nhập chưa, nếu đã đăng nhập, không cần phải hiển thị lại trang login
            if (!empty($_SESSION['admin_logged_in'])) {
                header('Location: ' . BASE_URL . '/admin');
                exit;
            }
            $this->loginView('admin');
        }
    }

    public function logout($role = 'admin')
    {
        parent::logout($role);
    }
}
