<?php
$jsonString = file_get_contents('users.json');
$data = json_decode($jsonString, true);

$emailToCheck = $_POST["email"];
$passwordToCheck = $_POST["password"];

$emailIndex = array_search($emailToCheck, array_column($data, 'email'));

if ($emailIndex === false) {
    echo json_encode("email_not_found");
} else {
    $user = $data[$emailIndex];
    if ($user['password'] === $passwordToCheck) {
        echo json_encode("success");
    } else {
        echo json_encode("wrong_password");
    }
}
?>