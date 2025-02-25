<?php

require_once 'src/employee.php';
require_once 'src/department.php';

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

$department = new Department();
$departments = $department->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_POST['confirm'] === 'no') {
    header('Location: index.php');
    exit;
  }

  $validationErrors = $employee->validate($_POST);

  if (!empty($validationErrors)) {
    $errorMessage = join(', ', $validationErrors);
  } else {
    if ($employee->update($employeeID, $_POST)) {
      header('Location: index.php');
      exit;
    } else {
      $errorMessage = 'It was not possible to update the employee.';
    }
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
  <?php if (isset($errorMessage)): ?>
    <p class="error"><?= $errorMessage ?></p>
  <?php endif; ?>

  <form action="edit.php?id=<?= $employeeID ?>" method="POST">
    <div>
      <label for="txtFirstName">First name</label>
      <input type="text" id="txtFirstName" name="first_name"
        value="<?= htmlspecialchars($_POST['first_name'] ?? ($employeeData['first_name'] ?? '')) ?>">
    </div>
    <div>
      <label for="txtLastName">Last name</label>
      <input type="text" id="txtLastName" name="last_name"
        value="<?= htmlspecialchars($_POST['last_name'] ?? ($employeeData['last_name'] ?? '')) ?>">
    </div>
    <div>
      <label for="txtEmail">Email</label>
      <input type="email" id="txtEmail" name="email"
        value="<?= htmlspecialchars($_POST['email'] ?? ($employeeData['email'] ?? '')) ?>">
    </div>
    <div>
      <label for="txtBirthDate">Birth date</label>
      <input type="date" id="txtBirthDate" name="birth_date"
        value="<?= htmlspecialchars($_POST['birth_date'] ?? ($employeeData['birth_date'] ?? '')) ?>">
    </div>
    <div>
      <label for="cmbDepartment">Department</label>
      <select name="department" id="cmbDepartment">
        <?php foreach ($departments as $department): ?>
          <option value="<?= $department['nDepartmentID'] ?>"
            <?= isset($employeeData['department_id']) && $department['nDepartmentID'] == $employeeData['department_id'] ? 'selected' : '' ?>>
            <?= $department['cName'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div style="display: flex; gap: 1rem;">
      <button type="submit" name="confirm" value="yes">Update</button>
      <button type="submit" name="confirm" value="no">Cancel</button>
    </div>
  </form>
</main>

<?php include_once 'views/footer.php'; ?>