<?php
require_once 'app/models/UserModel.php';
require_once 'app/models/HistoryModel.php';
class UserController extends AdminController
{
    protected $userModel;
    protected $historyModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->historyModel = new HistoryModel();
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

    public function persionalPractice(){
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            $userId = $_GET['userId'];
            $page = $_GET['page'];
            $pageSize = $_GET['pageSize'];
            $keyword = $_GET['keyword'];
            $result = $this->historyModel->ownHistory($userId,$page,$pageSize,$keyword);
            echo json_encode([
                'code'=>200,
                'msg'=>'Lấy lịch sử ôn tập của học viên thành công!',
                'result'=>$result
            ]);
        }
    }

    public function practiceDetail()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $id = $_GET['id'];
            $result = $this->historyModel->detail($id);
            header('Content-Type: application/json');
            echo json_encode(['code' => 200, 'msg' => 'Lấy chi tiết bài thi thành công!', 'data' => $result]);
        } else {
            // Nếu không phải phương thức POST, trả về lỗi
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['message' => 'Phương thức không được phép.']);
        }
    }
}