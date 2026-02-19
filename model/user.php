<?php
require_once 'conn.php';

class User {
    private $conn;
    public function __construct() {
        $this->conn = database();
    }

    public function getTask ($user_id) {
        if ($user_id === 1) {
            return; // Filtering Super Admin ID
        }
        $assigned_to = $this->getUserName($user_id);
        $status = 1;
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE assigned_to = :assigned_to AND status = :status");
        $stmt->execute(['assigned_to' => $assigned_to, 'status' => $status]);
        return $stmt->fetchAll();
    }

    public function getUserName ($user_id) {
        $stmt = $this->conn->prepare("SELECT user FROM users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        return $stmt->fetchColumn();
    }
}
?>