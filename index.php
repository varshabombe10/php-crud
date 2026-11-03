
<?php

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'config/database.php';
require_once 'models/Employee.php';

// Database connection
$database = new Database();
$db = $database->getConnection();

$employee = new Employee($db);

// Search and department filters
$search = trim($_GET['search'] ?? '');
$department = trim($_GET['department'] ?? '');

// Get available departments
$departments = $employee->getDepartments();

// Accept only an existing department
if ($department !== '' && !in_array($department, $departments, true)) {
    $department = '';
}

// Pagination settings
$limit = 10;
$page = max(1, (int)($_GET['page'] ?? 1));

// Count matching employees
$totalEmployees = $employee->countFilteredEmployees(
    $search,
    $department
);

// Calculate total pages
$totalPages = max(1, (int)ceil($totalEmployees / $limit));

// Prevent invalid page numbers
$page = min($page, $totalPages);

// Calculate starting record
$offset = ($page - 1) * $limit;

// Fetch employees for current page
$employees = $employee->getFilteredEmployees(
    $search,
    $department,
    $limit,
    $offset
);

// Build pagination URL while preserving filters
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid p-4">

    <h2 class="mb-4">All Employees</h2>

    <!-- Search and Department Filter -->
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">
            <h5 class="mb-0">Employee List</h5>
        </div>

        <div class="card-body">

            <form method="GET" action="index.php">

                <div class="row g-3 align-items-end">

                    <!-- Search -->
                    <div class="col-md-5">

                        <label class="form-label">
                            Search Employee
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by name or email"
                            value="<?php echo htmlspecialchars($search); ?>"
                        >

                    </div>

                    <!-- Department Filter -->
                    <div class="col-md-4">

                        <label class="form-label">
                            Department
                        </label>

                        <select name="department" class="form-select">

                            <option value="">
                                All Departments
                            </option>

                            <?php foreach ($departments as $dept): ?>

                                <option
                                    value="<?php echo htmlspecialchars($dept); ?>"
                                    <?php echo ($department === $dept) ? 'selected' : ''; ?>
                                >
                                    <?php echo htmlspecialchars($dept); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- Apply Button -->
                    <div class="col-md-3">

                        <button type="submit" class="btn btn-primary">
                            Apply Filters
                        </button>

                        <a href="index.php" class="btn btn-secondary">
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- Success Messages -->

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            Employee added successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">
            Employee updated successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">
            Employee deleted successfully!
        </div>
    <?php endif; ?>


    <!-- Employee Table -->

    <div class="card shadow-sm">

        <div class="card-header bg-white d-flex justify-content-between align-items-center">

            <h5 class="mb-0">Employee Records</h5>

            <div>
                <span class="badge bg-primary">
                    Total: <?php echo $totalEmployees; ?>
                </span>

                <a href="create.php" class="btn btn-success btn-sm ms-2">
                    Add Employee
                </a>
            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

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

                        <?php if (!empty($employees)): ?>

                            <?php foreach ($employees as $row): ?>

                                <tr>

                                    <td><?php echo (int)$row['id']; ?></td>

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
                                        ₹<?php echo htmlspecialchars($row['salary']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['created_at']); ?>
                                    </td>

                                    <td>

                                        <a
                                            href="edit.php?id=<?php echo (int)$row['id']; ?>"
                                            class="btn btn-warning btn-sm">
                                            Edit
                                        </a>

                                        <form
                                            action="delete.php"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this employee?');">

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?php echo (int)$row['id']; ?>">

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                                            <button
                                                type="submit"
                                                class="btn btn-danger btn-sm">
                                                Delete
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    No employees found.
                                    Try changing your search or department filter.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>


            <!-- Pagination -->

            <?php if ($totalEmployees > 0): ?>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">

                    <p class="mb-0 text-muted">

                        Showing
                        <?php echo $offset + 1; ?>

                        to

                        <?php echo $offset + count($employees); ?>

                        of

                        <?php echo $totalEmployees; ?>

                        employees

                    </p>

                    <nav aria-label="Employee pagination">

                        <ul class="pagination mb-0">

                            <!-- Previous -->

                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">

                                <a class="page-link"
                                   href="<?php echo htmlspecialchars(paginationUrl(max(1, $page - 1), $search, $department)); ?>">
                                    Previous
                                </a>

                            </li>

                            <!-- Page Numbers -->

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                                <li class="page-item <?php echo ($page === $i) ? 'active' : ''; ?>">

                                    <a class="page-link"
                                       href="<?php echo htmlspecialchars(paginationUrl($i, $search, $department)); ?>">

                                        <?php echo $i; ?>

                                    </a>

                                </li>

                            <?php endfor; ?>

                            <!-- Next -->

                            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">

                                <a class="page-link"
                                   href="<?php echo htmlspecialchars(paginationUrl(min($totalPages, $page + 1), $search, $department)); ?>">
                                    Next
                                </a>

                            </li>

                        </ul>

                    </nav>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>
