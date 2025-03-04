<?php

require_once 'DBCredentials.php';

Class Database extends DBCredentials
{
    protected function connect(): PDO
    {
        
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        
        $pdo = new PDO($dsn, $this->user, $this->password, $options);
        return $pdo;
    }
}