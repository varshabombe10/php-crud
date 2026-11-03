
<?php

class Employee {

    private $conn;
    private $table = "employees";

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. CREATE - Add a new employee
    public function create($name, $email, $phone, $department, $salary) {

        $sql = "INSERT INTO " . $this->table .
               " (name, email, phone, department, salary)
                 VALUES (:name, :email, :phone, :department, :salary)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':department' => $department,
            ':salary' => $salary
        ]);
    }

    // 2. READ - Get all employees
    public function readAll() {

        $sql = "SELECT * FROM " . $this->table .
               " ORDER BY id DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    // 3. READ - Get one employee by ID
    public function getById($id) {

        $sql = "SELECT * FROM " . $this->table .
               " WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }

    // 4. UPDATE - Update employee details
    public function update($id, $name, $email, $phone, $department, $salary) {

        $sql = "UPDATE " . $this->table . "
                SET name = :name,
                    email = :email,
                    phone = :phone,
                    department = :department,
                    salary = :salary
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':department' => $department,
            ':salary' => $salary
        ]);
    }

    // 5. DELETE - Delete employee
    public function delete($id) {

        $sql = "DELETE FROM " . $this->table .
               " WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }

    public function getDepartments(){
        $sql = "SELECT DISTINCT department
                FROM " . $this->table . "
                WHERE department IS NOT NULL
                AND department != ''
                ORDER BY department ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getFilteredEmployees($search, $department, $limit, $offset){
        $sql = "SELECT * FROM " . $this->table . " WHERE 1=1";

        if (!empty($search)) {
            $sql .= " AND (name LIKE :search_name OR email LIKE :search_email)";
        }

        if (!empty($department)) {
            $sql .= " AND department = :department";
        }

        $sql .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);

        if (!empty($search)) {
            $searchTerm = "%" . $search . "%";

            $stmt->bindValue(':search_name', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':search_email', $searchTerm, PDO::PARAM_STR);
        }

        if (!empty($department)) {
            $stmt->bindValue(':department', $department, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countFilteredEmployees($search, $department)
    {
        $sql = "SELECT COUNT(*) FROM " . $this->table . " WHERE 1=1";

        if (!empty($search)) {
            $sql .= " AND (name LIKE :search_name OR email LIKE :search_email)";
        }

        if (!empty($department)) {
            $sql .= " AND department = :department";
        }

        $stmt = $this->conn->prepare($sql);

        if (!empty($search)) {
            $searchTerm = "%" . $search . "%";

            $stmt->bindValue(':search_name', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':search_email', $searchTerm, PDO::PARAM_STR);
        }

        if (!empty($department)) {
            $stmt->bindValue(':department', $department, PDO::PARAM_STR);
        }

        $stmt->execute();

        return (int)$stmt->fetchColumn();
    }

}

?>
