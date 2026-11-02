
<?php

require_once 'config/database.php';
require_once 'models/Employee.php';

// Establish database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Database connection unavailable.");
}

// Create Employee object
$employee = new Employee($db);

$errors = [];

// Check whether form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Receive form values
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $salary = trim($_POST['salary'] ?? '');

    // Validate input
    if ($name === '') {
        $errors[] = "Employee name is required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if ($phone === '' || !preg_match('/^[0-9]{10,15}$/', $phone)) {
        $errors[] = "Enter a valid phone number (10-15 digits).";
    }

    if ($department === '') {
        $errors[] = "Department is required.";
    }

    if ($salary === '' || !is_numeric($salary) || $salary < 0 || $salary > 99999999.99) {
        $errors[] = "Enter a valid salary.";
    }

    // Insert employee if validation succeeds
    if (empty($errors)) {

        try {

            $result = $employee->create(
                $name,
                $email,
                $phone,
                $department,
                $salary
            );

            if ($result) {
                header("Location: index.php?success=1");
                exit;
            }

        } catch (PDOException $e) {

            if ($e->getCode() == '23000') {
                $errors[] = "This email address already exists.";
            } else {
                error_log($e->getMessage());
                $errors[] = "Unable to save employee. Please try again.";
            }

        }
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Employee</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-7">

            <div class="card shadow">

                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Add New Employee</h3>
                </div>

                <div class="card-body">

                    <!-- Display validation errors -->
                    <?php if (!empty($errors)) { ?>

                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error) { ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php } ?>
                            </ul>
                        </div>

                    <?php } ?>

                    <form method="POST" action="">

                        <div class="mb-3">
                            <label class="form-label">Employee Name</label>

                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   placeholder="Enter employee name"
                                   value="<?php echo htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>

                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   placeholder="Enter email address"
                                   value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>

                            <input type="tel"
                                   name="phone"
                                   class="form-control"
                                   placeholder="Enter phone number"
                                   value="<?php echo htmlspecialchars($phone ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Department</label>

                            <select name="department"
                                    class="form-select"
                                    required>

                                <option value="">Select Department</option>

                                <?php
                                $departments = ['IT', 'HR', 'Finance', 'Sales', 'Marketing'];

                                foreach ($departments as $dept) {
                                ?>

                                    <option value="<?php echo $dept; ?>"
                                        <?php echo (($department ?? '') === $dept) ? 'selected' : ''; ?>>
                                        <?php echo $dept; ?>
                                    </option>

                                <?php } ?>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Salary</label>

                            <input type="number"
                                   name="salary"
                                   class="form-control"
                                   placeholder="Enter salary"
                                   min="0"
                                   max="99999999.99"
                                   step="0.01"
                                   value="<?php echo htmlspecialchars($salary ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                   required>
                        </div>

                        <button type="submit" class="btn btn-success">
                            Add Employee
                        </button>

                        <a href="index.php" class="btn btn-secondary">
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
