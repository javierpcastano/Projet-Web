<?php
session_start();
header('Content-Type: application/json');


$username = $_POST['username'];
$newRole = $_POST['newRole'];


$users = json_decode(file_get_contents('users.json'), true);
$updated = false;

foreach ($users as &$user) {
    if ($user['username'] === $username) {
        $user['role'] = $newRole;
        $updated = true;
        break;
    }
}

if ($updated) {
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
    echo json_encode(["status" => "success", "message" => "Rôle mis à jour"]);
} else {
    echo json_encode(["status" => "error", "message" => "Utilisateur non trouvé"]);
}
?>