<?php

require_once 'src/database.php';

echo '<pre>';

$pdo = connect();
print_r(getAllEmployees($pdo));