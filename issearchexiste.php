<?php
// Lire le fichier JSON contenant les recettes
$jsonString = file_get_contents('recettes.json');
$data = json_decode($jsonString, true);

// Récupérer les données envoyées par la requête GET
$nameToCheck = $_GET["name"] ?? '';
$ingredientsToCheck = $_GET["ingredients"] ?? [];

// Initialiser les indicateurs de recherche
$recipeExists = false;
$ingredientExists = false;

// Vérifier si la recette existe par son nom
foreach ($data as $recipe) {
    if (strcasecmp($recipe['name'], $nameToCheck) == 0) {
        $recipeExists = true;
        break;
    }
}

// Si la recette n'existe pas, vérifier les ingrédients
if (!$recipeExists) {
    foreach ($data as $recipe) {
        foreach ($recipe['ingredients'] as $ingredient) {
            foreach ($ingredientsToCheck as $checkIngredient) {
                if (strcasecmp($ingredient['name'], $checkIngredient['name']) == 0) {
                    $ingredientExists = true;
                    break 3; // Sortir des trois boucles
                }
            }
        }
    }
}

// Retourner le résultat de la recherche
if ($recipeExists) {
    echo json_encode("recipe_found");
} elseif ($ingredientExists) {
    echo json_encode("ingredient_found");
} else {
    echo json_encode("not_found");
}
?>