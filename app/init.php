<?php
define('BASE_URL', 'http://localhost/filltheblank');
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Đặt múi giờ mặc định theo giờ VN

spl_autoload_register(function ($class) {
    require_once 'core/' . $class . '.php';
});
