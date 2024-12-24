<?php
class StatisticController extends Controller
{
    public function index()
    {
        $this->view('teacher/statistic/index', [], 'Thống kê ôn tập', 'lecture');
    }

    public function StatisticByClassAndSubject(){
        $classId = (int)$_GET['classId'];
        $subjectId = (int)$_GET['subjectId'];
        $keyword = $_GET['keyword'];
        $page = (int)$_GET['page'];  
        $pageSize = (int)$_GET['pageSize'];
        echo json_encode(['page'=>$page,'class'=>$classId,'subject'=>$subjectId]);
    }
}