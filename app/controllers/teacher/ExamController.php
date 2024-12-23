<?php
require_once 'app/models/ExamModel.php';
require_once 'app/models/ConfigModel.php';

class ExamController extends Controller{
    protected $examModel;
    protected $configModel;
    

    public function __construct()
    {
        $this->examModel = new ExamModel();
        $this->configModel = new ConfigModel();
    }
    public function index()
    {
        $this->view('teacher/exam/index', [], 'Danh sách đề thi','lecture');
    }
    public function customize()
    {
        $this->view('teacher/exam/customize', [], 'Chỉnh sửa đề bài thi trắc nghiệm', 'teacher');
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lấy dữ liệu từ POST
            $teaching_id = $_POST['teaching_id'];
            $subject_id = $_POST['subject_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $begin_date = $_POST['begin_date'];
            $end_date = $_POST['end_date'];
            $number_of_questions = (int) $_POST['number_of_questions'];
            $duration = (int) $_POST['duration'];
            $random_questions = (int) $_POST['random_questions'];
            $mode = (int) $_POST['mode'];

            // Xử lý hình ảnh nếu có
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileSize = $_FILES['image']['size'];
                $fileType = $_FILES['image']['type'];
                $fileNameCmps = explode('.', $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                // Kiểm tra phần mở rộng file hợp lệ
                $allowedExts = array('jpg', 'jpeg', 'png', 'gif');
                if (in_array($fileExtension, $allowedExts)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    // Sử dụng đường dẫn hệ thống tập tin
                    $uploadFileDir = './public/upload/images/';
                    $dest_path = $uploadFileDir . $newFileName;

                    // Đảm bảo thư mục tồn tại
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        // Lưu đường dẫn file vào cơ sở dữ liệu
                        $imagePath = '/public/upload/images/' . $newFileName;
                    } else {
                        echo 'Error uploading image.';
                        exit;
                    }
                } else {
                    echo 'Invalid file extension.';
                    exit;
                }
            } else {
                $imagePath = '';
            }
            header('Content-Type: application/json');
            session_start();
            $created_by = $_SESSION['teacher_logged_in']['id'];
            $result = $this->examModel->createExam($teaching_id,$title, $description, $number_of_questions, $duration, $mode, $imagePath, $begin_date, $end_date, $subject_id, $created_by);
            echo json_encode($result);
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lấy dữ liệu từ POST
            $id = $_POST['id'];
            $teaching_id = $_POST['teaching_id'];
            $subject_id = $_POST['subject_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $begin_date = $_POST['begin_date'];
            $end_date = $_POST['end_date'];
            $number_of_questions = (int) $_POST['number_of_questions'];
            $duration = (int) $_POST['duration'];
            $mode = (int) $_POST['mode']; // mode được lấy từ POST

            // Xử lý hình ảnh nếu có
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileSize = $_FILES['image']['size'];
                $fileType = $_FILES['image']['type'];
                $fileNameCmps = explode('.', $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                // Kiểm tra phần mở rộng file hợp lệ
                $allowedExts = array('jpg', 'jpeg', 'png', 'gif');
                if (in_array($fileExtension, $allowedExts)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    // Sử dụng đường dẫn hệ thống tập tin
                    $uploadFileDir = './public/upload/images/';
                    $dest_path = $uploadFileDir . $newFileName;

                    // Đảm bảo thư mục tồn tại
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        // Lưu đường dẫn file vào cơ sở dữ liệu
                        $imagePath = '/public/upload/images/' . $newFileName;
                    } else {
                        echo json_encode(['code' => 500, 'msg' => 'Lỗi upload thumbnail']);
                        exit;
                    }
                } else {
                    echo json_encode(['code' => 500, 'msg' => 'Lỗi định dạng hình ảnh của thumbnail']);
                    exit;
                }
            } else {
                // Giữ nguyên đường dẫn hình ảnh hiện tại nếu không có hình ảnh mới
                $imagePath = '';
            }

            // Gọi hàm updateExam và trả về kết quả dưới dạng JSON
            header('Content-Type: application/json');
            session_start();
            $updated_by = $_SESSION['teacher_logged_in']['id'];
            $result = $this->examModel->updateExam($id, $teaching_id,$title, $description, $number_of_questions, $duration, $mode, $imagePath, $begin_date, $end_date, $subject_id,$updated_by);
            echo json_encode($result);
        }
    }



    public function search()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $page = (int) $_GET['page'];
            $pageSize = (int) $_GET['pageSize'];
            $roomId = $_GET['roomId'];
            $keyword = $_GET['keyword'];
            session_start();
            $created_by = $_SESSION['teacher_logged_in']['id'];
            // echo $created_by;
            header('Content-Type: application/json');
            $result = $this->examModel->getOwnExams($roomId, null,null, $page, $pageSize, $keyword, $created_by);
            echo json_encode($result);
        }
    }

    public function list_by_subject()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $subject_id = $_GET['subject_id'];
            header('Content-Type: application/json');
            $result = $this->examModel->getBySubject($subject_id);
            echo json_encode($result);
        }
    }

    public function detail()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $id = $_GET['id'];
            header('Content-Type: application/json');
            $detail = $this->examModel->getExamById($id);
            echo json_encode($detail);
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            header('Content-Type: application/json');
            $result = $this->examModel->deleteExam($id);
            echo json_encode($result);
        }

    }

    public function getQuestionsList()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $id = $_GET['id'];
            header('Content-Type: application/json');
            $result = $this->examModel->getQuestionsByExamId($id);
            echo json_encode($result);
        }
    }

    function listConfigsBySubject(){
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $subject_id = $_GET['subject_id'];
            $number_of_questions = $_GET['number_of_questions'];
            header('Content-Type: application/json');
            $result = $this->configModel->getConfigsBySubjectQuestionsAndMarks($subject_id,$number_of_questions);
            echo json_encode($result);
        }
    }
}