<?php
class ClassroomController extends Controller{
    public function index()
    {
        $this->view('teacher/classroom/index', [], 'Dashboard','lecture');
    }
}