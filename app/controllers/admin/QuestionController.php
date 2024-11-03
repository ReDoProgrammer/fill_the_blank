<?php
require_once 'app/models/QuestionModel.php';

class QuestionController extends AdminController
{
    protected $questionModel;

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
    }

    public function index()
    {
        $this->view('admin/question/index', [], 'Quản lý câu hỏi', 'admin');
    }

    public function search()
    {

        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $pageSize = isset($_GET['pageSize']) ? (int) $_GET['pageSize'] : 10;
            $subjectId = isset($_GET['subjectId']) ? (int) $_GET['subjectId'] : 0;
    
            $result = $this->questionModel->getAllQuestions($subjectId, $page, $pageSize, $keyword);
    
            echo json_encode($result);
        }
    }
    
    public function all(){
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $lession_id = $_GET['lession_id'];
            $result = $this->questionModel->getAllQuestionsByLession($lession_id);
            header('Content-Type: application/json');
            echo json_encode(['code'=>200,'msg'=>'Lấy danh sách câu hỏi theo môn học thành công!','data'=> $result]);
        }
    }

    public function detail()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $id = isset($_GET["id"]) ? (int) $_GET["id"] : -1;
            $detail = $this->questionModel->getQuestionById($id);
            header('Content-Type: application/json');
            if ($detail) {
                echo json_encode(['code' => 200, 'msg' => 'Lấy thông tin câu hỏi thành công', 'detail' => $detail]);
            } else {
                echo json_encode(['code' => 404, 'msg' => 'Không tìm thấy câu hỏi tương ứng']);
            }
        }
    }

    public function add()
    {
        header('Content-Type: application/json');
        // echo json_encode(['code'=> 200,'msg'=> 'test']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $lessionId = $_POST['lession_id'] ?? '';
            $question = $_POST['question'] ?? '';
            $blanks = $_POST['blanks'] ?? []; 
            // echo json_encode(['lessionId'=> $lessionId,'msg'=> 'test']);
            $result = $this->questionModel->createQuestion($lessionId, $question, $blanks);
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
            $lessonId = $_POST['lession_id'] ?? '';
            $question = $_POST['question'] ?? '';
            $blanks = $_POST['blanks'] ?? []; 

            $result = $this->questionModel->updateQuestion($id, $lessonId, $question, $blanks);
            echo json_encode($result);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức không được phép'
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
                    'msg' => 'ID không hợp lệ'
                ]);
                return;
            }

            $result = $this->questionModel->deleteQuestion($id);
            echo json_encode($result);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức không được phép'
            ]);
        }
    }
}
