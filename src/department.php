<?php

require_once 'database.php';
require_once 'logger.php';

/**
 * It retrieves all department from the database
 * @param $pdo A PDO database connection
 * @return An associative array with department information,
 *         or false if there was an error
 */
function getAllDepartments(PDO $pdo): array|false
{
    $sql =<<<SQL
        SELECT nDepartmentID, cName
        FROM department
        ORDER BY cName
    SQL;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logText('Error getting all departments: ', $e);
        return false;
    }
}