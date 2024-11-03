<?php
require_once 'app/models/LessionModel.php';

class LessionController extends AdminController
{
    protected $lessionModel;

    public function __construct()
    {
        $this->lessionModel = new LessionModel();
    }

    public function index()
    {
        $this->view('admin/lession/index', [], 'Quản lý bài học', 'admin');
    }

    public function getAll()
    {
        header('Content-Type: application/json');
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $subjectId = isset($_GET['subject_id']) ? (int) $_GET['subject_id'] : 0;
            $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $pageSize = isset($_GET['pageSize']) ? (int) $_GET['pageSize'] : 10;

            $result = $this->lessionModel->getAllLessions($subjectId, $keyword, $page, $pageSize);

            echo json_encode($result);
        }
    }
    // Lấy tất cả bài học theo subject_id
    public function getBySubject()
    {
        header('Content-Type: application/json');
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $subjectId = isset($_GET['subject_id']) ? (int) $_GET['subject_id'] : 0;

            // Gọi hàm getBySubject từ model
            $lessions = $this->lessionModel->getBySubject($subjectId);
            echo json_encode([
                'code' => 200,
                'msg' => 'Lấy danh sách bài học thành công!',
                'lessions' => $lessions
            ]);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức truy vấn không hợp lệ!'
            ]);
        }
    }
    public function detail()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $id = isset($_GET["id"]) ? (int) $_GET["id"] : -1;
            $lession = $this->lessionModel->getLessionById($id);
            if ($lession) {
                echo json_encode(['code' => 200, 'msg' => 'Lấy thông tin bài học thành công!', 'lession' => $lession]);
            } else {
                echo json_encode(['code' => 404, 'msg' => 'Không tìm thấy bài học tương ứng']);
            }
        }
    }

    public function add()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $subjectId = $_POST['subject_id'];

            $result = $this->lessionModel->createLession($name, $subjectId);
            echo json_encode($result);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức truy vấn không hợp lệ!'
            ]);
        }
    }

    public function update()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';

            $result = $this->lessionModel->updateLession($id, $name);
            echo json_encode($result);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức truy vấn không hợp lệ!'
            ]);
        }
    }

    public function delete()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';

            if (empty($id)) {
                echo json_encode([
                    'code' => 400,
                    'msg' => 'Mã bài học không hợp lệ!'
                ]);
                return;
            }

            $result = $this->lessionModel->deleteLession($id);
            echo json_encode($result);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức truy vấn không hợp lệ!'
            ]);
        }
    }
}
