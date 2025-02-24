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

$departments = getAllDepartments($pdo);
if (!$departments) {
  die('Error retrieving deparments');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $validationErrors = validateEmployee($_POST);

  if (!empty($validationErrors)) {
    $errorMessage = join(', ', $validationErrors);
  } else {
    if (updateEmployee($pdo, $employeeID, $_POST)) {
      header('Location: index.php');
      exit;
    } else {
      $errorMessage = 'It was not possible to update the employee';
    }
  }
} else {
  $_POST['first_name'] = $employee['first_name'];
  $_POST['last_name'] = $employee['last_name'];
  $_POST['email'] = $employee['email'];
  $_POST['birth_date'] = $employee['birth_date'];
  $_POST['department'] = $employee['department_id']; 
}

include_once 'views/header.php';

?>

<nav>
  <ul>
    <li><a href="index.php" title="Homepage">Back</a></li>
  </ul>
</nav>

<main>
  <?php if (isset($errorMessage)): ?>
    <section>
      <p class="error"><?=$errorMessage ?></p>
    </section>
  <?php endif; ?>

  <form action="edit.php?id=<?=$employeeID ?>" method="POST">
    <div>
      <label for="txtFirtName">First name</label>
      <input 
        type="text"
        id="txtFirtName"
        name="first_name"
        value="<?=htmlspecialchars($_POST['first_name'] ?? '') ?>"
      >
    </div>
    <div>
      <label for="txtLastName">Last name</label>
      <input 
        type="text"
        id="txtLastName"
        name="last_name"
        value="<?=htmlspecialchars($_POST['last_name'] ?? '') ?>"  
      >
    </div>
    <div>
      <label for="txtEmail">Email</label>
      <input 
        type="email"
        id="txtEmail"
        name="email"
        value="<?=htmlspecialchars($_POST['email'] ?? '') ?>"  
      >
    </div>
    <div>
      <label for="txtBirthDate">Birth date</label>
      <input 
        type="date"
        id="txtBirthDate"
        name="birth_date"
        value="<?=htmlspecialchars($_POST['birth_date'] ?? '') ?>"  
      >
    </div>
    <div>
      <label for="cmbDeparment">Department</label>
      <select name="department" id="cmbDeparment">
        <?php foreach ($departments as $department): ?>
          <option 
            value="<?=$department['nDepartmentID'] ?>"
            <?php if (($department['nDepartmentID'] ?? '') == ($_POST['department'] ?? '')) echo 'selected'; ?>
          >
            <?=$department['cName'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <button type="submit">Update Employee</button>
    </div>
  </form>
</main>

<?php include_once 'views/footer.php'; ?>

