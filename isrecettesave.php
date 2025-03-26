<?php
$jsonString = file_get_contents('recettes.json');
$data = json_decode($jsonString, true);

$ingredients = isset($_POST["ingredients"]) ? $_POST["ingredients"] : [];
$steps = isset($_POST["steps"]) ? explode("\n", trim($_POST["steps"])) : [];
$timers = isset($_POST["timers"]) ? array_map('intval', explode(",", $_POST["timers"])) : [];

$newRecipe = [
    "name" => $_POST["name"] ?? '',
    "nameFR" => $_POST["nameFR"] ?? '',
    "Author" => $_POST["Author"] ?? '',
    "Without" => $_POST["Without"] ?? [],
    "ingredients" => $ingredients,
    "steps" => $steps,
    "timers" => $timers,
];

$data[] = $newRecipe;
file_put_contents("recettes.json", json_encode($data, JSON_PRETTY_PRINT));
?>