<?php

class DashboardController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->view('admin/dashboard/index', [], 'Dashboard','admin');
    }
}
