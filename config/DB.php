<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'stats';
    private $username = 'root';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'connection error: ' . $e->getMessage();
        }

        $query = "SHOW TABLES LIKE 'events'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            $query = "CREATE TABLE events (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                event_type VARCHAR(255) NOT NULL,
                user_status VARCHAR(255) NOT NULL,
                user_address VARCHAR(255),
                event_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }

        return $this->conn;
    }
}

?>