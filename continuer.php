<?php
$jsonString = file_get_contents('users.json');
$data = json_decode($jsonString, true);

$emailToCheck = $_POST["email"] ?? '';

$emailExists = false;
foreach ($data as $user) {
    if (strcasecmp($user['email'], $emailToCheck) == 0) {
        $emailExists = true;
        break;
    }
}

if (!$emailExists) {
    $newUser = [
        "email" => $_POST["email"],
        "username" => $_POST["username"],
        "password" => $_POST["password"],
        "role" => $_POST["role"],
    ];

    $data[] = $newUser;
    file_put_contents("users.json", json_encode($data, JSON_PRETTY_PRINT));
    echo json_encode(false); // Indique que l'email n'existait pas et que l'utilisateur a été ajouté
} else {
    echo json_encode(true); // Indique que l'email existe déjà
}
?>