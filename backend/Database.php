<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'expression';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }

        return $this->conn;
    }

    public function logConversion($user_id, $source, $target, $original, $converted) {
        $stmt = $this->conn->prepare("
            INSERT INTO conversion_history 
            (user_id, source_type, target_type, original_expression, converted_expression)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$user_id, $source, $target, $original, $converted]);
    }

    public function logEvaluation($user_id, $type, $expression, $variables, $result) {
        $stmt = $this->conn->prepare("
            INSERT INTO evaluation_history 
            (user_id, expression_type, expression, variables, result)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $user_id,
            $type,
            $expression,
            json_encode($variables),
            $result
        ]);
    }

    public function getConversionHistory($user_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM conversion_history 
            WHERE user_id = ?
            ORDER BY conversion_timestamp DESC
            LIMIT 10
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEvaluationHistory($user_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM evaluation_history 
            WHERE user_id = ?
            ORDER BY evaluation_timestamp DESC
            LIMIT 10
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
