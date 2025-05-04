<?php
$jsonString = file_get_contents('recettes.json');
$data = json_decode($jsonString, true);

$commentaire = $_POST["Commentaire"];
$nomRecette =  $_POST['nom'];

foreach ($data as &$recette) { 
    if ($recette["nameFR"] === $nomRecette || $recette["name"] === $nomRecette) {

        $recette["Commentaire"][] = $commentaire;

        break;
    }
}

file_put_contents("recettes.json", json_encode($data, JSON_PRETTY_PRINT));
?>