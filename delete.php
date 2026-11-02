<?php

session_start();

require_once 'config/database.php';
require_once 'models/Employee.php';

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Verify CSRF token
if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    header("Location: index.php?error=invalid_request");
    exit;
}

// Validate employee ID
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: index.php?error=invalid_id");
    exit;
}

try {

    $database = new Database();
    $db = $database->getConnection();

    $employee = new Employee($db);

    $deleted = $employee->delete($id);

    if ($deleted) {
        header("Location: index.php?deleted=1");
        exit;
    }

    header("Location: index.php?error=delete_failed");
    exit;

} catch (PDOException $e) {

    error_log($e->getMessage());

    header("Location: index.php?error=delete_failed");
    exit;
}