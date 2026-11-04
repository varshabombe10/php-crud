
<?php

session_start();

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include required files
require_once 'config/database.php';
require_once 'models/Employee.php';

// Database connection
$database = new Database();
$db = $database->getConnection();

$employee = new Employee($db);

// Get search and department filters safely
$rawSearch = $_GET['search'] ?? '';
$rawDepartment = $_GET['department'] ?? '';

$search = is_string($rawSearch) ? trim($rawSearch) : '';
$department = is_string($rawDepartment) ? trim($rawDepartment) : '';

// Get available departments
$departments = $employee->getDepartments();

// Validate selected department
if (
    $department !== '' &&
    !in_array($department, $departments, true)
) {
    $department = '';
}

// Pagination settings
$limit = 10;

$rawPage = $_GET['page'] ?? 1;
$page = is_scalar($rawPage) ? max(1, (int)$rawPage) : 1;

// Count matching employees
$totalEmployees = $employee->countFilteredEmployees(
    $search,
    $department
);

// Calculate total pages
$totalPages = max(1, (int)ceil($totalEmployees / $limit));

// Prevent invalid page numbers
$page = min($page, $totalPages);

// Calculate offset
$offset = ($page - 1) * $limit;

// Fetch employees for current page
$employees = $employee->getFilteredEmployees(
    $search,
    $department,
    $limit,
    $offset
);

// Pagination URL - preserve selected filters
function paginationUrl($page, $search, $department)
{
    return '?' . http_build_query([
        'search' => $search,
        'department' => $department,
        'page' => $page
    ]);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Employee Management System</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<div class="main-container">

    <!-- ==============================
         PAGE HEADER
    =============================== -->

    <div class="page-header">

        <div>
            <h2 class="page-title">All Employees</h2>

            <p class="page-subtitle">
                Manage, search and organize your employee records.
            </p>
        </div>

        <div>
            <a href="create.php" class="btn btn-primary">
                + Add Employee
            </a>
        </div>

    </div>


    <!-- ==============================
         SUCCESS MESSAGES
    =============================== -->

    <?php if (isset($_GET['success'])): ?>

        <div class="alert alert-success animate-fade" role="alert">
            Employee added successfully!
        </div>

    <?php endif; ?>


    <?php if (isset($_GET['updated'])): ?>

        <div class="alert alert-success animate-fade" role="alert">
            Employee updated successfully!
        </div>

    <?php endif; ?>


    <?php if (isset($_GET['deleted'])): ?>

        <div class="alert alert-success animate-fade" role="alert">
            Employee deleted successfully!
        </div>

    <?php endif; ?>


    <!-- ==============================
         SEARCH AND FILTER SECTION
    =============================== -->

    <div class="custom-card">

        <h5 class="card-heading">
            Search & Filter Employees
        </h5>

        <form method="GET" action="index.php">

            <div class="row g-3 align-items-end">

                <!-- Search by name or email -->
                <div class="col-12 col-md-6 col-lg-5">

                    <label for="search" class="form-label">
                        Search Employee
                    </label>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        class="form-control"
                        placeholder="Enter employee name or email"
                        value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                    >

                </div>


                <!-- Department dropdown -->
                <div class="col-12 col-md-6 col-lg-4">

                    <label for="department" class="form-label">
                        Department
                    </label>

                    <select
                        name="department"
                        id="department"
                        class="form-select"
                    >

                        <option value="">
                            All Departments
                        </option>

                        <?php foreach ($departments as $dept): ?>

                            <option
                                value="<?php echo htmlspecialchars($dept, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo ($department === $dept) ? 'selected' : ''; ?>
                            >

                                <?php echo htmlspecialchars($dept, ENT_QUOTES, 'UTF-8'); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- Filter buttons -->
                <div class="col-12 col-lg-3">

                    <div class="filter-actions">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Apply Filters
                        </button>

                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >
                            Reset
                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>


    <!-- ==============================
         EMPLOYEE RECORDS SECTION
    =============================== -->

    <div class="custom-card">

        <!-- Table heading -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

            <div>
                <h5 class="card-heading mb-1">
                    Employee Records
                </h5>

                <small class="text-muted">
                    View and manage your employee information.
                </small>
            </div>


            <div class="d-flex align-items-center gap-2 flex-wrap">

                <span class="total-badge">
                    Total: <?php echo $totalEmployees; ?>
                </span>

                <a
                    href="create.php"
                    class="btn btn-success"
                >
                    + Add Employee
                </a>

            </div>

        </div>


        <!-- ==============================
             EMPLOYEE TABLE
        =============================== -->

        <div class="table-responsive">

            <table class="table employee-table">

                <thead>

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

                    <?php if (!empty($employees)): ?>

                        <?php foreach ($employees as $row): ?>

                            <tr>

                                <!-- Employee ID -->
                                <td>
                                    <?php echo (int)$row['id']; ?>
                                </td>


                                <!-- Employee Name -->
                                <td>
                                    <strong>
                                        <?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </strong>
                                </td>


                                <!-- Email -->
                                <td>
                                    <?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>


                                <!-- Phone -->
                                <td>
                                    <?php echo htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>


                                <!-- Department -->
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?php echo htmlspecialchars($row['department'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>


                                <!-- Salary -->
                                <td>
                                    ₹<?php echo htmlspecialchars($row['salary'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>


                                <!-- Created Date -->
                                <td>
                                    <?php echo htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>


                                <!-- Actions -->
                                <td>

                                    <div class="d-flex gap-2">

                                        <!-- Edit button -->
                                        <a
                                            href="edit.php?id=<?php echo (int)$row['id']; ?>"
                                            class="btn btn-warning btn-sm"
                                        >
                                            Edit
                                        </a>


                                        <!-- Delete form -->
                                        <form
                                            action="delete.php"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this employee?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php echo (int)$row['id']; ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-danger btn-sm"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                    <?php else: ?>

                        <!-- Empty result message -->
                        <tr>

                            <td colspan="8" class="text-center py-5">

                                <div class="animate-fade">

                                    <h6 class="text-secondary">
                                        No Employees Found
                                    </h6>

                                    <p class="text-muted mb-3">
                                        No matching employee records were found.
                                        Try changing your search or department filter.
                                    </p>

                                    <a
                                        href="index.php"
                                        class="btn btn-outline-primary btn-sm"
                                    >
                                        Clear Filters
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>


        <!-- ==============================
             PAGINATION
        =============================== -->

        <?php if ($totalEmployees > 0): ?>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4">

                <!-- Record count -->
                <p class="mb-0 text-muted">

                    Showing

                    <strong>
                        <?php echo $offset + 1; ?>
                    </strong>

                    to

                    <strong>
                        <?php echo $offset + count($employees); ?>
                    </strong>

                    of

                    <strong>
                        <?php echo $totalEmployees; ?>
                    </strong>

                    employees

                </p>


                <!-- Pagination navigation -->
                <nav aria-label="Employee pagination">

                    <ul class="pagination mb-0">


                        <!-- Previous button -->
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">

                            <a
                                class="page-link"
                                href="<?php echo htmlspecialchars(paginationUrl(max(1, $page - 1), $search, $department), ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo ($page <= 1) ? 'aria-disabled="true" tabindex="-1"' : ''; ?>
                            >
                                Previous
                            </a>

                        </li>


                        <!-- Page numbers -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                            <li class="page-item <?php echo ($page === $i) ? 'active' : ''; ?>">

                                <a
                                    class="page-link"
                                    href="<?php echo htmlspecialchars(paginationUrl($i, $search, $department), ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo ($page === $i) ? 'aria-current="page"' : ''; ?>
                                >

                                    <?php echo $i; ?>

                                </a>

                            </li>

                        <?php endfor; ?>


                        <!-- Next button -->
                        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">

                            <a
                                class="page-link"
                                href="<?php echo htmlspecialchars(paginationUrl(min($totalPages, $page + 1), $search, $department), ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo ($page >= $totalPages) ? 'aria-disabled="true" tabindex="-1"' : ''; ?>
                            >
                                Next
                            </a>

                        </li>


                    </ul>

                </nav>

            </div>

        <?php endif; ?>


    </div>

    <!-- End Employee Records -->

</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
