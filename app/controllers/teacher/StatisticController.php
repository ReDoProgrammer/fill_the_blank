<?php
require_once 'app/models/HistoryModel.php';
require_once 'app/models/TeachingModel.php';
require_once 'app/models/LessionModel.php';
class StatisticController extends Controller
{
    protected $historyModel;
    protected $teachingModel;
    protected $lessionModel;

    public function __construct()
    {
        $this->historyModel = new HistoryModel();
        $this->teachingModel = new TeachingModel();
        $this->lessionModel = new LessionModel();
    }
    public function index()
    {
        $this->view('teacher/statistic/index', [], 'Thống kê ôn tập', 'lecture');
    }

    //hàm thống kê ôn tập theo bài học của từng môn tương ứng với lớp học
    public function StatisticByLession(){
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $classId = (int)$_GET['classId'];
            $lessionId = (int)$_GET['lessionId'];
            $keyword = (string)$_GET['keyword'];
            $page = (int)$_GET['page'];
            $pageSize = (int)$_GET['pageSize'];
            header('Content-Type: application/json');
            $result = $this->lessionModel->StatisticByLessionBasedonClass($classId,$lessionId,$keyword,$page,$pageSize);
            echo json_encode([
                'code'=>200,
                'msg'=>'Thống kê ôn tập theo bài tương ứng với lớp học thành công!',
                'result'=>$result
            ]);
        }
    }

    //hàm thống kê ôn tập theo môn học theo lớp
    function StatisticBySubject()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $classId = (int)$_GET['classId'];
            $subjectId = (int)$_GET['subjectId'];
            $keyword = $_GET['keyword'];
            $page = (int)$_GET['page'];
            $pageSize = (int)$_GET['pageSize'];
            $result = $this->teachingModel->getSubjectStatistic($classId, $subjectId,$keyword,$page,$pageSize);
            echo json_encode(['code' => 200, 'msg' => 'Thống kê môn học theo lớp thành công!','result'=>$result]);
        } else {
            echo json_encode([
                'code' => 405,
                'msg' => 'Phương thức không được phép'
            ]);
        }
    }


    public function StatisticByClassAndSubject()
    {
        $classId = (int) $_GET['classId'];
        $subjectId = (int) $_GET['subjectId'];
        $keyword = $_GET['keyword'];
        $page = (int) $_GET['page'];
        $pageSize = (int) $_GET['pageSize'];
        echo json_encode(['page' => $page, 'class' => $classId, 'subject' => $subjectId]);
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
}