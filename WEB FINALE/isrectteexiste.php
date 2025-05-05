<?php
$jsonString = file_get_contents('recettes.json');
$data = json_decode($jsonString, true);
$searchName = $_GET["recette"];

$found = false;
foreach ($data as $recipe) {
    if (strcasecmp($recipe['name'], $searchName) === 0 || 
        (isset($recipe['nameFR']) && strcasecmp($recipe['nameFR'], $searchName) === 0)) {
        $found = true;
        break;
    }
}

echo json_encode($found);
?>