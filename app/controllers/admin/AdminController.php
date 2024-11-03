<?php

class AdminController extends Controller
{
    public function __construct()
    {
        // Khởi tạo session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Nếu admin chưa đăng nhập và không phải là trang login của admin, chuyển hướng đến trang login của admin
        $currentUrl = trim($_SERVER['REQUEST_URI'], '/');
        if (empty($_SESSION['admin_logged_in']) && !str_contains($currentUrl, 'admin/auth/login')) {
            header('Location: ' . BASE_URL . '/admin/auth/login');
            exit;
        }
    }
}
