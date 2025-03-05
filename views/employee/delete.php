<?php

require_once 'src/employee.php';

$employeeID = (int) ($_GET['id'] ?? 0);

if ($employeeID === 0) {
    die('No employee ID specified.');
}

$employee = new Employee();
$employeeData = $employee->getByID($employeeID);

if (!$employeeData || empty($employeeData[0])) {
    die('No employee found.');
}

$employeeData = $employeeData[0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['confirm'] === 'yes') {
        if ($employee->delete($employeeID)) {
            header('Location: index.php');
            exit;
        } else {
            $errorMessage = 'It was not possible to delete the employee.';
        }
    } else {
        header('Location: index.php');
        exit;
    }
}

include_once 'views/header.php';
?>

<nav>
  <ul>
    <li><a href="index.php" title="Homepage">Back</a></li>
  </ul>
</nav>

<main>
  <h1>Delete Employee</h1>
  
  <?php if (isset($errorMessage)): ?>
    <p class="error"><?=$errorMessage ?></p>
  <?php else: ?>
    <p>Are you sure you want to delete this employee?</p>
    <ul>
      <li><strong>First name:</strong> <?=$employeeData['first_name'] ?></li>
      <li><strong>Last name:</strong> <?=$employeeData['last_name'] ?></li>
      <li><strong>Email:</strong> <?=$employeeData['email'] ?></li>
      <li><strong>Birth date:</strong> <?=$employeeData['birth_date'] ?></li>
      <li><strong>Department:</strong> <?=$employeeData['department_name'] ?></li>
    </ul>

    <form method="POST">
      <div style="display: flex; gap: 1rem;">
        <button type="submit" name="confirm" value="yes" style="background-color: red; color: white;">Delete</button>
        <button type="submit" name="confirm" value="no">Cancel</button>
      </div>
    </form>
  <?php endif; ?>
</main>

<?php include_once 'views/footer.php'; ?>