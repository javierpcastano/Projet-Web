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
            ingredients: ingredients
        };

        $.ajax({
            method: "POST",
            url: "isrecettesave.php",
            data: data
        }).done(function(e) {
            $("#recetteelements").html("Recette enregistrée avec succès !");
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
        email: $('#email').val(),
        username: $('#username').val(),
        password: $('#password').val(),
        role: $('input[name="Role"]:checked').map(function() { return this.value; }).get()
    };

    $.ajax({
        method: "POST",
        url: "continuer.php",
        data: data
    }).done(function(response) {
        if (response === "true") {
            $("#nouvcompte").html("L'email <strong>" + $('#email').val() + "</strong> existe déjà. Veuillez modifier l'adresse mail.");
        } else {
            $("#inscrire").css("display", "none");
            $("#signinn").css("display", "none");
            $("#loginn").css("display", "none");
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


function Research() {
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
        ingredients: ingredients
    };

    console.log($('#name').val());
    $.ajax({
        method: "GET",
        url: "issearchexiste.php",
        data: data,
    }).done(function(response) {
        console.log(response);
        const parsedResponse = JSON.parse(response);

        if (parsedResponse.length === 0) {
            $("#feedbackname").html("Aucune recette trouvée correspondant à votre recherche.");
        } else {
            let resultHtml = "<h3>Recettes trouvées :</h3><ul>";
            parsedResponse.forEach(function(recipe) {
                resultHtml += "<li><strong>" + recipe.name + "</strong> - Auteur : " + recipe.Author + "</li>";
            });
            resultHtml += "</ul>";
            $("#feedbackname").html(resultHtml);
        }
    }).fail(function(e) {
        console.log(e);
        $("#message").html("<span class='ko'> Error: network problem </span>");
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

    <!--form action="projet.php" method="get"-->

    <label for="name">Recipe Name:</label>
    <input type="text" id="name" name="name" required><br><br>
    <input type="hidden" name="fullForm" value="1">
    <button onclick="submit()" >Continuer</button>
    <p id="feedbackname">  </p>
    <!--/form-->
    
    <!--form action="projet.php" method="get"-->
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
    <input type="hidden" name="completeRecipe" value="1">
    <button onclick="sauvegarder()" >Sauvegarder</button>
    <p id="recetteelements">  </p>
    <!--/form-->
    </div>



    <div id="loginn" style="display:block">
    <button onclick="login()" >Log in</button>
    <p id="hola1"></p>
    </div>

    <div id="connecter" style="display:none">
    <label for="email">Email:</label>
    <input type="text" id="email" name="email"><br><br>

    <label for="password">Mot de Passe:</label>
    <input type="password" id="password" name="password"><br><br>

    <button onclick="connection()" >Log In</button>
    <p id="Connexion"></p>

    </div>

    <div id="signinn" style="display:block">
    <button onclick="signin()" >Sign in</button>
    <p id="hola"></p>
    </div>

    <div id="inscrire" style="display:none">
    <label for="email">Email:</label>
    <input type="text" id="email" name="email"><br><br>
    
    <label for="username">Username:</label>
    <input type="text" id="username" name="username"><br><br>

    <label for="password">Mot de Passe:</label>
    <input type="password" id="password" name="password"><br><br>

    <label>Role:</label><br>
    <input type="checkbox" id="Cuisinier" name="Role" value="Cuisinier">
    <label for="Cuisinier">Cuisinier</label><br>
    <input type="checkbox" id="DemandeTraducteur" name="Role" value="DemandeTraducteur">
    <label for="DemandeTraducteur">Demande Traducteur</label><br>
    <input type="checkbox" id="DemandeChef" name="Role" value="DemandeChef">
    <label for="DemandeChef">Demande Chef</label><br>

    <button onclick="Continuer()">Sign In</button>

    <p id="nouvcompte"></p>
</div>


<div id = "search" >
<label for="recherche">Recherhce:</label>
<input type="text" id="search" name="search" placeholder="Chercher une recette, un igredient, ..."><br><br>
<button onclick="Research()"> Research </button>
    

  </body>
</html>