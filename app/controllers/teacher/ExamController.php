<?php
class ExamController extends Controller{
    public function index()
    {
        $this->view('teacher/exam/index', [], 'Danh sách đề thi','teacher');
    }
}