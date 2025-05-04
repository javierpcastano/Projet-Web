<?php
session_start(); // Démarre la session

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
        // Mise à jour complète de la session comme dans continuer.php
        $_SESSION['user'] = [
            'email' => $user['email'],
            'username' => $user['username'],
            'role' => $user['role'],
            'logged_in' => true
        ];
        echo json_encode(true);
    } else {
        echo json_encode(false);
    }
}
?>