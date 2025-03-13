<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Projet Javier Peña et Aidan Barouk</title>
    <script>
      function addIngredient() {
          let container = document.getElementById("ingredientsContainer");
          let index = container.children.length;
          
          let div = document.createElement("div");
          div.innerHTML = `
              <label>Quantité:</label>
              <input type="text" name="ingredients[${index}][quantity]"><br>
              <label>Nom:</label>
              <input type="text" name="ingredients[${index}][name]"><br>
              <label>Type:</label>
              <input type="text" name="ingredients[${index}][type]"><br><br>
          `;
          
          container.appendChild(div);
      }
    </script>
  </head>

  <body>
  <form action="projet.php" method="get">
        <label for="name">Nom de la Recette:</label>
        <input type="text" id="name" name="name" required><br><br>
        <input type="hidden" name="fullForm" value="1">
        <input type="submit" value="Continuer">
    </form>
    <?php

$jsonString = file_get_contents("recettes.json");
$data = json_decode($jsonString, true);
$recetteExiste = false;
$nomRecette = isset($_GET["name"]) ? trim($_GET["name"]) : "";

// Vérifier uniquement si un nom a été soumis
if (!empty($nomRecette)) {
    foreach ($data as $recette) {
        if (strcasecmp($recette["name"], $nomRecette) == 0) {
            $recetteExiste = true;
            break;
        }
    }
}

?>

<?php if (empty($nomRecette) && !isset($_GET["fullForm"])): ?>
    <!-- Message si la recette existe déjà -->
    <p>Cette recette existe déjà. Veuillez choisir un autre nom.</p>
    <a href="projet.php"><button>Retour</button></a>

<?php else: ?>

      <form action="projet.php" method="get">
          <label for="name">Nom de la Recette:</label>
          <input type="text" id="name" name="name" value="<?php echo $nomRecette; ?>" readonly><br><br>
          
          <label for="nameFR">Nom recette (FR):</label>
          <input type="text" id="nameFR" name="nameFR"><br><br>
  
          <label for="Author">Auteur:</label>
          <input type="text" id="Author" name="Author"><br><br>
  
          <label>Sans:</label><br>
          <input type="checkbox" id="noGluten" name="Without[]" value="NoGluten">
          <label for="noGluten">Sans Gluten</label><br>
          <input type="checkbox" id="noMilk" name="Without[]" value="NoMilk">
          <label for="noMilk">Sans Lait</label><br>
          <input type="checkbox" id="vegan" name="Without[]" value="vegan">
          <label for="vegan">Vegan</label><br>
          <input type="checkbox" id="vegetarien" name="Without[]" value="vegetarien">
          <label for="vegetarien">Vegetarien</label><br><br>
  
          <h3>Ingrédients:</h3>
          <div id="ingredientsContainer"></div>
          <button type="button" onclick="addIngredient()">+ Ajouter un ingrédient</button><br><br>
  
          <label for="steps">Étapes:</label>
          <textarea id="steps" name="steps"></textarea><br><br>
  
          <label for="timers">Timers (en minutes):</label>
          <input type="text" id="timers" name="timers"><br><br>
  
          <input type="submit" value="Soumettre">
      </form>
    <?php endif; ?>
    <?php
      $jsonString = file_get_contents("recettes.json");
      $data = json_decode($jsonString, true);

      $ingredients = [];
      $i = 0;
      while (isset($_GET["ingredients"]["$i"])) {
          $ingredient = $_GET["ingredients"]["$i"];
          if (!empty($ingredient["name"])) {
              $ingredients[] = [
                  "quantity" => $ingredient["quantity"],
                  "name" => $ingredient["name"],
                  "type" => $ingredient["type"]
              ];
          }
          $i++;
      }

      $newRecipe = [
          "name" => $_GET["name"],
          "nameFR" => $_GET["nameFR"],
          "Author" => $_GET["Author"],
          "Without" => $_GET["Without"],
          "ingredients" => $ingredients,
          "steps" => explode("\n", $_GET["steps"]),
          "timers" => array_map('intval', explode(",", $_GET["timers"])),
      ];
      
      $data[] = $newRecipe;
      file_put_contents("recettes.json", json_encode($data, JSON_PRETTY_PRINT));
    ?>

  </body>