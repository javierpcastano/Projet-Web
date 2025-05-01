<!DOCTYPE html>
<?php 
require_once 'check_session.php';
?>

<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Projet Javier Peña et Aidan Barouk</title>
    <script>


    function Commentaire(button){

        commentaire = $(button).closest('.zone-commentaire').find('.champ-commentaire').val();

        const data = {
            Commentaire: commentaire,
            nom: $(button).data("name")
        };

        console.log("Commentaire :", data);
     
        $.ajax({
            method: "POST",
            url: "isCOMsave.php",
            data: data
        }).done(function(e) {

            const listeCommentaires = $(button).closest('.recette').find('.commentaires-liste'); 
            listeCommentaires.append(`<div>${commentaire}</div>`);

            $(button).closest('.zone-commentaire').hide()
            $("#zone-commentaire").css("display", "block")

        }).fail(function(e) {
            console.log(e);
            $("#message").html("<span class='ko'> Error: network problem </span>");
        });
    }


    function Fav(button){
        const fav = $(button).data("name");
        const isLiked = $(button).hasClass("liked");

        if (!isLiked) {
            $.ajax({
                method: "GET",
                url: "fav.php",
                data: {"fav": fav}
            }).done(function(e) {
                $(button).addClass("liked");
                $(button).find(".heart-icon").removeClass("fa-regular").addClass("fa-solid").css("color", "red");
                let like = parseInt($(button).find(".likeCount").text());
                $(button).find(".likeCount").text(like + 1);
            });
        } else {
            $.ajax({
                method: "GET",
                url: "unfav.php",
                data: {"fav": fav}
            }).done(function(e) {
                $(button).removeClass("liked");
                $(button).find(".heart-icon").removeClass("fa-solid").addClass("fa-regular").css("color", "");
                
                let like = parseInt($(button).find(".likeCount").text());
                $(button).find(".likeCount").text(like - 1);
            });
        }
    }


    function submit() {
        console.log($('#name').val())
        $.ajax({
        method: "GET",
        url: "isrectteexiste.php",
        data: {"recette": $('#name').val()}
      }).done(function(e) {
        console.log(e)
        if(e!="false"){
          $("#feedbackname").html("La recette <strong>" +$('#name').val()+ "</strong> existe déjà. Veuillez modifier le titre de la recette.");
        }else{
            $("#formulaire").css("display", "block")
            $("#Succes").css("display", "none")
        }
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

    function logout() {
        $.ajax({
            method: "POST",
            url: "logout.php"
        }).done(function() {
            location.reload();
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
            location.reload(); // Recharge la page pour afficher le dashboard
        }
    }).fail(function(e) {
        console.log(e);
        $("#message").html("<span class='ko'> Error: network problem </span>");
    });
}

    function connection() {
        console.log("Bouton Login cliqué"); // Debug 1

        const data = {
            email: $('#email').val(),
            password: $('#password').val(),
        };

        $.ajax({
            method: "POST",
            url: "connection.php",
            data: data
        }).done(function(response) {
            const parsedResponse = JSON.parse(response);
            console.log(parsedResponse);
            if (parsedResponse === true) {
                location.reload(); // Recharge la page pour afficher le dashboard

            } else {
                $("#Connexion").html("<span class='error'>Identifiants incorrects</span>");
            }
        }).fail(function(error) {
            console.error("Erreur AJAX:", error); // Debug 3
        });
    }


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

  <div id="dashboard" style="display:<?php echo isLoggedIn() ? 'block' : 'none'; ?>">
    <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['user']['username'] ?? 'Invité'); ?></h2>
    <p>Email: <?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?></p>
    <p>Role: <?php echo implode(', ', $_SESSION['user']['role'] ?? []); ?></p>
    
    <!-- Section réservée aux chefs -->
    <?php if(in_array('DemandeChef', $_SESSION['user']['role'] ?? [])): ?>
        <div class="chef-section">
            <h3>Outils Chef</h3>
            <!-- Contenu spécifique -->
        </div>
    <?php endif; ?>
    
    <button onclick="logout()">Déconnexion</button>
</div>


<div id="namerecette" style="display:<?php echo (isLoggedIn() && in_array('DemandeChef', $_SESSION['user']['role'] ?? [])) ? 'block' : 'none'; ?>">
    <h1>Ajouter une recette</h1>
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

    <div id="signinn" style="display:<?php echo !isLoggedIn() ? 'block' : 'none'; ?>" >
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

    <div id="loginn" style="display:<?php echo !isLoggedIn() ? 'block' : 'none'; ?>">
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
                
                <button class="fav" 
                    data-name="${recipe.nameFR || recipe.name}" 
                    onclick="Fav(this)">
                    <i class="fa-regular fa-heart heart-icon"></i> <span class="likeCount">${recipe.like}</span>
                </button>
                <p id="fav"></p>

                
                <h3>Feedback </h3>

                <div class="recette">
    <h2>${recipe.nameFR || recipe.name}</h2>

    <div class="commentaires-liste">
        ${recipe.Commentaire.map(commentaire => `<div>${commentaire}</div><br>`).join("")}
    </div>

    <div class="zone-commentaire">
        <label>Ajouter un Commentaire</label><br>
       <textarea class="champ-commentaire" name="commentaire" rows="5" cols="40"></textarea><br>
        <button class="ajout-commentaire" data-name="${recipe.nameFR || recipe.name}" onclick="Commentaire(this)">Ajouter</button>
    </div>
</div>






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

            recipes = Object.values(recipes);

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

    <style>
        .fav {
            background-color: transparent; 
            border: none;
            cursor: pointer;               
            font-size: 18px; 
            padding: 0;
        }

        .fav:hover .heart-icon, .fav:hover .heart {
             transform: scale(1.2);
             transition: transform 0.2s;
            }
    </style>





</html>
