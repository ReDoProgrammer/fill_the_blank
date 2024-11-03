<?php

class BaseController
{
    protected function view($view, $data = [], $title = '')
    {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';

        if (file_exists($viewFile)) {
            extract($data);
            require_once $viewFile;
        } else {
            die('View file not found.');
        }
    }
}
