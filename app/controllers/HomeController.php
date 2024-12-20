<?php
require_once 'app/models/SubjectModel.php';
require_once 'app/models/ExamModel.php';
class HomeController extends Controller
{
    protected $subjectModel;
    protected $examModel;
    public function __construct()
    {
        $this->subjectModel = new SubjectModel();
        $this->examModel = new ExamModel();
    }
    public function index()
    {
        $this->view('home/index', [], 'Home Page');
    }

    public function about() {}
    public function contact()
    {
        $this->view('', [], '');
    }
   

    public function OwnExams()
    {
        header('Content-Type: application/json');
        session_start();
        $userId = $_SESSION['user_logged_in']['id'];
        $result = $this->examModel->getExamsByUserId($userId);
        echo json_encode(['code' => 200, 'msg' => 'Lấy danh sách bài thi thành công!', 'result' => $result]);
      
    }

    public function OwnSubjects()
    {
        header('Content-Type: application/json');
        session_start();
        $userId = $_SESSION['user_logged_in']['id'];
        $subjects = $this->subjectModel->getOwnSubjects($userId);
        echo json_encode(['code' => 200, 'msg' => 'Lấy danh sách môn học của học viên thành công!', 'subjects' => $subjects]);
        // echo json_encode($sidebar);
    }
    public function check_session()
    {
        session_start();
        // Giả sử bạn có một session với tên 'user_logged_in'
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] == true) {
            echo json_encode(['code' => 200, 'msg' => 'Bạn đã đăng nhập thành công!']);
        } else {
            echo json_encode(['code' => 401, 'msg' => 'Vui lòng đăng nhập để thực hiện tính năng này!', 'url' => BASE_URL . '/auth/login']);
        }
    }
}
