
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

}

?>
