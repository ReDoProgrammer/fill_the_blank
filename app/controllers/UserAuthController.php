<?php

require_once 'app/controllers/AuthController.php';

class UserAuthController extends AuthController
{
    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $this->loginWithRole($username, $password, 'user');
        } else {
            $this->loginView('user');
        }
    }
    public function profile()
    {
        $user = $_SESSION['user_logged_in'];
        $this->view('auth/profile', ['profile' => $user], 'Thông tin tài khoản');
    }

    public function update()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = (int) $_POST["id"];
            $password = $_POST["password"];
            $new_password = $_POST["new_password"];
            $fullname = $_POST["fullname"];
            $phone = $_POST["phone"];
            $email = $_POST["email"];
            $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);
            $data = [
                'fullname' => $fullname,
                'phone' => $phone,
                'email' => $email,
                'password' => $hashedPassword
            ];
            // Trả về kết quả dưới dạng JSON
            header('Content-Type: application/json');
            $result = $this->userModel->updateUser($id, $data);
            echo json_encode($result);
        } else {
            // Nếu không phải phương thức POST, trả về lỗi
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['message' => 'Phương thức không được phép.']);
        }
    }

    public function logout($role = 'user')
    {
        parent::logout('user');
    }
}
