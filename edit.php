
<?php

require_once 'config/database.php';
require_once 'models/Employee.php';

// Database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Database connection unavailable.");
}

// Create Employee object
$employee = new Employee($db);

// Get employee ID from URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    die("Invalid employee ID.");
}

// Retrieve existing employee
$employeeData = $employee->getById($id);

if (!$employeeData) {
    die("Employee not found.");
}

$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $salary = trim($_POST['salary'] ?? '');

    // Validate employee name
    if ($name === '') {
        $errors[] = "Employee name is required.";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    }

    // Validate phone
    if ($phone === '' || !preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors[] = "Enter a valid phone number.";
    }

    // Validate department
    $departments = ['IT', 'HR', 'Finance', 'Sales', 'Marketing'];

    if (!in_array($department, $departments, true)) {
        $errors[] = "Please select a valid department.";
    }

    // Validate salary
    if (
        $salary === '' ||
        !is_numeric($salary) ||
        $salary < 0 ||
        $salary > 99999999.99
    ) {
        $errors[] = "Enter a valid salary.";
    }

    // Update employee
    if (empty($errors)) {

        try {

            $result = $employee->update(
                $id,
                $name,
                $email,
                $phone,
                $department,
                $salary
            );

            if ($result) {
                header("Location: index.php?updated=1");
                exit;
            }

            $errors[] = "Unable to update employee.";

        } catch (PDOException $e) {

            if ($e->getCode() == '23000') {
                $errors[] = "This email address is already registered.";
            } else {
                error_log($e->getMessage());
                $errors[] = "Something went wrong while updating.";
            }
        }
    }

    // Keep entered values if validation fails
    $employeeData = [
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'department' => $department,
        'salary' => $salary
    ];
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Employee</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-7">

            <div class="card shadow">

                <div class="card-header bg-warning">

                    <h3 class="mb-0">Edit Employee</h3>

                </div>

                <div class="card-body">

                    <?php if (!empty($errors)) { ?>

                        <div class="alert alert-danger">

                            <ul class="mb-0">

                                <?php foreach ($errors as $error) { ?>

                                    <li>
                                        <?php echo htmlspecialchars($error); ?>
                                    </li>

                                <?php } ?>

                            </ul>

                        </div>

                    <?php } ?>

                    <form method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                Employee Name
                            </label>

                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($employeeData['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                   required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Email Address
                            </label>

                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($employeeData['email'], ENT_QUOTES, 'UTF-8'); ?>"
                                   required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Phone Number
                            </label>

                            <input type="tel"
                                   name="phone"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($employeeData['phone'], ENT_QUOTES, 'UTF-8'); ?>"
                                   required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Department
                            </label>

                            <select name="department"
                                    class="form-select"
                                    required>

                                <?php
                                $departments = [
                                    'IT',
                                    'HR',
                                    'Finance',
                                    'Sales',
                                    'Marketing'
                                ];

                                foreach ($departments as $dept) {
                                ?>

                                    <option value="<?php echo $dept; ?>"
                                        <?php echo ($employeeData['department'] === $dept) ? 'selected' : ''; ?>>

                                        <?php echo $dept; ?>

                                    </option>

                                <?php } ?>

                            </select>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Salary
                            </label>

                            <input type="number"
                                   name="salary"
                                   class="form-control"
                                   min="0"
                                   max="99999999.99"
                                   step="0.01"
                                   value="<?php echo htmlspecialchars((string)$employeeData['salary'], ENT_QUOTES, 'UTF-8'); ?>"
                                   required>

                        </div>

                        <button type="submit"
                                class="btn btn-warning">

                            Update Employee

                        </button>

                        <a href="index.php"
                           class="btn btn-secondary">

                            Cancel

                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
