<?php
require_once 'app/models/QuizModel.php';
class QuizController extends
Controller
{
    protected $quizModel;
    public function __construct()
    {
        $this->quizModel = new QuizModel();
    }
    public function index()
    {
        $this->view('teacher/quiz/index', [], 'Quản lý câu hỏi trắc nghiệm', 'lecture');
    }

    public function search()
    {

        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $pageSize = isset($_GET['pageSize']) ? (int) $_GET['pageSize'] : 10;
            $subjectId = isset($_GET['subjectId']) ? (int) $_GET['subjectId'] : 0;

            $result = $this->quizModel->getAllQuizzes($subjectId, $page, $pageSize, $keyword);

            echo json_encode($result);
        }
    }
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            $created_by = $_SESSION['teacher_logged_in']['id'];
            $subject_id = $_POST['subject_id'];
            $question = $_POST['question'];
            $options = [
                'option_1' => $_POST['option_a'],
                'option_2' => $_POST['option_b'],
                'option_3' => $_POST['option_c'],
                'option_4' => $_POST['option_d']
            ];
            $correct_option = (int) $_POST['correct_option'];
            $mark = (float) $_POST['mark'];



            header('Content-Type: application/json');
            $result = $this->quizModel->createQuiz($subject_id, $question, $mark, $options, $correct_option, $created_by);
            echo json_encode($result);
        }
    }

    public function import()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $questions = $_POST['questions'];
            $subject_id = $_POST['subject_id'];

            $count = 0;
            $arr = [];
            session_start();
            $created_by = $_SESSION['teacher_logged_in']['id'];
            foreach ($questions as $question) {
                $options = [
                    'option_1' => $question['option_1'],
                    'option_2' => $question['option_2'],
                    'option_3' => $question['option_3'],
                    'option_4' => $question['option_4']
                ];
                $correct_option = (int) $question['answer'];
                if ($correct_option > 0) {
                    // Đảm bảo giá trị mark là số thực (float)
                    $mark = (float) $question['mark'];

                    $result = $this->quizModel->createQuiz($subject_id, $question['title'], $mark, $options, $correct_option, $created_by);

                    if ($result['code'] == 201) {
                        $count++;
                    } else {
                        array_push($arr, "Câu hỏi thứ: " . $question['stt']);
                    }
                }
            }
            header('Content-Type: application/json');
            if ($count != count($questions)) {
                echo json_encode(['code' => 201, 'msg' => 'Import không hoàn tất.', 'unimport' => $arr, 'count' => $count, 'length' => count($questions)]);
            } else {
                echo json_encode(['code' => 201, 'msg' => 'Import hoàn tất.']);
            }
        }
    }


    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $question = $_POST['question'];
            $options = [
                'option_1' => $_POST['option_a'],
                'option_2' => $_POST['option_b'],
                'option_3' => $_POST['option_c'],
                'option_4' => $_POST['option_d']
            ];
            $correct_option = (int) $_POST['correct_option'];
            $mark = (float) $_POST['mark'];
            session_start();
            $updated_by = $_SESSION['teacher_logged_in']['id'];
            header('Content-Type: application/json');
            $result = $this->quizModel->updateQuiz($id, $question, $mark, $options, $correct_option, $updated_by);
            echo json_encode($result);
        }
    }

    public function list()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $subject_id = (int) $_GET['subject_id'];
            $page = (int) $_GET['page'];
            $pageSize = (int) $_GET['pageSize'];
            $keyword = $_GET['keyword'];
            header('Content-Type: application/json');
            // echo json_encode(["subject"=>$subject_id]);
            $result = $this->quizModel->getAllQuizzes($subject_id, $page, $pageSize, $keyword);
            echo json_encode($result);
        }
    }
    public function list_by_subject()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $subject_id = (int) $_GET['subject_id'];
            header('Content-Type: application/json');
            $result = $this->quizModel->getQuizzesBySubject($subject_id);
            echo json_encode($result);
        }
    }

    
    public function detail()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $id = $_GET['id'];
            header('Content-Type: application/json');
            $detail = $this->quizModel->getQuizById($id);
            echo json_encode($detail);
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            header('Content-Type: application/json');
            $detail = $this->quizModel->deleteQuiz($id);
            echo json_encode($detail);
        }
    }

    public function listMarkAndQuestions()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $subject_id = $_GET['subject_id'];
            header('Content-Type: application/json');
            $result = $this->quizModel->getMarksAndQuestionCounts($subject_id);
            echo json_encode($result);
        }
    }
}
