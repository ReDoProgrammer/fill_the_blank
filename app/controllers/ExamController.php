<?php
require_once 'app/models/SubjectModel.php';
require_once 'app/models/LessionModel.php';
require_once 'app/models/QuestionModel.php';
require_once 'app/models/ExamModel.php';
class ExamController extends Controller
{
  protected $subjectModel;
  protected $lessionModel;
  protected $questionModel;
  protected $examModel;
  public function __construct()
  {
    $this->lessionModel = new LessionModel();
    $this->subjectModel = new SubjectModel();
    $this->questionModel = new QuestionModel();
    $this->examModel = new ExamModel();
  }
  public function index()
  {
    session_start();
    // Kiểm tra nếu session không tồn tại
    if (!isset($_SESSION['user_logged_in'])) {
      // Điều hướng tới trang đăng nhập
      header('Location:' . BASE_URL . '/login'); // Đường dẫn tới trang đăng nhập
      exit();
    }

    $subject = $_GET['s'];

    // Cắt chuỗi theo ký tự '-'
    $subjectParts = explode('-', $subject);

    // Lấy phần tử cuối cùng của mỗi mảng
    $subjectId = end($subjectParts);

    $subjectName = $this->subjectModel->getSubjectById($subjectId)['name'];

    $this->view('exam/index', ['subject' => $subjectName], 'Danh sách bài thi trắc nghiệm');
  }

  public function doing()
  {
    session_start();
    // Kiểm tra nếu session không tồn tại
    if (!isset($_SESSION['user_logged_in'])) {
      // Điều hướng tới trang đăng nhập
      header('Location:' . BASE_URL . '/login'); // Đường dẫn tới trang đăng nhập
      exit();
    }

    $this->view('exam/doing', [], 'Làm bài thi trắc nghiệm');
  }

  public function list_by_subject()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $subject_id = (int) $_GET['subject_id'];
      header('Content-Type: application/json');
      $exams = $this->examModel->getBySubject($subject_id);
      echo json_encode(['code' => 200, 'msg' => 'Lấy danh sách bài thi thành công!', 'exams' => $exams]);
    }
  }

  public function check_available()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      session_start();
      $user_id = $_SESSION['user_logged_in']['id'];
      $exam_id = (int) $_GET['exam_id'];
      header('Content-Type: application/json');
      $count = $this->examModel->CheckAvailable($user_id, $exam_id);
      if ($count <= 3) {
        echo json_encode(['code' => 200, 'msg' => 'Bạn có thể tiếp tục làm bài thi!']);
      } else {
        echo json_encode(['code' => 409, 'msg' => 'Bạn không thể làm lại đề bài thi này !', 'header' => 'Số lần làm bài thi này của bạn đã đạt mức tối đa']);
      }
    }
  }

  public function detail()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $id = $_GET['id'];
      header('Content-Type: application/json');
      $detail = $this->examModel->getExamById($id);
      echo json_encode($detail);
    }
  }

  public function save()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $exam_id = (int)$_POST['exam_id'];
      $result = $_POST['result'];
      $spent_time = (int)$_POST['spent_time'];
      session_start();
      $user_id = $_SESSION['user_logged_in']['id'];
      header('Content-Type: application/json');
      $rs = $this->examModel->saveResult($user_id,$exam_id,$result,$spent_time);
      echo json_encode($rs);
    }
  }


}

