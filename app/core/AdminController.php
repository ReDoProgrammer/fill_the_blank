<?php

require_once 'app/core/Controller.php'; // Bao gồm lớp Controller

class AdminController extends Controller
{
    public function __construct()
    {
        // parent::__construct(); // Gọi constructor của lớp cơ sở

        // Kiểm tra xem admin đã đăng nhập chưa
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); // Khởi tạo session nếu chưa có
        }

        if (empty($_SESSION['admin_logged_in'])) {
            // Nếu không có session admin_logged_in, chuyển hướng đến trang login của admin
            header('Location: ' . BASE_URL . '/admin/auth/login');
            exit;
        }
    }
}
