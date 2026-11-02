

<?php

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'config/database.php';
require_once 'models/Employee.php';

// Step 1: Connect to database
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("Database connection unavailable.");
}

// Step 2: Create Employee object
$employee = new Employee($db);

// Step 3: Retrieve all employee records
try {
    $employees = $employee->readAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Unable to retrieve employee records.");
}

// Step 4: Calculate total employees
$totalEmployees = count($employees);

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Employee Management System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Employee Management System</h2>

        <a href="create.php" class="btn btn-primary">
            + Add Employee
        </a>

    </div>

    <!-- Success Message -->
    <?php if (isset($_GET['success'])) { ?>

        <div class="alert alert-success">
            Employee added successfully!
        </div>

    <?php } ?>

    <?php if (isset($_GET['updated'])) { ?>

    <div class="alert alert-success">
        Employee updated successfully!
    </div>

    <?php } ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Employee deleted successfully!

            <button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Employee Count -->
    <div class="alert alert-info">

        Total Employees:
        <strong>
            <?php echo $totalEmployees; ?>
        </strong>

    </div>

    <!-- Employee Table -->
    <div class="card shadow">

        <div class="card-header bg-dark text-white">

            <h5 class="mb-0">Employee Records</h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="table-dark">

                        <tr>

                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Department</th>
                            <th>Salary</th>
                            <th>Created At</th>
                            <th>Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($employees)) { ?>

                            <?php foreach ($employees as $row) { ?>

                                <tr>

                                    <td>
                                        <?php echo htmlspecialchars((string)$row['id']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['email']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['phone']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['department']); ?>
                                    </td>

                                    <td>
                                        ₹<?php echo number_format((float)$row['salary'], 2); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['created_at']); ?>
                                    </td>

                                    <!-- <td>
                                        <a href="edit.php?id=<?php echo (int)$row['id']; ?>"
                                            class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                    </td> -->

                                    <td>
                                        <a href="edit.php?id=<?php echo (int)$row['id']; ?>"
                                            class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <form action="delete.php" method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this employee?');">

                                            <input type="hidden" name="id"
                                                value="<?php echo (int)$row['id']; ?>">

                                            <input type="hidden" name="csrf_token"
                                                value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Delete
                                            </button>

                                        </form>
                                    </td>

                                </tr>

                            <?php } ?>

                        <?php } else { ?>

                            <tr>

                                <td colspan="8" class="text-center">

                                    No employee records found.

                                </td>

                            </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>
