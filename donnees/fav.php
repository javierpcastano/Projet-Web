<?php

$jsonString = file_get_contents('recettes.json');
$data = json_decode($jsonString, true);

$nomRecette = $_GET['fav']; 

foreach ($data as &$recette) {
    if ($recette["nameFR"] === $nomRecette || $recette["name"] === $nomRecette) {
        $recette["like"] += 1;
        break;
    }
}


file_put_contents("recettes.json", json_encode($data, JSON_PRETTY_PRINT));

?>