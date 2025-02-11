<?php

function connect(): PDO
{
    require_once 'db_credentials.php';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    $pdo = new PDO($dsn, $user, $password, $options);
    return $pdo;
}

function getAllEmployees(PDO $pdo): array
{
    $sql =<<<SQL
        SELECT cFirstName, cLastName, dBirth
        FROM employee
        ORDER BY cFirstName, cLastName;
    SQL;
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}