<?php
session_start();
header('Content-Type: application/json');

// Vérifie que l'utilisateur est un traducteur
if (!in_array('Traducteur', $_SESSION['user']['role'] ?? [])) {
    echo json_encode(["status" => "error", "message" => "Accès non autorisé"]);
    exit;
}

// Récupère les données JSON envoyées
$jsonPayload = file_get_contents('php://input');
$updatedData = json_decode($jsonPayload, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "Données invalides"]);
    exit;
}

// Chemin absolu vers le fichier JSON
$filePath = __DIR__ . '/recettes.json';
$jsonString = file_get_contents($filePath);
$data = json_decode($jsonString, true);

$found = false;
foreach ($data as &$recipe) {
    if (strcasecmp($recipe['name'], $updatedData['name']) === 0) {
        $found = true;
        
        // Met à jour les traductions
        if (!empty($updatedData['nameFR'])) {
            $recipe['nameFR'] = $updatedData['nameFR'];
        }
        
        if (!empty($updatedData['stepsFR'])) {
            $recipe['stepsFR'] = $updatedData['stepsFR'];
        }
        
        // Met à jour les ingrédients
        if (!empty($updatedData['ingredientsFR'])) {
            foreach ($updatedData['ingredientsFR'] as $index => $ingFR) {
                if (isset($recipe['ingredients'][$index]) && !empty($ingFR['nameFR'])) {
                    $recipe['ingredients'][$index]['nameFR'] = $ingFR['nameFR'];
                }
            }
        }
        break;
    }
}

// Sauvegarde avec verrou pour éviter les conflits
$result = file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($result === false) {
    echo json_encode(["status" => "error", "message" => "Échec de l'écriture du fichier"]);
    exit;
}

echo json_encode(["status" => "success", "message" => "Traduction enregistrée"]);
?>