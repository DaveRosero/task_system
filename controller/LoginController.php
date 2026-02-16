<?php
require_once '../model/login.php';
require_once '../helpers/registerHelper.php';

class LoginController {
    private $login;
    public function __construct() {
        $this->login = new Login();
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                if ($this->login->login($data['user'], $data['password'])) {
                    
                } else {

                }
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

$controller = new LoginController();
$controller->handleRequest();
?>