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