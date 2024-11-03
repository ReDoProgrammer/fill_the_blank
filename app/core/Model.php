<?php
// app/core/Model.php

class Model
{
    protected $pdo;

    public function __construct()
    {
        $config = require 'app/core/config.php';

        try {
            $dsn = 'mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'];
            $this->pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // app/models/UserModel.php

// app/core/Model.php

public function fetchAll($sql, $params = []) {
    // Chuẩn bị câu lệnh SQL
    $stmt = $this->pdo->prepare($sql);
    
    // Chạy câu lệnh SQL với các tham số
    $stmt->execute($params);
    return $stmt->fetchAll();
}


    public function fetch($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function execute($sql, $params = [])
    {
        return $this->query($sql, $params)->rowCount();
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}
