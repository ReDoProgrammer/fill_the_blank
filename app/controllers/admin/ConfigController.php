<?php
require_once 'app/models/ConfigModel.php';

class ConfigController extends AdminController
{
    protected $configModel;

    public function __construct()
    {
        $this->configModel = new ConfigModel();
    }

    public function index()
    {
        $this->view('admin/config/index', [], 'Cấu hình bài thi trắc nghiệm', 'admin');
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject_id = $_POST['subject_id'];
            $title = $_POST['title'];

            // Kiểm tra nếu levels là chuỗi thì mới giải mã
            $levels = isset($_POST['levels']) ? json_decode($_POST['levels'], true) : [];

            header('Content-Type: application/json');
            // Kiểm tra lỗi khi giải mã JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo json_encode(['code' => 500, 'msg' => 'Lỗi khi giải mã dữ liệu JSON']);
                return;
            }

            // // Debug output
            // echo json_encode(['levels' => $levels]);

            $result = $this->configModel->createConfig($subject_id, $title, $levels);
            echo json_encode($result);
        }
    }


    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) $_POST['id'];
            $title = $_POST['title'];
            $levels = isset($_POST['levels']) && is_string($_POST['levels']) ? json_decode($_POST['levels'], true) : [];

            header('Content-Type: application/json');
            $result = $this->configModel->updateConfig($id, $title, $levels);
            echo json_encode($result);
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) $_POST['id'];

            header('Content-Type: application/json');
            $result = $this->configModel->deleteConfig($id);
            echo json_encode($result);
        }
    }

    public function detail()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $id = (int) $_GET['id'];

            header('Content-Type: application/json');
            $result = $this->configModel->getConfigById($id);
            echo json_encode($result);
        }
    }

    public function list()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $keyword = $_GET['keyword'];
            header('Content-Type: application/json');
            $result = $this->configModel->getAllConfigs($keyword);
            echo json_encode($result);
        }
    }

    public function listBySubject()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $subject_id = $_GET['subject_id'];
            $number_of_questions = $_GET['number_of_questions'];
            header('Content-Type: application/json');
            $result = $this->configModel->getConfigsBySubjectQuestionsAndMarks($subject_id,$number_of_questions);
            echo json_encode($result);
        }
    }


}
