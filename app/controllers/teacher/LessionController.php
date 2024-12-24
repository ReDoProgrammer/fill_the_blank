<?php
require_once 'app/models/LessionModel.php';
class LessionController extends TeacherController
{
    protected $lessionModel;

    public function __construct()
    {
        $this->lessionModel = new LessionModel();
    }

        // Lấy tất cả bài học theo subject_id
        public function getBySubject()
        {
            header('Content-Type: application/json');
            if ($_SERVER["REQUEST_METHOD"] === "GET") {
                $subjectId = isset($_GET['subject_id']) ? (int) $_GET['subject_id'] : 0;
    
                // Gọi hàm getBySubject từ model
                $lessions = $this->lessionModel->getBySubject($subjectId);
                echo json_encode([
                    'code' => 200,
                    'msg' => 'Lấy danh sách bài học thành công!',
                    'lessions' => $lessions
                ]);
            } else {
                echo json_encode([
                    'code' => 405,
                    'msg' => 'Phương thức truy vấn không hợp lệ!'
                ]);
            }
        }
    
}