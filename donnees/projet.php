<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Projet Javier Peña et Aidan Barouk</title>
    <script>

    function submit() {
        console.log($('#name').val())
        $.ajax({
        method: "GET",
        url: "isrectteexiste.php",
        data: {"recette": $('#name').val()}
      }).done(function(e) {
        console.log(e)
        if(e!="false")
          $("#feedbackname").html("La recette <strong>" +$('#name').val()+ "</strong> existe déjà. Veuillez modifier le titre de la recette.");
        else
            $("#formulaire").css("display", "block")
            $("#Succes").css("display", "none")
    }).fail(function(e) {
        console.log(e);
        $("#message").html("<span class='ko'> Error: network problem </span>");
      });
    }

    function sauvegarder() {
        const ingredients = [];
        $('#ingredientsContainer div').each(function() {
            const quantity = $(this).find('input[name*="[quantity]"]').val();
            const name = $(this).find('input[name*="[name]"]').val();
            const type = $(this).find('input[name*="[type]"]').val();
            if (name) {
                ingredients.push({ quantity, name, type });
            }
        });

        const data = {
            name: $('#name').val(),
            nameFR: $('#nameFR').val(),
            Author: $('#Author').val(),
            Without: $('input[name="Without[]"]:checked').map(function() { return this.value; }).get(),
            steps: $('#steps').val(),
            timers: $('#timers').val(),
            ingredients: ingredients,
            imageURL: $('#imageURL').val(),
            originalURL:$('#originalURL').val()
        };

        $.ajax({
            method: "POST",
            url: "isrecettesave.php",
            data: data
        }).done(function(e) {
            $("#formulaire").css("display", "none")
            $("#namerecette").css("display", "block")
            $("#Succes").css("display", "block")
        }).fail(function(e) {
            console.log(e);
            $("#message").html("<span class='ko'> Error: network problem </span>");
        });
    }

    function login() {
        console.log($('#uname').val())
        $.ajax({
        method: "POST",
        url: "islogin.php",
        data: {"username": $('#uname').val()}
      }).done(function(e) {
        $("#connecter").css("display", "block");
        $("#loginn").css("display", "none");

    }).fail(function(e) {
        console.log(e);
        $("#message").html("<span class='ko'> Error: network problem </span>");
      });
    }

    function signin() {
    
        const data = {
            email: $('#email').val(),
            username: $('#username').val(),
            password: $('#password').val(),
            role: $('#role').val(),
        };


        $.ajax({
        method: "GET",
        url: "issign.php",
        data: {"username": $('#uname').val()}
      }).done(function(e) {
        $("#signinn").css("display", "none");
        $("#inscrire").css("display", "block");
    }).fail(function(e) {
        console.log(e);
        $("#message").html("<span class='ko'> Error: network problem </span>");
      });
    }

    function Continuer() {
    const data = {
        email: $('#email1').val(),
        username: $('#username').val(),
        password: $('#password1').val(),
        role: $('input[name="Role"]:checked').map(function() { return this.value; }).get()
    };

    $.ajax({
        method: "POST",
        url: "continuer.php",
        data: data
    }).done(function(response) {
        const parsedResponse = JSON.parse(response);
        console.log(parsedResponse);
        console.log(email);
        if (parsedResponse === true) {
            $("#nouvcompte").html("L'email <strong>" + data.email + "</strong> existe déjà. Veuillez modifier l'adresse mail.");
        } else {
            $("#inscrire").css("display", "none");
            $("#signinn").css("display", "none");
            $("#loginn").css("display", "none");
            $("#connecter").css("display", "none");
        }
    }).fail(function(e) {
        console.log(e);
        $("#message").html("<span class='ko'> Error: network problem </span>");
    });
}

function connection() {
    const data = {
        email: $('#email').val(),
        password: $('#password').val(),
    };

    $.ajax({
        method: "POST",
        url: "connection.php",
        data: data
    }).done(function(response) {
        try {
            const parsedResponse = JSON.parse(response);
            console.log(parsedResponse); // Ajoutez ceci pour voir la réponse dans la console

            if (parsedResponse === "email_not_found") {
                $("#Connexion").html("L'email <strong>" + $('#email').val() + "</strong> n'a pas de compte associé. Veuillez modifier l'adresse mail.");
            } else if (parsedResponse === "wrong_password") {
                $("#Connexion").html("Le mot de passe est incorrect. Veuillez réessayer.");
            } else if (parsedResponse === "success") {
                $("#Connexion").html("Connexion réussie !");
                $("#inscrire").css("display", "none");
                $("#signinn").css("display", "none");
                $("#loginn").css("display", "none");
                $("#connecter").css("display", "none");
            } else {
                $("#Connexion").html("Erreur inattendue. Veuillez réessayer.");
            }
        } catch (error) {
            console.error("Erreur de parsing JSON:", error);
            $("#Connexion").html("Erreur de traitement de la réponse. Veuillez réessayer.");
        }
    }).fail(function(e) {
        console.log(e);
        $("#message").html("<span class='ko'> Error: network problem </span>");
    });
}

/*
function Research() {
    const searchQuery = $('#search').val().trim(); // Assurez-vous de supprimer les espaces

    $.ajax({
        method: "GET",
        url: "issearchexiste.php",
        data: { query: searchQuery },
    }).done(function(response) {
        console.log(response);
        const parsedResponse = JSON.parse(response);

        if (parsedResponse.length === 0) {
            $("#feedbackname").html("Aucune recette trouvée correspondant à votre recherche.");
        } else {
            let resultHtml = "<h3>Recette trouvée :</h3>";
            parsedResponse.forEach(function(recipe) {
                resultHtml += "<div><strong>Nom :</strong> " + recipe.name + "</div>";
                resultHtml += "<div><strong>Auteur :</strong> " + recipe.Author + "</div>";
                resultHtml += "<div><img src='" + recipe.imageURL + "' alt='Image de " + recipe.name + "' style='max-width: 100%; height: auto;'></div>";
                resultHtml += "<div><strong>Ingrédients :</strong><ul>";
                recipe.ingredients.forEach(function(ingredient) {
                    resultHtml += "<li>" + ingredient.quantity + " " + ingredient.name + " (" + ingredient.type + ")</li>";
                });
                resultHtml += "</ul></div>";
                resultHtml += "<div><strong>Étapes :</strong><ol>";
                recipe.steps.forEach(function(step) {
                    resultHtml += "<li>" + step + "</li>";
                });
                resultHtml += "</ol></div>";
                resultHtml += "<div><strong>Timers :</strong> " + recipe.timers.join(", ") + " minutes</div>";
                resultHtml += "<div><a href='" + recipe.originalURL + "' target='_blank'>Lien original</a></div>";
            });
            $("#feedbackname").html(resultHtml);
        }
    }).fail(function(e) {
        console.log(e);
        $("#message").html("<span class='ko'> Error: network problem </span>");
    });
}
*/
    $role = $_SESSION["role"]; 

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

<!--
  if ($role == "chef")   {
           echo '<div id="namerecette" style="display:block">';
           echo '<label for="name">Recipe Name:</label>';
           echo '<input type="text" id="name" name="name" required><br><br>';
           echo '<input type="hidden" name="fullForm" value="1">';
           echo '<button onclick="submit()" >Continuer</button>';
           echo '<p id="feedbackname">  </p>';
           echo '</div>';
         
        }
    ?>
    -->
    <div id="namerecette" style="display:block">
    <label for="name">Recipe Name:</label>
    <input type="text" id="name" name="name" required><br><br>
    <input type="hidden" name="fullForm" value="1">
    <button onclick="submit()" >Continuer</button>
    <p id="feedbackname">  </p>
    </div>
    
    
    <div id="formulaire" style="display:none">
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

    <label for="imageURL">Lien Image:</label>
    <input type="text" id="imageURL" name="imageURL"><br><br>
    <label for="originalURL">Lien Original:</label>
    <input type="text" id="originalURL" name="originalURL"><br><br>

    <input type="hidden" name="completeRecipe" value="1">
    <button onclick="sauvegarder()" >Sauvegarder</button>
    <p id="recetteelements">  </p>

    </div>

    <div id="Succes" style="display:none">
    <label>Recette enregistrée avec succès !</label><br>
    </div>

    <div id="signinn" style="display:block" >
    <button onclick="signin()" >Sign in</button>
    <p id="hola"></p>
    </div>

    <div id="inscrire" style="display:none">
    <label for="email">Email:</label>
    <input type="text" id="email1" name="email" required="required"><br><br>
    
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required="required"><br><br>

    <label for="password">Mot de Passe:</label>
    <input type="password" id="password1" name="password" required="required"><br><br>

    <label>Role:</label><br>
    <input type="checkbox" id="Cuisinier" name="Role" value="Cuisinier">
    <label for="Cuisinier">Cuisinier</label><br>
    <input type="checkbox" id="DemandeTraducteur" name="Role" value="DemandeTraducteur">
    <label for="DemandeTraducteur">Demande Traducteur</label><br>
    <input type="checkbox" id="DemandeChef" name="Role" value="DemandeChef">
    <label for="DemandeChef">Demande Chef</label><br>

    <button onclick="Continuer()">Continuer</button>

    <p id="nouvcompte"></p>
</div>

    <div id="loginn" style="display:block">
    <button onclick="login()" >Log in</button>
    <p id="hola1"></p>
    </div>

    <div id="connecter" style="display:none">
    <label for="email">Email:</label>
    <input type="text" id="email" name="email" required="required"> <br><br>

    <label for="password">Mot de Passe:</label>
    <input type="password" id="password" name="password" required="required"><br><br>

    <button onclick="connection()" >Log In</button>
    <p id="Connexion"></p>

    </div>

    
</div>


    <h1>Rechercher une recette</h1>
    <input type="text" id="searchInput" class="search-bar" placeholder="Rechercher une recette...">
    <div id="recipeList"></div>
    <div id="recipeDetails" class="recipe-details"></div>

    <script>
        let recipes = [];

        // Charger les recettes avec AJAX
        function fetchRecipes() {
            $.ajax({
                url: "recettes.json",
                method: "GET",
                dataType: "json"
            }).done(function(data) {
                recipes = data;
            }).fail(function() {
                console.error("Erreur de chargement des recettes");
            });
        }

        function displayRecipes(filteredRecipes) {
            const recipeList = $("#recipeList");
            recipeList.empty();

            if (filteredRecipes.length === 0) return;

            filteredRecipes.forEach(recipe => {
                recipeList.append(`<div class="recipe" data-name="${recipe.name}">${recipe.nameFR || recipe.name}</div>`);
            });

            // Ajouter l'événement de clic sur chaque recette
            $(".recipe").on("click", function() {
                const selectedRecipe = recipes.find(r => r.name === $(this).data("name"));
                displayRecipeDetails(selectedRecipe);
            });
        }

        function displayRecipeDetails(recipe) {
            if (!recipe) return;

            let detailsHTML = `
                <h2>${recipe.nameFR || recipe.name}</h2>
                <img src="${recipe.imageURL}" alt="${recipe.nameFR || recipe.name}">
                <h3>Ingrédients :</h3>
                <ul>
                    ${recipe.ingredients.map(ing => `<li>${ing.quantity} ${ing.name}</li>`).join("")}
                </ul>
                <h3>Étapes :</h3>
                <ol>
                    ${recipe.steps.map(step => `<li>${step}</li>`).join("")}
                </ol>
                <h3>Temps estimé :</h3>
                <p>${recipe.timers.reduce((a, b) => a + b, 0)} minutes</p>
            `;

            $("#recipeDetails").html(detailsHTML).fadeIn();
        }

        $("#searchInput").on("input", function() {
            const searchText = $(this).val().trim().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");

            if (searchText === "") {
                displayRecipes([]);
                $("#recipeDetails").hide();
                return;
            }

            const filteredRecipes = recipes.filter(recipe =>
                (recipe.nameFR && recipe.nameFR.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").includes(searchText)) ||
                (recipe.name && recipe.name.toLowerCase().includes(searchText))
            );

            displayRecipes(filteredRecipes);
            $("#recipeDetails").hide(); // Masquer les détails lorsqu'on fait une nouvelle recherche
        });

        fetchRecipes();
    </script>

  </body>
</html>