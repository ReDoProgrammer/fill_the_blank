<?php
require_once 'app/models/TeachingModel.php';
class TeachingController extends AdminController
{
    protected $teachingModel;
    public function __construct()
    {
        $this->teachingModel = new TeachingModel();
    }
    public function index()
    {
        $this->view('admin/teaching/index', [], 'Quản lý quá trình giảng dạy', 'admin');
    }
    public function search()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $pageSize = isset($_GET['pageSize']) ? (int) $_GET['pageSize'] : 10;
            $result = $this->teachingModel->getAllTeachings($keyword, $page, $pageSize);
            echo json_encode($result);
        }
    }
    public function detail()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $id = isset($_GET["id"]) ? (int) $_GET["id"] : -1;
            $result = $this->teachingModel->getTeachingById($id);
            echo json_encode(['code'=>200,'msg'=>'Lấy thông tin giảng dạy thành công!','detail'=>$result]);
        }
    }

    public function update()
    {
        header('Content-Type: application/json'); // Đặt header cho JSON

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ POST request
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'];
            $teacher_id = $_POST['teacher_id'];
            $subject_id = $_POST['subject_id'];
            $schoolyear = $_POST['schoolyear'];

            $result = $this->teachingModel->updateTeaching($id,$name,$teacher_id,$subject_id,$schoolyear);
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

            // Kiểm tra xem ID có hợp lệ không
            if (empty($id)) {
                echo json_encode([
                    'code' => 400,
                    'msg' => 'ID không hợp lệ'
                ]);
                return;
            }

            $result = $this->teachingModel->deleteTeaching($id);
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
            $name = $_POST['name'];
            $teacher_id = $_POST['teacher_id'];
            $subject_id = $_POST['subject_id'];
            $schoolyear = $_POST['schoolyear'];

            $result = $this->teachingModel->createTeaching($name,$teacher_id, $subject_id, $schoolyear);
            echo json_encode($result);
        } else {
            // Nếu không phải POST request, trả về lỗi
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức truy vấn không hợp lệ!'
            ]);
        }
    }
}
