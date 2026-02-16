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

    public function createTask($task, $description, $assigned_to) {
        if ($this->isSuperAdmin($assigned_to)) {
            return false;
        }

        if ($this->taskExists($task, $assigned_to)) {
            return ([
                'task_exist' => true,
                'assigned_to' => $assigned_to
            ]);
        }

        $stmt = $this->conn->prepare("INSERT INTO tasks (task, description, assigned_to) VALUES (:task, :description, :assigned_to)");
        $stmt->execute([
            'task' => $task,
            'description' => $description,
            'assigned_to' => $assigned_to
        ]);
        return ([
            'task_id' => $this->conn->lastInsertId(),
            'assigned_to' => $assigned_to
        ]);
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

    public function taskExists($task, $assigned_to) {
        $stmt = $this->conn->prepare("SELECT 1 FROM tasks WHERE task = :task AND assigned_to = :assigned_to LIMIT 1");
        $stmt->execute([
            'task' => $task,
            'assigned_to' => $assigned_to
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
?>