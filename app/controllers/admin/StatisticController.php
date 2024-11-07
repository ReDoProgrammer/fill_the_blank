<?php
require_once 'app/models/QuestionModel.php';
require_once 'app/models/UserModel.php';
require_once 'app/models/SubjectModel.php';
require_once 'app/models/LessionModel.php';
require_once 'app/models/HistoryModel.php';

class StatisticController extends AdminController
{
    protected $questionModel;
    protected $userModel, $subjectModel, $lessionModel, $historyModel;
    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        $this->userModel = new UserModel();
        $this->subjectModel = new SubjectModel();
        $this->lessionModel = new LessionModel();
        $this->historyModel = new HistoryModel();
    }



    public function user_statistic()
    {

        $this->view('admin/statistic/index', [], 'Thống kê tài khoản', 'admin');
    }
    public function question_statistic()
    {
        $this->view('admin/statistic/question', [], 'Thống kê câu hỏi', 'admin');
    }
    public function subject_statistic()
    {
        $this->view('admin/statistic/subject', [], 'Thống kê theo môn học', 'admin');
    }
    public function lession_statistic()
    {
        $this->view('admin/statistic/lession', [], 'Thống kê theo bài học', 'admin');
    }

    public function review_statistic()
    {
        $this->view('admin/statistic/review', [], 'Thống kê phần ôn tập', 'admin');
    }
    public function quiz_statistic()
    {
        $this->view('admin/statistic/quiz', [], 'Thống kê thi trắc nghiệm', 'admin');
    }

    function get_review_statistic()
    {
        var_dump($_GET);
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $keyword = $_GET['keyword'];
            $page = (int)$_GET['page'];
            $pageSize = (int)$_GET['pageSize'];
            $lessionId = $_GET['lession'];


            header('Content-Type: application/json');
            $result = $this->lessionModel->getReviewStatistic($lessionId, $keyword, $page, $pageSize);
            echo json_encode(['code' => 200, 'msg' => 'Lấy số liệu thống kê phần ôn tập thành công!', 'result' => $result]);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức không được phép'
            ]);
        }
    }
    function get_users_statistic()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $from_date = DateTime::createFromFormat('d/m/Y', $_GET['from_date']);
            $to_date = DateTime::createFromFormat('d/m/Y', $_GET['to_date']);

            // Chuyển đổi thành chuỗi ngày với định dạng thích hợp, ví dụ Y-m-d
            $fromDateFormatted = $from_date->format('Y-m-d');
            $toDateFormatted = $to_date->format('Y-m-d');

            $keyword = $_GET['keyword'];

            header('Content-Type: application/json');
            $result = $this->userModel->getAccountStatisticsGroupedByUser($keyword, $fromDateFormatted, $toDateFormatted);
            echo json_encode(['code' => 200, 'msg' => 'Lấy số liệu báo cáo theo tài khoản thành công', 'result' => $result]);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức không được phép'
            ]);
        }
    }

    function get_subjects_statistic()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $from_date = DateTime::createFromFormat('d/m/Y', $_GET['from_date']);
            $to_date = DateTime::createFromFormat('d/m/Y', $_GET['to_date']);

            // Chuyển đổi thành chuỗi ngày với định dạng thích hợp, ví dụ Y-m-d
            $fromDateFormatted = $from_date->format('Y-m-d');
            $toDateFormatted = $to_date->format('Y-m-d');

            $keyword = $_GET['keyword'];

            header('Content-Type: application/json');
            $result = $this->subjectModel->getExamCountBySubject($keyword, $fromDateFormatted, $toDateFormatted);
            echo json_encode(['code' => 200, 'msg' => 'Lấy số liệu báo cáo theo môn học thành công', 'result' => $result]);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức không được phép'
            ]);
        }
    }

    function get_lessions_statistic()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $from_date = DateTime::createFromFormat('d/m/Y', $_GET['from_date']);
            $to_date = DateTime::createFromFormat('d/m/Y', $_GET['to_date']);

            // Chuyển đổi thành chuỗi ngày với định dạng thích hợp, ví dụ Y-m-d
            $fromDateFormatted = $from_date->format('Y-m-d');
            $toDateFormatted = $to_date->format('Y-m-d');

            $keyword = $_GET['keyword'];

            header('Content-Type: application/json');
            $result = $this->lessionModel->getExamCountByLession($keyword, $fromDateFormatted, $toDateFormatted);
            echo json_encode(['code' => 200, 'msg' => 'Lấy số liệu báo cáo theo bài học thành công', 'result' => $result]);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức không được phép'
            ]);
        }
    }

    function get_questions_statistic()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $from_date = DateTime::createFromFormat('d/m/Y', $_GET['from_date']);
            $to_date = DateTime::createFromFormat('d/m/Y', $_GET['to_date']);

            // Chuyển đổi thành chuỗi ngày với định dạng thích hợp, ví dụ Y-m-d
            $fromDateFormatted = $from_date->format('Y-m-d');
            $toDateFormatted = $to_date->format('Y-m-d');

            $keyword = $_GET['keyword'];

            header('Content-Type: application/json');
            $result = $this->questionModel->getQuestionStatistics($keyword, $fromDateFormatted, $toDateFormatted);
            echo json_encode(['code' => 200, 'msg' => 'Lấy số liệu báo cáo theo câu hỏi thành công', 'result' => $result]);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức không được phép'
            ]);
        }
    }

    function get_quiz_statistic()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $exam_id = (int) $_GET['exam_id'];
            $page = (int) $_GET['page'];
            $pageSize = (int) $_GET['pageSize'];
            $keyword = $_GET['keyword'];

            header('Content-Type: application/json');
            $result = $this->historyModel->quizHistory($exam_id, $page, $pageSize, $keyword);
            echo json_encode($result);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức không được phép'
            ]);
        }
    }


    function export_quiz_statistic()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $exam_id = (int) $_GET['exam_id'];
            $keyword = $_GET['keyword'];

            header('Content-Type: application/json');
            $result = $this->historyModel->allQuizHistory($exam_id, $keyword);
            echo json_encode($result);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức không được phép'
            ]);
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

    public function top()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $exam_id = $_GET['exam_id'];
            $result = $this->historyModel->getTopResults($exam_id);
            header('Content-Type: application/json');
            echo json_encode($result);
        } else {
            // Nếu không phải phương thức POST, trả về lỗi
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['message' => 'Phương thức không được phép.']);
        }
    }
}
