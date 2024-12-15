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

    //trả về danh sách các lớp giảng dạy của năm học hiện tại
    public function listCurretCLassese()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $result = $this->teachingModel->listCurrentClasses();
            echo json_encode([
                'code' => 200,
                'msg' => 'Lấy danh sách lớp giảng dạy của năm học hiện tại thành công!',
                'classese' => $result
            ]);
        }
    }
    public function detail()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $id = isset($_GET["id"]) ? (int) $_GET["id"] : -1;
            $result = $this->teachingModel->getTeachingById($id);
            echo json_encode(['code' => 200, 'msg' => 'Lấy thông tin giảng dạy thành công!', 'detail' => $result]);
        }
    }

    public function update()
    {
        header('Content-Type: application/json'); // Đặt header cho JSON

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức truy vấn không hợp lệ!'
            ]);
            return;
        }

        $input = file_get_contents('php://input'); // Lấy nội dung thô
        $data = json_decode($input, true); // Chuyển JSON thành mảng

        // Truy cập các giá trị
        $id = $data['id'] ?? null;
        $name = $data['name'] ?? null;
        $teacher_id = $data['teacher_id'] ?? null;
        $subjects = $data['subjects'] ?? [];
        $schoolyear = $data['schoolyear'] ?? null;
      

        // Kiểm tra dữ liệu
        if (!$id || !$name || !$teacher_id || !$schoolyear || empty($subjects)) {
            echo json_encode([
                'code' => 400,
                'msg' => 'Dữ liệu không hợp lệ!'
            ]);
            return;
        }

        // Gọi hàm updateTeaching từ model để cập nhật
        $result = $this->teachingModel->updateTeaching($id, $name, $teacher_id, $subjects, $schoolyear);

        // Trả về kết quả dưới dạng JSON
        echo json_encode($result);
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
        header('Content-Type: application/json'); // Đặt header JSON

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức truy vấn không hợp lệ!'
            ]);
            return;
        }

        $input = file_get_contents('php://input'); // Lấy nội dung thô
        $data = json_decode($input, true); // Chuyển JSON thành mảng

        // Truy cập các giá trị
        $name = $data['name'] ?? null;
        $teacher_id = $data['teacher_id'] ?? null;
        $subjects = $data['subjects'] ?? [];
        $schoolyear = $data['schoolyear'] ?? null;

        // Kiểm tra dữ liệu
        if (!$name || !$teacher_id || !$schoolyear || empty($subjects)) {
            echo json_encode([
                'code' => 400,
                'msg' => 'Dữ liệu không hợp lệ!'
            ]);
            return;
        }


        $result = $this->teachingModel->createTeaching($name, $teacher_id, $subjects, $schoolyear);
        echo json_encode($result);
    }
}
