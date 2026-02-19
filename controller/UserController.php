<?php
require_once '../model/user.php';

class UserController {
    private $user;
    public function __construct() {
        $this->user = new User();
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if (isset($data['user_id'])) {
                    $rows = $this->user->getTask($data['user_id']);
                    if ($rows === null) {
                        return; // Filtering Super Admin ID
                    }
                    $this->jsonResponse(true, $rows);
                    return;
                } else {
                    $this->jsonResponse(false, "You must be logged in to do that action.");
                    return;
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

$controller = new UserController();
$controller->handleRequest();
?>