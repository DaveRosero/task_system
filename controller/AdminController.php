<?php
require_once '../model/admin.php';

class AdminController {
    private $admin;
    public function __construct() {
        $this->admin = new Admin();
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                try {
                    if ($data['task_name'] !== null) {
                        $task = $data['task_name'];
                        $description = $data['description'] ?? "";
                        $assigned_to = $data['assigned_to'] ?? ""; // This should be an option field in the form

                        if (empty($assigned_to)) {
                            $this->jsonResponse(false, "All fields are required.");
                            return;
                        }

                        $row = $this->admin->createTask($task, $description, $assigned_to);
                        if (!empty($row)) {
                            if ($row['task_exist'] === true) {
                                $this->jsonResponse(false, 'This task assigned to ' . ucwords($row['assigned_to']) . " already exists.");
                                return;
                            }
                            $this->jsonResponse(true, "New task created with ID: " . $row['task_id'] . "assigned to user: ". ucwords($row['assigned_to']));
                            return;
                        } else {
                            $this->jsonResponse(false, "Something went wrong, please try again."); // return this error if somehow the user managed to select a superadmin for the task
                            return;
                        }
                    }
                } catch (PDOException $e) {
                    if (APP_DEBUG) {
                        $this->jsonResponse(false, $e->getMessage());
                        return;
                    } else { 
                        error_log($e->getMessage());
                        $this->jsonResponse(false, 'Internal Server Error');
                    }
                }
                break;
            case 'PATCH':
                try {
                    if ($data['user_status'] !== null) {
                        $user = $this->admin->updateUserStatus($data['user'], $data['user_status']); // user and user_status should be a type hidden field when admin submits the form
                        if ($user === true) {
                            $this->jsonResponse(true, "Updated status of user: " . ucwords($data['user']));
                            return;
                        } else {
                            $this->jsonResponse(false, "User does not exist.");
                            return;
                        }
                    }
                } catch (PDOException $e) {
                    if (APP_DEBUG) {
                        $this->jsonResponse(false, $e->getMessage());
                        return;
                    } else { 
                        error_log($e->getMessage());
                        $this->jsonResponse(false, 'Internal Server Error');
                    }
                }
                break;
            default:
                $this->methodNotAllowed();
                break;
        }
    }

    public function jsonResponse($bool, $msg = null) {
        echo json_encode([
            'success' => $bool,
            'msg' => $msg
        ]);
        return;
    }

    public function methodNotAllowed() {
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode([
            'message' => 'Method Not Allowed'
        ]);
        exit();
    }
}

$controller = new AdminController();
$controller->handleRequest();
?>