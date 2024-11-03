<?php
require_once 'app/models/HistoryModel.php';
class HistoryController extends Controller
{
    protected $historyModel;
    public function __construct()
    {
        $this->historyModel = new HistoryModel();
    }
    public function index()
    {
        $this->view('history/index', [], 'Lịch sử ôn bài');
    }
    public function quiz()
    {
        $this->view('history/quiz', [], 'Lịch sử thi trắc nghiệm');
    }
    public function show()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            session_start();
            $user_id = $_SESSION['user_logged_in']['id'];

            $page = $_GET['page'];
            $pageSize = $_GET['pageSize'];
            $keyword = $_GET['keyword'];
            $result = $this->historyModel->ownHistory($user_id, $page, $pageSize, $keyword);
            header('Content-Type: application/json');
            echo json_encode($result);
        } else {
            // Nếu không phải phương thức POST, trả về lỗi
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['message' => 'Phương thức không được phép.']);
        }

    }


    public function showquiz()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            session_start();
            $user_id = $_SESSION['user_logged_in']['id'];

            $page = $_GET['page'];
            $pageSize = $_GET['pageSize'];
            $keyword = $_GET['keyword'];
            $result = $this->historyModel->ownQuizHistory($user_id, $page, $pageSize, $keyword);
            header('Content-Type: application/json');
            echo json_encode($result);
        } else {
            // Nếu không phải phương thức POST, trả về lỗi
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['message' => 'Phương thức không được phép.']);
        }

    }


    public function detail()
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
    public function quiz_detail()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $id = $_GET['id'];
            $result = $this->historyModel->getQuizDetails($id);
            header('Content-Type: application/json');
            echo json_encode(['code' => 200, 'msg' => 'Lấy chi tiết bài thi trắc nghiệm thành công!', 'data' => $result]);
        } else {
            // Nếu không phải phương thức POST, trả về lỗi
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['message' => 'Phương thức không được phép.']);
        }
    }

    
}



