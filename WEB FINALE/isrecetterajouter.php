<?php
$jsonString = file_get_contents('user.json');
$data = json_decode($jsonString, true);

#$userEmail = $input['userEmail'];
$recipeName = $input['recipeName'];


$data["fav"][] = [$recipeName];
file_put_contents($filepath, json_encode($users, JSON_PRETTY_PRINT));

?>