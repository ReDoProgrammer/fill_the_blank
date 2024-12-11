<?php
require_once 'app/models/UserModel.php';
class UserController extends AdminController
{
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
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
}