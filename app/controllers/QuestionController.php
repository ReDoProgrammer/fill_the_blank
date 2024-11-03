<?php
require_once 'app/models/SubjectModel.php';
require_once 'app/models/LessionModel.php';
require_once 'app/models/QuestionModel.php';
class QuestionController extends Controller
{
  protected $subjectModel;
  protected $lessionModel;
  protected $questionModel;
  public function __construct()
  {
    $this->lessionModel = new LessionModel();
    $this->subjectModel = new SubjectModel();
    $this->questionModel = new QuestionModel();
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
    $lession = $_GET['l'];

    // Cắt chuỗi theo ký tự '-'
    $subjectParts = explode('-', $subject);
    $lessionParts = explode('-', $lession);

    // Lấy phần tử cuối cùng của mỗi mảng
    $subjectId = end($subjectParts);
    $lessionId = end($lessionParts);

    $subjectName = $this->subjectModel->getSubjectById($subjectId)['name'];
    $lessionName = $this->lessionModel->getLessionById($lessionId)['name'];

    $this->view('question/index', ['lessionId' => $lessionId, 'lession' => $lessionName, 'subjectId' => $subjectId, 'subject' => $subjectName], 'Luyện tập');
  }

  public function getQuestion()
  {
    header('Content-Type: application/json');
    $id = $_GET['id'];
    $detail = $this->questionModel->getQuestionById($id);
    if ($detail) {
      echo json_encode(['code' => 200, 'msg' => 'Lấy thông tin câu hỏi thành công!', 'detail' => $detail], JSON_UNESCAPED_UNICODE);
    } else {
      echo json_encode(['code' => 404, 'msg' => 'Không tìm thấy câu hỏi!'], JSON_UNESCAPED_UNICODE);
    }

  }

  public function canTakeTest()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $subject_id = $_GET['subject_id'];
      $lession_id = $_GET['lession_id'];
      session_start();
      if (isset($_SESSION['user_logged_in'])) {
        $user_id = $_SESSION['user_logged_in']['id'];
        header('Content-Type: application/json');
        $result = $this->questionModel->canDoTest($subject_id, $lession_id, $user_id);
        echo json_encode($result);
      } else {
        echo json_encode(['code'=>401,'msg'=>'Tác vụ này yêu cầu phải có tài khoản. Vui lòng đăng nhập để thực hiện chức năng này!']);
      }

    }
  }

  public function getMinQuestion()
  {
    header('Content-Type: application/json');
    $id = $_GET['lession_id'];
    $result = $this->questionModel->getMinIdQuestionIdByLessionId($id);
    echo json_encode($result);

  }

  public function answer()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Lấy dữ liệu từ request
      $questionId = $_POST['question_id'];
      $answers = $_POST['answers']; // Giả sử answers là một mảng các câu trả lời

      // Kiểm tra câu trả lời bằng hàm checkAnswers trong QuestionModel
      $result = $this->questionModel->checkAnswers($questionId, $answers);

      // Trả về kết quả dưới dạng JSON
      header('Content-Type: application/json');
      echo json_encode($result);
    } else {
      // Nếu không phải phương thức POST, trả về lỗi
      header('HTTP/1.1 405 Method Not Allowed');
      echo json_encode(['message' => 'Phương thức không được phép.']);
    }
  }

  public function saveResult()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      session_start();
      $user_id = $_SESSION['user_logged_in']['id'];
      $subject_id = $_POST['subject_id'];
      $lession_id = $_POST['lession_id'];
      $answers = $_POST['answers'];
      header('Content-Type: application/json');
      $result = $this->questionModel->saveResult($user_id, $subject_id, $lession_id, $answers);
      echo json_encode($result);
    } else {
      // Nếu không phải phương thức POST, trả về lỗi
      header('HTTP/1.1 405 Method Not Allowed');
      echo json_encode(['message' => 'Phương thức không được phép.']);
    }
  }


}

