
<?php

class Database {

    private $host = "127.0.0.1";
    private $port = "3307";
    private $dbname = "php_crud";
    private $username = "root";
    private $password = "";

    public $conn;

    public function getConnection() {

        $this->conn = null;

        try {

            $this->conn = new PDO(
                "mysql:host=" . $this->host .
                ";port=" . $this->port .
                ";dbname=" . $this->dbname .
                ";charset=utf8mb4",
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $this->conn->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

        } catch(PDOException $exception) {

            error_log($exception->getMessage());

            return null;
        }

        return $this->conn;
    }
}

?>
