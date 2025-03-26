<?php

  $jsonString = file_get_contents('recettes.json');
  $data = json_decode($jsonString, true);
  echo json_encode(array_search($_GET["recette"], array_column($data, 'name')));
?>