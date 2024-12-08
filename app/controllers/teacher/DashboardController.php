<?php

class DashboardController extends TeacherController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view('teacher/dashboard/index', [], 'Dashboard','lecture');
    }
}
