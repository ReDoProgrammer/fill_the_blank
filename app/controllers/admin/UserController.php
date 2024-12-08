<?php
require_once 'app/models/UserModel.php';
class UserController extends AdminController
{
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    public function index()
    {
        $this->view('admin/user/index', [], 'Quản lý tài khoản', 'admin');
    }
    public function search()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $pageSize = isset($_GET['pageSize']) ? (int) $_GET['pageSize'] : 10;
            $role = $_GET['role']??'user';
            $userModel = new UserModel();
            $result = $userModel->getAllUsers($keyword, $page, $pageSize,$role);

            echo json_encode($result);
        }
    }
    public function detail()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $id = isset($_GET["id"]) ? (int) $_GET["id"] : -1;
            $user = $this->userModel->getUserById($id);
            if ($user) {
                echo json_encode(['code' => 200, 'msg' => 'Lấy thông tin tài khoản thành công', 'user' => $user]);
            } else {
                echo json_encode(['code' => 404, 'msg' => 'Không tìm thấy tài khoản tương ứng']);
            }
        }
    }

    public function update()
    {
        header('Content-Type: application/json'); // Đặt header cho JSON

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ POST request
            $id = $_POST['id'] ?? '';
            $usercode = $_POST['usercode'] ?? '';
            $fullname = $_POST['fullname'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role']??'user';

            // Chuẩn bị dữ liệu để cập nhật
            $data = [
                'user_code' => $usercode,
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $phone,
                'password' => md5($password), // Mã hóa mật khẩu
                'role'=>$role
            ];

            // Gọi hàm updateUser từ model và trả về kết quả dưới dạng JSON
            $result = $this->userModel->updateUser($id, $data);
            echo json_encode($result);
        } else {
            // Nếu không phải POST request, trả về lỗi
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức không được phép'
            ]);
        }
    }

    public function delete()
    {
        header('Content-Type: application/json'); // Đặt header cho JSON

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ POST request
            $id = $_POST['id'] ?? '';
            $role = $_POST['role']??'user';
            // Kiểm tra xem ID có hợp lệ không
            if (empty($id)) {
                echo json_encode([
                    'code' => 400,
                    'msg' => 'ID không hợp lệ'
                ]);
                return;
            }

            // Gọi hàm deleteUser từ model và trả về kết quả dưới dạng JSON
            $result = $this->userModel->deleteUser($id,$role);
            echo json_encode($result);
        } else {
            // Nếu không phải POST request, trả về lỗi
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức không được phép'
            ]);
        }
    }

    public function add()
    {
        header('Content-Type: application/json'); // Thiết lập header đúng cho JSON
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $usercode = $_POST['usercode'];
            $password = $_POST['password'];
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $role = $_POST['role']??'user';

            // Gọi hàm tạo người dùng từ model và trả về kết quả dưới dạng JSON
            $result = $this->userModel->createUser($username, $usercode, $fullname, $phone, $email, $password,$role);

            echo json_encode($result);
        } else {
            // Nếu không phải POST request, trả về lỗi
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức truy vấn không hợp lệ!'
            ]);
        }
    }

    public function import()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $users = $_POST['users'] ?? [];
            // echo json_encode($users);
            header('Content-Type: application/json'); // Thiết lập header đúng cho JSON
            $result = $this->userModel->importUsers($users);
            echo $result;
        }
    }
}
