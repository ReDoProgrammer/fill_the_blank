<?php

class Controller {
    public function model($model) {
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model();
    }
    
    protected function view($view, $data = [], $title = '', $layout = 'main') {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        $layoutFile = __DIR__ . '/../views/layouts/' . $layout . '.php';
        
        if (file_exists($viewFile) && file_exists($layoutFile)) {
            // Extract the data array to variables
            extract($data);
            
            ob_start();
            include $viewFile;
            $content = ob_get_clean();
            require_once $layoutFile;
        } else {
            die('View or Layout file not found.');
        }
    }
}
