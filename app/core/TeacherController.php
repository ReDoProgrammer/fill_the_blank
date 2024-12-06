<?php

require_once 'app/core/Controller.php'; // Bao gồm lớp Controller

class TeacherController extends Controller
{
    public function __construct()
    {      
        // Kiểm tra xem admin đã đăng nhập chưa
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); // Khởi tạo session nếu chưa có
        }

        if (empty($_SESSION['teacher_logged_in'])) {
            // Nếu không có session teacher_logged_in, chuyển hướng đến trang login của teacher
            header('Location: ' . BASE_URL . '/teacher/auth/login');
            exit;
        }
    }
}
