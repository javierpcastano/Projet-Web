<?php
// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

$jsonString = file_get_contents('recettes.json');
$data = json_decode($jsonString, true);

$query = $_GET["query"] ?? '';
$foundRecipes = [];

// Vérifiez que la requête n'est pas vide
if (!empty($query)) {
    // Rechercher par nom de recette, ingrédient, timer ou étape
    foreach ($data as $recipe) {
        $found = false;

        // Vérifier le nom de la recette
        if (stripos($recipe['name'], $query) !== false) {
            $found = true;
        }

        // Vérifier les ingrédients
        if (!$found) {
            foreach ($recipe['ingredients'] as $ingredient) {
                if (stripos($ingredient['name'], $query) !== false) {
                    $found = true;
                    break;
                }
            }
        }

        // Vérifier les timers
        if (!$found) {
            foreach ($recipe['timers'] as $timer) {
                if (stripos((string)$timer, $query) !== false) {
                    $found = true;
                    break;
                }
            }
        }

        // Vérifier les étapes
        if (!$found) {
            foreach ($recipe['steps'] as $step) {
                if (stripos($step, $query) !== false) {
                    $found = true;
                    break;
                }
            }
        }

        // Ajouter la recette si elle correspond à la recherche
        if ($found) {
            $foundRecipes[] = $recipe;
        }
    }
}

// Retourner les recettes trouvées
header('Content-Type: application/json');
echo json_encode($foundRecipes);
?>