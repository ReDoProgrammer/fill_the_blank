<?php
require_once 'app/models/UserModel.php';

class ClassroomController extends Controller{
    protected $userModel;
    

    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    public function index()
    {
        $this->view('teacher/classroom/index', [], 'Dashboard','lecture');
    }

    public function getStudentsByClassId(){
        $teachingId = $_GET['classId'];
        $page  = $_GET['page'];
        $pageSize = $_GET['pageSize'];
        $keyword = $_GET['keyword'];
        $result = $this->userModel->listByTeachingId($teachingId, $keyword, $page, $pageSize);
        echo json_encode([
            'code'=>200,
            'msg'=>'Lấy danh sách học viên theo lớp thành công!',
            'data'=>$result
        ]);
    }
}