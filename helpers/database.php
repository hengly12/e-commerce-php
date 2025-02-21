<?php
class Database {
    private $host   = DB_HOST;
    private $user   = DB_USER;
    private $pass   = DB_PASS;
    private $dbname = DB_NAME;
    
    private $pdo;
    private $error;

    public function __construct() {
        $this->connectDB();
    }

    private function connectDB() {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname;
            $options = array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );
            
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e) {
            $this->error = "Connection failed: " . $e->getMessage();
            return false;
        }
    }

    public function select($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            
            if($stmt->rowCount() > 0) {
                return $stmt;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function insert($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $result = $stmt->execute($params);
            
            if($result) {
                return $stmt;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function update($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $result = $stmt->execute($params);
            
            if($result) {
                return $stmt;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function delete($query, $params = []) {
        try {
            $stmt = $this->pdo->prepare($query);
            $result = $stmt->execute($params);
            
            if($result) {
                return $stmt;
            } else {
                return false;
            }
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    public function getError() {
        return $this->error;
    }
}
?>