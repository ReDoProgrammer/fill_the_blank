<?php
require_once 'app/models/SubjectModel.php';
class SubjectController extends Controller
{
    protected $subjectModel;
    public function __construct()
    {
        $this->subjectModel = new SubjectModel();
    }

    //Trả về mảng các môn học có bài học
    public function HaveLessions(){
        session_start();
        $teacher_id = $_SESSION['teacher_logged_in']['id'];
        $resulst = $this->subjectModel->getSubjectsWithLessionsByTeacherId($teacher_id);
        echo json_encode([
            'code'=>200,
            'msg'=>'Lấy danh sách môn học có bài học thành công!',
            'subjects'=>$resulst
        ]);
    }
}
