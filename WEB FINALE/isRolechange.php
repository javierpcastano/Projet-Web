<?php
session_start();
header('Content-Type: application/json');

$email = $_POST['email'];
$username = $_POST['username'];
$newRole = json_decode($_POST['newRole'], true); // Décodage du tableau JSON

$users = json_decode(file_get_contents('users.json'), true);
$updated = false;

foreach ($users as &$user) {
    if ($user['email'] === $email) {
        $user['role'] = $newRole;
        $updated = true;
        
        // Mise à jour de la session si l'utilisateur modifié est celui connecté
        if (isset($_SESSION['user']['email']) && $_SESSION['user']['email'] === $email) {
            $_SESSION['user']['role'] = $newRole;
        }
        break;
    }
}

if ($updated) {
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Utilisateur non trouvé"]);
}
?>