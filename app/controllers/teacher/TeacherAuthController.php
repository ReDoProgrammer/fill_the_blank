<?php

require_once 'app/controllers/AuthController.php';

class TeacherAuthController extends AuthController
{
    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $this->loginWithRole($username, $password, 'teacher');
        } else {
            // Kiểm tra xem teacher đã đăng nhập chưa, nếu đã đăng nhập, không cần phải hiển thị lại trang login
            if (!empty($_SESSION['teacher_logged_in'])) {
                header('Location: ' . BASE_URL . '/teacher');
                exit;
            }
            $this->loginView('teacher');
        }
    }

    public function logout($role = 'teacher')
    {
        parent::logout($role);
    }
}
