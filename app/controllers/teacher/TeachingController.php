<?php
require_once 'app/models/TeachingModel.php';

class TeachingController extends Controller{
    protected $teachingModel;
    public function __construct()
    {
        $this->teachingModel = new TeachingModel();
    }

   
    public function ownclasses(){
        session_start();
        $teacher_id = $_SESSION['teacher_logged_in']['id'];
        $result = $this->teachingModel->getClassesByTeacherId($teacher_id);
        echo json_encode([
            'code'=>200,
            'msg'=>'Lấy danh sách lớp giảng dạy thành công!',
            'classes'=>$result
        ]);
    }
}