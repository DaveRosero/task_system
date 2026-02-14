<?php
require_once '../model/register.php';
require_once '../helpers/registerHelper.php';

class RegisterController {
    private $register;
    public function __construct() {
        $this->register = new Register();
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $user = $data['user'] ?? '';
                $password = $data['password'] ?? '';
                $confirm_password = $data['confirm_password'] ?? '';

                if (!all_fields_filled($user, $password, $confirm_password)) {
                    $this->jsonResponse(false, 'All fields are required.');
                    return;
                }

                $errors = check_password($password, $confirm_password);
                if (!empty($errors)) {
                    $this->jsonResponse(false, $errors);
                    return;
                }

                if ($this->register->userExists($user)) {
                    $this->jsonResponse(false, 'User already exists.');
                    return;
                }

                try {
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    if ($this->register->userCount() == 0) {
                        if ($this->register->createSuperAdmin($user, $password)) {
                            $this->jsonResponse(true, 'Super Admin regitered successfully.');
                            return;
                        }
                    } else {
                        if ($this->register->createUser($user, $password)) {
                            $this->jsonResponse(true, 'New user registered successfully.');
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

                // if ($this->register->userExists($user)) {
                //     $this->jsonResponse(false, 'User already exist.');
                //     return;
                // }
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

$controller = new RegisterController();
$controller->handleRequest();
?>