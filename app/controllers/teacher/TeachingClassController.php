<?php
class TeachingClassController extends Controller{
    public function index()
    {
        $this->view('teacher/teachingclass/index', [], 'Lớp giảng dạy','teacher');
    }
}