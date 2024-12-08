<?php
require_once 'app/models/SubjectModel.php';
class HomeController extends Controller
{
    protected $subjectModel;
    public function __construct()
    {
        $this->subjectModel = new SubjectModel();
    }
    public function index()
    {
        $this->view('teacher/home/index', [], 'Home Page','lecture');
    }

    public function about()
    {
    }
    public function contact()
    {
        $this->view('', [], '');
    }
    public function SubjectsHaveQuestions()
    {
        header('Content-Type: application/json');
        $sidebar = $this->subjectModel->getSubjectsWithLessions();
        echo json_encode(['code' => 200, 'msg' => 'Lấy thông tin môn học và bài học!', 'sidebar' => $sidebar]);
        // echo json_encode($sidebar);
    }

    public function SubjectsHaveExams()
    {
        header('Content-Type: application/json');
        $sidebar = $this->subjectModel->getSubjectsWithExams();
        echo json_encode(['code' => 200, 'msg' => 'Lấy thông tin môn học có bài kiểm tra!', 'sidebar' => $sidebar]);
        // echo json_encode($sidebar);
    }
    public function check_session()
    {
        session_start();
        // Giả sử bạn có một session với tên 'user_logged_in'
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] == true) {
            echo json_encode(['code' => 200, 'msg'=> 'Bạn đã đăng nhập thành công!']);
        } else {
            echo json_encode(['code' => 401,'msg'=> 'Vui lòng đăng nhập để thực hiện tính năng này!','url'=>BASE_URL.'/auth/login']);
        }
    }
}



