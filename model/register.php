<?php
require_once 'conn.php';

Class Register {
    private $conn;
    public function __construct()
    {
        $this->conn = database();
    }

    public function errorMsg($bool, $msg = null) {
        echo json_encode([
            'success' => $bool,
            'msg' => $msg
        ]);
        return;
    }

    public function userCount() {
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM users');
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function userExists($user) {
        $stmt = $this->conn->prepare("SELECT 1 FROM users WHERE user = :user LIMIT 1");
        $stmt->execute(['user' => $user]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function createSuperAdmin ($user, $password) {
        $stmt = $this->conn->prepare("INSERT INTO users (user, password, role) VALUES (:user, :password, :role)");
        $stmt->execute([
            'user' => $user,
            'password' => $password,
            'role' => 'superadmin'
        ]);
        return true;
    }

    public function createUser($user, $password) {
        $stmt = $this->conn->prepare("INSERT INTO users (user, password, role) VALUES (:user, :password, :role)");
        $stmt->execute([
            'user' => $user,
            'password' => $password,
            'role' => 'user'
        ]);
        return true;
    }
}
?>