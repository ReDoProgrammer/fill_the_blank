<?php
class StatisticController extends Controller
{
    public function index()
    {
        $this->view('teacher/statistic/index', [], 'Thống kê ôn tập', 'lecture');
    }
}