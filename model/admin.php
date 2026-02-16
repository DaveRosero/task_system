<?php
require_once 'conn.php';

class Admin {
    private $conn;
    public function __construct() {
        $this->conn = database();
    }

    public function updateUserStatus ($user, $status) {
        if (!$this->userExists($user) || $this->isSuperAdmin($user)) {
            return false;
        }
        $role = "user";
        $stmt = $this->conn->prepare("UPDATE users SET status = :status WHERE user = :user AND role = :role");
        $stmt->execute([
            'user' => $user,
            'status' => $status,
            'role' => $role
        ]);
        return true;
    }

    public function userExists($user) {
        $stmt = $this->conn->prepare("SELECT 1 FROM users WHERE user = :user LIMIT 1");
        $stmt->execute(['user' => $user]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function isSuperAdmin($user) {
        $role = "superadmin";
        $stmt = $this->conn->prepare("SELECT 1 FROM users WHERE user = :user AND role = :role LIMIT 1");
        $stmt->execute([
            'user' => $user,
            'role' => $role
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
?>