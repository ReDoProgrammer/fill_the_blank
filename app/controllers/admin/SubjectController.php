<?php
require_once 'app/models/SubjectModel.php';

class SubjectController extends AdminController
{
    protected $subjectModel;

    public function __construct()
    {
        $this->subjectModel = new SubjectModel();
    }

    public function index()
    {
        $this->view('admin/subject/index', [], 'Quản lý môn học', 'admin');
    }

    public function search()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $pageSize = isset($_GET['pageSize']) ? (int) $_GET['pageSize'] : 10;

            // Gọi hàm getAllSubjects từ model
            $result = $this->subjectModel->searchSubject($keyword, $page, $pageSize);

            // Trả kết quả dưới dạng JSON
            echo json_encode($result);
        }
    }
    // Lấy tất cả môn học
    public function allSubjects()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            // Gọi hàm allSubjects từ model
            $subjects = $this->subjectModel->allSubjects();
            echo json_encode([
                'code' => 200,
                'msg' => 'Lấy danh sách môn học thành công',
                'subjects' => $subjects
            ]);
        } else {
            // Nếu không phải GET request, trả về lỗi
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
            $subject = $this->subjectModel->getSubjectById($id);
            if ($subject) {
                echo json_encode(['code' => 200, 'msg' => 'Lấy thông tin môn học thành công', 'subject' => $subject]);
            } else {
                echo json_encode(['code' => 404, 'msg' => 'Không tìm thấy môn học tương ứng']);
            }
        }
    }

    public function add()
    {
        header('Content-Type: application/json'); // Đặt header cho JSON
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];

            // Gọi hàm tạo môn học từ model và trả về kết quả dưới dạng JSON
            $result = $this->subjectModel->createSubject($name);
            echo json_encode($result);
        } else {
            // Nếu không phải POST request, trả về lỗi
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức truy vấn không hợp lệ!'
            ]);
        }
    }

    public function update()
    {
        header('Content-Type: application/json'); // Đặt header cho JSON

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ POST request
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';

            // Gọi hàm updateSubject từ model và trả về kết quả dưới dạng JSON
            $result = $this->subjectModel->updateSubject($id, $name);
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

            // Gọi hàm deleteSubject từ model và trả về kết quả dưới dạng JSON
            $result = $this->subjectModel->deleteSubject($id);
            echo json_encode($result);
        } else {
            // Nếu không phải POST request, trả về lỗi
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức không được phép'
            ]);
        }
    }

    public function getSubjectsWithQuizzes()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $subjects = $this->subjectModel->getSubjectsWithQuizzes();
            echo json_encode(['code' => 200, 'msg' => 'Lấy thông tin môn học có quiz thành công!', 'subjects' => $subjects]);
        }
    }
}
