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

    public function profile()
    {
        $user = $_SESSION['teacher_logged_in'];
        $this->view('teacher/auth/profile', ['profile' => $user], 'Thông tin tài khoản giảng viên',layout:'lecture');
    }

    public function update()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = (int) $_POST["id"];
            $new_password = $_POST["new_password"];
            $fullname = $_POST["fullname"];
            $phone = $_POST["phone"];
            $email = $_POST["email"];
            $user_code = $_POST['user_code'];
            $teaching_id = $_POST['teaching_id'];
            $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
            $data = [
                'fullname' => $fullname,
                'phone' => $phone,
                'email' => $email,
                'password' => $hashedPassword,
                'role'=>'user',
                'user_code'=>$user_code,
                'teaching_id'=>null
            ];
            // Trả về kết quả dưới dạng JSON
            header('Content-Type: application/json');
            $_SESSION['teacher_logged_in'] = null;   
            $result = $this->userModel->updateUser($id, $data);
            echo json_encode($result);
        } else {
            // Nếu không phải phương thức POST, trả về lỗi
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['message' => 'Phương thức không được phép.']);
        }
    }
}
