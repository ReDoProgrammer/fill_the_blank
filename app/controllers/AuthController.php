<?php

require_once 'app/core/Controller.php';
require_once 'app/models/UserModel.php';

class AuthController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function loginWithRole($username, $password, $role)
    {
        header('Content-Type: application/json');

        // Fetch user from the database
        $user = $this->userModel->getUserByUsername($username);


        if ($user && md5($password) === $user['password']) {
            if ($role === 'admin' && $user['role'] === 'admin') {
                $_SESSION['admin_logged_in'] = $user;
                echo json_encode([
                    'code' => 200,
                    'message' => 'Admin login successful',
                    'redirect' => BASE_URL . '/admin'
                ]);
            } elseif($role == 'teacher' && $user['role'] === 'teacher' ){
                echo json_encode([
                    'code' => 200,
                    'message' => 'Teacher login successful',
                    'redirect' => BASE_URL . '/home'
                ]);
            }
            elseif (($role === 'user' && $user['role'] === 'user') || ($role === 'user' && $user['role'] === 'admin')) {
                $_SESSION['user_logged_in'] = $user;
                echo json_encode([
                    'code' => 200,
                    'message' => 'User login successful',
                    'redirect' => BASE_URL . '/home'
                ]);
            } else {
                echo json_encode([
                    'code' => 403,
                    'message' => 'Bạn không có quyền truy cập module này!'
                ]);
            }
        } else {
            echo json_encode([
                'code' => 401,
                'message' => 'Tài khoản hoặc mật khẩu không chính xác!!'
            ]);
        }

        exit;
    }

    protected function loginView($role)
    {       
        $viewPath = ($role === 'admin') ? 'app/views/admin/auth/login.php' : ($role ==='teacher'?'app/views/teacher/auth/login.php':'app/views/auth/login.php');
        $title = ($role === 'admin') ? 'Admin Login' :(($role === 'teacher') ?'Teacher Login': 'User Login');
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            die('View file not found.');
        }
    }

    public function index()
    {
        $this->loginView('user'); // Hoặc 'admin' tùy thuộc vào nhu cầu
    }
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $new_password = $_POST['new_password'];
            $confirm_new_password = $_POST['confirm_new_password'];
            $fullname = $_POST['fullname'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            echo '' . $username . '' . $password . '' . $confirm_new_password;
        } else {
            // Nếu không phải phương thức POST, trả về lỗi
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['message' => 'Phương thức không được phép.']);
        }
    }

    public function logout($role)
    {
        if ($role === 'admin') {
            $_SESSION['admin_logged_in'] = null;
        } elseif($role === 'teacher') {
            $_SESSION['teacher_logged_in'] = null;
        }else{
            $_SESSION['user_logged_in'] = null;
        }
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}
