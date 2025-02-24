<?php

require_once 'database.php';
require_once 'logger.php';

/**
 * It retrieves all employees from the database
 * @param $pdo A PDO database connection
 * @return An associative array with employee information,
 *         or false if there was an error
 */
function getAllEmployees(PDO $pdo): array|false
{
    $sql =<<<SQL
        SELECT nEmployeeID, cFirstName, cLastName, dBirth
        FROM employee
        ORDER BY cFirstName, cLastName;
    SQL;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logText('Error getting all employees: ', $e);
        return false;
    }
}

/**
 * It retrieves employees from the database based 
 * on a text search on the first and last name
 * @param $pdo A PDO database connection
 * @param $searchText The text to search in the database
 * @return An associative array with employee information,
 *         or false if there was an error
 */
function searchEmployees(PDO $pdo, string $searchText): array|false
{
    $sql =<<<SQL
        SELECT nEmployeeID, cFirstName, cLastName, dBirth
        FROM employee
        WHERE cFirstName LIKE :firstName
           OR cLastName LIKE :lastName
        ORDER BY cFirstName, cLastName;
    SQL;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':firstName', "%$searchText%");
        $stmt->bindValue(':lastName', "%$searchText%");
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logText('Error searching for employees: ', $e);
        return false;
    }
}

/**
 * It retrieves information of an employee
 * @param $pdo A PDO database connection
 * @param $employeeID The ID of the employee
 * @return An associative array with employee information,
 *         or false if there was an error
 */
function getEmployeeByID(PDO $pdo, int $employeeID): array|false
{
    $sql =<<<SQL
        SELECT 
            employee.cFirstName AS first_name, 
            employee.cLastName AS last_name, 
            employee.cEmail AS email, 
            employee.dBirth AS birth_date, 
            employee.nDepartmentID AS department_id, 
            department.cName AS department_name
        FROM employee INNER JOIN department
            ON employee.nDepartmentID = department.nDepartmentID
        WHERE nEmployeeID = :employeeID;
    SQL;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':employeeID', $employeeID);
        $stmt->execute();

        return $stmt->fetchAll();
    } catch (PDOException $e) {
        logText('Error retrieving employee information: ', $e);
        return false;
    }
}

/**
 * It validates employee data before putting it into the database
 * @param $employee Employee data in an associative array
 * @return An array with all validation error messages
 */
function validateEmployee(array $employee): array
{
    $firstName = trim($employee['first_name'] ?? '');
    $lastName = trim($employee['last_name'] ?? '');
    $email = trim($employee['email'] ?? '');
    $birthDate = trim($employee['birth_date'] ?? '');
    $departmentID = trim($employee['department_id'] ?? '');

    $validationErrors = [];

    if ($firstName === '') {
        $validationErrors[] = 'First name is mandatory.';
    }
    if ($lastName === '') {
        $validationErrors[] = 'Last name is mandatory.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validationErrors[] = 'Invalid email format.';
    }

    if ($birthDate === '') {
        $validationErrors[] = 'Birth date is mandatory.';
    } else {
        try {
            $dateTime = new DateTime($birthDate);
            $today = new DateTime();
            $age = $today->diff($dateTime)->y;

            if ($age < 16) {
                $validationErrors[] = 'Employee must be minimum 16 years old.';
            }
        } catch (Exception $e) {
            $validationErrors[] = 'Invalid birth date format';
        }
    }

    return $validationErrors;
}

/**
 * It inserts a new employee in the database
 * @param $pdo A PDO database connection
 * @param $employee An associative array with employee information
 * @return true if the insert was successful,
 *         or false if there was an error
 */
function insertEmployee(PDO $pdo, array $employee): bool
{
    $sql =<<<SQL
        INSERT INTO employee
            (cFirstName, cLastName, cEmail, dBirth, nDepartmentID)
        VALUES
            (:firstName, :lastName, :email, :birthDate, :departmentID);
    SQL;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':firstName', $employee['first_name']);
        $stmt->bindValue(':lastName', $employee['last_name']);
        $stmt->bindValue(':email', $employee['email']);
        $stmt->bindValue(':birthDate', $employee['birth_date']);
        $stmt->bindValue(':departmentID', $employee['department']);
        $stmt->execute();

        return $stmt->rowCount() === 1;
    } catch (PDOException $e) {
        logText('Error inserting a new employee: ', $e);
        return false;
    }
}

/**
 * Updates an existing employee in the database
 * @param PDO $pdo A PDO database connection
 * @param int $employeeID The ID of the employee to update
 * @param array $employee An associative array with new employee information
 * @return bool True if the update was successful, otherwise false 
 */
function updateEmployee(PDO $pdo, int $employeeID, array $employee): bool
{
    $sql =<<<SQL
        UPDATE employee
        SET
            cFirstName = :firstName,
            cLastName = :lastName,
            cEmail = :email,
            dBirth = :birthDate,
            nDepartmentID = :departmentID
        WHERE nEmployeeID = :employeeID
    SQL;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':firstName', $employee['first_name']);
        $stmt->bindValue(':lastName', $employee['last_name']);
        $stmt->bindValue(':email', $employee['email']);
        $stmt->bindValue(':birthDate', $employee['birth_date']);
        $stmt->bindValue(':departmentID', $employee['department']);
        $stmt->bindValue(':employeeID', $employeeID, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->rowCount() === 1;
    } catch (PDOException $e) {
        logText('Error updating employee: ', $e);
        return false;
    }
}

/**
 * Deletes an employee by ID
 * @param PDO $pdo A PDO database connection
 * @param int $employeeID The ID of the employee to delete
 * @return bool True if the deletion was successful, otherwise false
 */
function deleteEmployee(PDO $pdo, int $employeeID): bool 
{
    $sql = "DELETE FROM employee WHERE nEmployeeID = :employeeID";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':employeeID', $employeeID, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->rowCount() === 1;
    } catch (PDOException $e) {
        logText('Error deleting employee: ', $e);
        return false;
    }
}