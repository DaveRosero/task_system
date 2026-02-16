<?php
require_once 'conn.php';

class Login {
    private $conn;
    public function __construct()
    {
        $this->conn = database();
    }

    public function login($user, $password) {
        $stmt = $this->conn->prepare("SELECT password, role, user, id FROM users WHERE user = :user LIMIT 1");
        $stmt->execute([
            'user' => $user
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($row)) {
            return false;
        } else {
            if (password_verify($password, $row['password'])) {
                return $row;
            } else {
                return false;
            }
        }
    }
}
?>