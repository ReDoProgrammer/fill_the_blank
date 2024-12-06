<?php

class App
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        if (empty($url)) {
            $this->loadDefaultController();
            return;
        }

        // Xác định Controller
        if (isset($url[0]) && $url[0] === 'admin') {
            array_shift($url); // Loại bỏ 'admin' khỏi mảng URL
            if (isset($url[0]) && !empty($url[0])) {
                $controllerFile = 'app/controllers/admin/' . ucfirst($url[0]) . 'Controller.php';
                if (file_exists($controllerFile)) {
                    $this->controller = ucfirst($url[0]) . 'Controller';
                    require_once $controllerFile;
                    unset($url[0]);
                } else {
                    $this->controller = 'AdminAuthController'; // Controller mặc định nếu không tìm thấy
                    require_once 'app/controllers/admin/' . $this->controller . '.php';
                }
            } else {
                $this->controller = 'DashboardController'; // Controller mặc định khi chỉ có 'admin'
                require_once 'app/controllers/admin/' . $this->controller . '.php';
            }
        } elseif (isset($url[0]) && $url[0] === 'teacher') {
            array_shift($url); // Loại bỏ 'teacher' khỏi mảng URL
            if (isset($url[0]) && !empty($url[0])) {
                $controllerFile = 'app/controllers/teacher/' . ucfirst($url[0]) . 'Controller.php';
                if (file_exists($controllerFile)) {
                    $this->controller = ucfirst($url[0]) . 'Controller';
                    require_once $controllerFile;
                    unset($url[0]);
                } else {
                    $this->controller = 'TeacherAuthController'; // Controller mặc định nếu không tìm thấy
                    require_once 'app/controllers/teacher/' . $this->controller . '.php';
                }
            } else {
                $this->controller = 'DashboardController'; // Controller mặc định khi chỉ có 'admin'
                require_once 'app/controllers/teacher/' . $this->controller . '.php';
            }
        } else {
            if (isset($url[0])) {
                $controllerFile = 'app/controllers/' . ucfirst($url[0]) . 'Controller.php';
                if (file_exists($controllerFile)) {
                    $this->controller = ucfirst($url[0]) . 'Controller';
                    require_once $controllerFile;
                    unset($url[0]);
                } else {
                    $this->controller = 'UserAuthController'; // Controller mặc định nếu không tìm thấy
                    require_once 'app/controllers/' . $this->controller . '.php';
                }
            } else {
                $this->controller = 'UserAuthController'; // Controller mặc định nếu không có phần tử trong URL
                require_once 'app/controllers/' . $this->controller . '.php';
            }
        }

        if (!class_exists($this->controller)) {
            die("Class {$this->controller} does not exist.");
        }

        $this->controller = new $this->controller;

        // Xác định phương thức (action) trong Controller
        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        } else {
            $this->method = 'index'; // Hoặc xử lý lỗi 404
        }

        // Xử lý tham số dựa trên phương thức HTTP
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        if ($requestMethod === 'POST') {
            $postData = $_POST;
            $this->params = $postData ? array_values($postData) : [];
        } elseif ($requestMethod === 'GET') {
            $this->params = !empty($url) ? array_values($url) : [];
        } else {
            $this->params = [];
        }

        if (method_exists($this->controller, $this->method)) {
            call_user_func_array([$this->controller, $this->method], $this->params);
        } else {
            die("Method {$this->method} does not exist in controller {$this->controller}.");
        }
    }

    public function get($route, $handler)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $this->matchRoute($route)) {
            $this->handleRoute($handler);
        }
    }

    public function post($route, $handler)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->matchRoute($route)) {
            $this->handleRoute($handler);
        }
    }

    protected function matchRoute($route)
    {
        $url = $this->parseUrl();
        $route = trim($route, '/');
        $urlString = implode('/', $url);

        return preg_match("#^$route$#", $urlString);
    }

    protected function handleRoute($handler)
    {
        list($controller, $method) = explode('@', $handler);

        $controllerFile = 'app/controllers/' . ucfirst($controller) . 'Controller.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controller = ucfirst($controller) . 'Controller';
            if (class_exists($controller)) {
                $controllerInstance = new $controller();
                $controllerInstance->$method();
            } else {
                die("Class {$controller} does not exist.");
            }
        } else {
            die("Controller file for {$controller} does not exist.");
        }
    }

    public function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }

    protected function loadDefaultController()
    {
        require_once 'app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
}
