<?php
function is_password_long($password) {
    return strlen($password) >= 8;
}

function password_match($password, $confirm_password) {
    return $password === $confirm_password;
}

function is_password_alphanumeric ($password) {
    return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$/', $password);
}

function check_password($password, $confirm_password){
    $errors = [];
    if (!is_password_long($password)) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if (!password_match($password, $confirm_password)) {
        $errors[] = 'Password and Confirm Password does not match';
    }
    if (!is_password_alphanumeric($password)) {
        $errors[] = 'Password must contain at least one(1) letter and one(1) number.';
    }
    return $errors;
}

function all_fields_filled($user, $password, $confirm_password) {
    return $user && $password && $confirm_password !== '';
}
?>