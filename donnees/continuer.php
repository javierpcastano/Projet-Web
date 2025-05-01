<?php
session_start(); // Démarre la session

$jsonString = file_get_contents('users.json');
$data = json_decode($jsonString, true);

$emailToCheck = $_POST["email"];
$username = $_POST["username"];
$password = $_POST["password"];
$role = $_POST["role"] ?? ["Cuisinier"];

$emailExists = false;

foreach ($data as $user) {
    if (strcasecmp($user['email'], $emailToCheck) == 0) {
        $emailExists = true;
        break;
    }
}

if (!$emailExists) {
    $newUser = [
        "email" => $emailToCheck,
        "username" => $username,
        "password" => $password,
        "role" => $role,
        "fav" => [],
    ];

    $data[] = $newUser;
    file_put_contents('users.json', json_encode($data, JSON_PRETTY_PRINT));
    
    // Crée la session pour le nouvel utilisateur
    $_SESSION['user'] = [
        'email' => $emailToCheck,
        'username' => $username,
        'role' => $role,
        'logged_in' => true
    ];
}

echo json_encode($emailExists);
?>
