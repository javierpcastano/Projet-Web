<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <?php
    $jsonString = file_get_contents("recettes.json");
    $data = json_decode($jsonString, true);
    if (!is_array($data)) {
      $data = [];
    }
    $recetteExiste = false;
    $nomRecette = isset($_GET["name"]) ? trim($_GET["name"]) : "";
    $afficherFormComplet = false;
    ?>

    <?php if (!isset($_GET["fullForm"]) && !isset($_GET["completeRecipe"])) { ?>
      <form action="projet.php" method="get">
        <label for="name">Nom de la Recette:</label>
        <input type="text" id="name" name="name" required><br><br>
        <input type="hidden" name="fullForm" value="1">
        <input type="submit" value="Continuer">
      </form>
    <?php } ?>

    <?php
    if (isset($_GET["fullForm"]) && !isset($_GET["completeRecipe"])) {
      if (!empty($nomRecette)) {
        foreach ($data as $recette) {
          if (strcasecmp($recette["name"], $nomRecette) == 0) {
            $recetteExiste = true;
            break;
          }
        }
      }
      
      if ($recetteExiste) {
        echo "<p>La recette <strong>" . htmlspecialchars($nomRecette) . "</strong> existe déjà. Veuillez modifier le titre de la recette.</p>";
    ?>
        <form action="projet.php" method="get">
          <label for="name">Nom de la Recette:</label>
          <input type="text" id="name" name="name" required><br><br>
          <input type="hidden" name="fullForm" value="1">
          <input type="submit" value="Continuer">
        </form>
    <?php
      } else {
        $afficherFormComplet = true;
      }
    }
    ?>

    <?php if ($afficherFormComplet || isset($_GET["completeRecipe"])) { ?>
      <form action="projet.php" method="get">
        <label for="name">Nom de la Recette:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($nomRecette); ?>" readonly><br><br>
        <label for="nameFR">Nom recette (FR):</label>
        <input type="text" id="nameFR" name="nameFR"><br><br>
        <label for="Author">Auteur:</label>
        <input type="text" id="Author" name="Author"><br><br>
        <label>Sans:</label><br>
        <input type="checkbox" id="noGluten" name="Without[]" value="NoGluten">
        <label for="noGluten">Sans Gluten</label><br>
        <input type="checkbox" id="noMilk" name="Without[]" value="NoMilk">
        <label for="noMilk">Sans Lait</label><br>
        <input type="checkbox" id="vegan" name="Without[]" value="Vegan">
        <label for="vegan">Vegan</label><br>
        <input type="checkbox" id="vegetarien" name="Without[]" value="Vegetarien">
        <label for="vegetarien">Végétarien</label><br><br>
        <h3>Ingrédients:</h3>
        <div id="ingredientsContainer"></div>
        <button type="button" onclick="addIngredient()">+ Ajouter un ingrédient</button><br><br>
        <label for="steps">Étapes (séparez chaque étape par un retour à la ligne):</label><br>
        <textarea id="steps" name="steps" rows="5" cols="40"></textarea><br><br>
        <label for="timers">Timers (en minutes, séparez-les par des virgules):</label>
        <input type="text" id="timers" name="timers"><br><br>
        <input type="hidden" name="completeRecipe" value="1">
        <input type="submit" value="Soumettre">
      </form>
    <?php } ?>

    <?php
    if (isset($_GET["completeRecipe"])) {
      $ingredients = [];
      if (isset($_GET["ingredients"]) && is_array($_GET["ingredients"])) {
        foreach ($_GET["ingredients"] as $ingredient) {
          if (!empty($ingredient["name"])) {
            $ingredients[] = [
              "quantity" => $ingredient["quantity"],
              "name" => $ingredient["name"],
              "type" => $ingredient["type"]
            ];
          }
        }
      }
      $steps = isset($_GET["steps"]) ? explode("\n", trim($_GET["steps"])) : [];
      $timers = isset($_GET["timers"]) ? array_map('intval', explode(",", $_GET["timers"])) : [];
      
      $newRecipe = [
        "name" => $nomRecette,
        "nameFR" => $_GET["nameFR"] ?? '',
        "Author" => $_GET["Author"] ?? '',
        "Without" => $_GET["Without"] ?? [],
        "ingredients" => $ingredients,
        "steps" => $steps,
        "timers" => $timers,
      ];
      
      $data[] = $newRecipe;
      file_put_contents("recettes.json", json_encode($data, JSON_PRETTY_PRINT));
      echo "<p>Recette enregistrée avec succès !</p>";
    }
    ?>
  </body>
</html>