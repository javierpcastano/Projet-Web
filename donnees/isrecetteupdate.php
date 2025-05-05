<?php
session_start();
header('Content-Type: application/json');

// Récupère les données JSON envoyées
$jsonPayload = file_get_contents('php://input');
$updatedData = json_decode($jsonPayload, true);

// Chemin absolu vers le fichier JSON
$filePath = __DIR__ . '/recettes.json';
$jsonString = file_get_contents($filePath);
$data = json_decode($jsonString, true);

$found = false;
foreach ($data as &$recipe) {
    // On cherche si le nom correspond soit en anglais (name) soit en français (nameFR)
    if (
        strcasecmp($recipe['name'], $updatedData['name']) === 0 ||
        (isset($recipe['nameFR']) && strcasecmp($recipe['nameFR'], $updatedData['name']) === 0)
    ) {
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

        break; // On arrête la boucle car on a trouvé la recette
    }
}


// Sauvegarde avec verrou pour éviter les conflits
$result = file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode(["status" => "success", "message" => "Traduction enregistrée"]);
?>
