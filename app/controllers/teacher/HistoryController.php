<?php
class HistoryController extends Controller{
    public function index()
    {
        $this->view('teacher/history/index', [], 'Lịch sử ôn tập','lecture');
    }
}