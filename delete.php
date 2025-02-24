<?php 

require_once 'src/employee.php';
require_once 'src/department.php';

$pdo = connect();

if (!isset($_GET['id'])) {
  die('No employee ID specified.');
}
$employeeID = (int) $_GET['id'];

$employee = getEmployeeByID($pdo, $employeeID);
if (!$employee) {
  die('No employee found');
}
$employee = $employee[0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    if (deleteEmployee($pdo, $employeeID)) {
      header('Location: index.php');
      exit;
    } else {
      $errorMessage = 'It was not possible to delete the employee';
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
    <p>Are you sure you want to delete this employee</p>
    <ul>
      <li><strong>First name:</strong> <?=$employee['first_name'] ?></li>
      <li><strong>Last name:</strong> <?=$employee['last_name'] ?></li>
      <li><strong>Email:</strong> <?=$employee['email'] ?></li>
      <li><strong>Birth date:</strong> <?=$employee['birth_date'] ?></li>
      <li><strong>Department:</strong> <?=$employee['department_name'] ?></li>
    </ul>

    <form method="POST">
      <button type="submit" name="confirm" value="yes">Confirm</button>
      <button type="submit" name="confirm" value="no">Cancel</button>
    </form>
  <?php endif; ?>    
</main>



<?php include_once 'views/footer.php'; ?>