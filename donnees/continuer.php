<?php
// Lire le fichier JSON contenant les utilisateurs
$jsonString = file_get_contents('users.json');
$data = json_decode($jsonString, true);

// Récupérer les données depuis la requête POST
$emailToCheck = $_POST["email"];
$username = $_POST["username"];
$password = $_POST["password"];
$role = $_POST["role"] ?? ["Cuisinier"];
$fav = [],

// Initialiser un indicateur pour l'existence de l'email
$emailExists = false;

// Vérifier si l'email existe dans la liste des utilisateurs
foreach ($data as $user) {
    if (strcasecmp($user['email'], $emailToCheck) == 0) {
        $emailExists = true;
        break;
    }
}

// Si l'email n'existe pas, ajouter le nouvel utilisateur
if (!$emailExists) {
    $newUser = [
        "email" => $emailToCheck,
        "username" => $username,
        "password" => $password,
        "role" => $role,
        "fav" => $fav,
    ];

    $data[] = $newUser;
    
    file_put_contents('users.json', json_encode($data, JSON_PRETTY_PRINT));
}

// Retourner le résultat de la vérification sous forme de JSON
echo json_encode($emailExists);
?>