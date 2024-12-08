<?php
require_once 'app/models/QuestionModel.php';
class QuestionController extends
Controller {
    protected $questionModel;
    public function __construct()
    {
        $this->questionModel = new QuestionModel();
    }
    public function index()
    {
        $this->view('teacher/question/index', [], 'Quản lý câu hỏi', 'teacher');
    }

}
