# Rapport Technique : Projet de Site de Recettes Culinaires

## 1. Introduction

Imaginez une plateforme en ligne où vous pouvez non seulement trouver des recettes pour vos plats préférés, mais aussi les partager, les aimer, et discuter avec d’autres passionnés de cuisine. C’est l’idée derrière notre projet : une application web interactive de recettes culinaires, développée avec des technologies comme **PHP**, **JavaScript (AJAX)**, **HTML**, et **CSS**. Tout repose sur un fichier central, `projet.php`, qui fait office de cœur de l’application. Voici ce qu’il permet de faire :

- **S’identifier ou s’inscrire** : un système d’authentification pour sécuriser l’accès.
- **Gérer des recettes** : ajouter, modifier ou chercher des idées culinaires.
- **Interagir** : liker une recette ou laisser un commentaire.
- **Administrer** : une interface spéciale pour gérer les utilisateurs et leurs rôles.

Ce rapport va plonger dans les détails techniques, expliquer comment les fonctions AJAX rendent tout cela possible, et partager les galères qu’on a rencontrées – avec les solutions qu’on a trouvées pour s’en sortir !

## 2. Architecture Technique

### 2.1. Structure des Fichiers

On a organisé le projet pour que tout soit clair et facile à maintenir. Voici les principaux éléments :

- **`projet.php`** : la page principale où tout se passe, avec du HTML pour la structure, du CSS pour le style, et du JavaScript pour rendre l’expérience fluide.
- **`users.json`** : notre "base de données" pour les utilisateurs – emails, mots de passe, rôles, etc.
- **`recettes.json`** : là où on stocke toutes les recettes, avec leurs noms, ingrédients, et étapes.
- **Fichiers PHP pour AJAX** :
  - `connection.php` : pour se connecter.
  - `continuer.php` : pour s’inscrire.
  - `fav.php` et `unfav.php` : pour gérer les likes.
  - `isCOMsave.php` : pour enregistrer les commentaires.
  - `isrecettesave.php` : pour ajouter une recette.
  - `isrecetteupdate.php` : pour mettre à jour les traductions.
  - `logout.php` : pour se déconnecter.
  - `ListUtilisateur.php` : pour les admins, afin de voir les utilisateurs en attente de validation.

Chaque fichier a son rôle, comme une équipe de cuisine où chacun sait ce qu’il doit faire.

## 3. Les Fonctions AJAX : Comment Ça Marche ?

AJAX, c’est la magie qui rend l’application vivante : pas besoin de recharger la page pour voir des mises à jour. Voici comment on l’a utilisé, avec les défis qu’on a dû relever.

### 3.1. Authentification

Pour que les utilisateurs puissent se connecter ou s’inscrire, on a mis en place un système solide.

#### Fichiers Utilisés :
- `connection.php` : vérifie les identifiants.
- `continuer.php` : ajoute un nouvel utilisateur.
- `check_session.php` : confirme si quelqu’un est connecté.
- `logout.php` : met fin à la session.

#### Comment Ça Fonctionne :
Quand vous entrez votre email et mot de passe, un appel AJAX part vers `connection.php`. Voici le code côté JavaScript :

```javascript
function connection() {
    $.ajax({
        url: "connection.php",
        type: "POST",
        data: {
            email: $("#email").val(),
            password: $("#password").val()
        }
    }).done(function(response) {
        if (response === "true") location.reload();
        else $("#Connexion").html("Échec de connexion");
    });
}
```

Si tout est bon, la page se recharge, et vous êtes connecté !

#### Difficultés :
- **Sessions qui disparaissaient** : au début, on se faisait déconnecter sans raison. On a compris que `session_start()` manquait dans certains fichiers. Maintenant, on l’ajoute partout où c’est nécessaire.
- **Rôles qui ne suivaient pas** : parfois, le rôle (comme "Cuisinier" ou "Admin") ne se mettait pas à jour. On a corrigé ça en mettant bien à jour `$_SESSION['user']['role']` à chaque changement.

### 3.2. Gestion des Recettes

Ajouter ou modifier une recette, c’est le cœur du projet.

#### Fichiers Utilisés :
- `isrecettesave.php` : enregistre une nouvelle recette.
- `isrecetteupdate.php` : met à jour les traductions.
- `isrecetteexiste.php` : vérifie si une recette existe déjà.

#### Ajouter une Recette :
1. On vérifie d’abord si le nom est unique avec `isrecetteexiste.php`.
2. Ensuite, les données partent vers `isrecettesave.php`. Voici un extrait :

```php
$newRecipe = [
    "name" => $_POST["name"],
    "ingredients" => json_decode($_POST["ingredients"], true),
    "steps" => explode("\n", $_POST["steps"])
];
file_put_contents("recettes.json", json_encode($data, JSON_PRETTY_PRINT));
```

#### Problèmes Rencontrés :
- **Ingrédients mal formatés** : les ingrédients arrivent en JSON, mais ça plantait si le format n’était pas parfait. On a ajouté `JSON.stringify()` côté JavaScript pour être sûrs.
- **Traductions désynchronisées** : mettre à jour une traduction demandait de trouver la bonne recette dans `recettes.json`. On a peaufiné la boucle dans `isrecetteupdate.php` pour que ça marche nickel.

### 3.3. Likes et Commentaires

Pour rendre le site interactif, on a ajouté des likes et des commentaires.

#### Fichiers Utilisés :
- `fav.php` et `unfav.php` : pour liker ou unlike une recette.
- `isCOMsave.php` : pour sauvegarder un commentaire.

#### Les Likes en Action :
Quand vous cliquez sur "like", voilà ce qui se passe :

```javascript
function Fav(button) {
    $.get("fav.php", { fav: $(button).data("name") })
     .done(() => {
         $(button).toggleClass("liked");
         // Mise à jour visuelle immédiate
     });
}
```

#### Galère Résolue :
- **Double-clics intempestifs** : si vous cliquiez trop vite, ça envoyait plusieurs requêtes et les likes devenaient fous. On a désactivé le bouton temporairement après chaque clic pour calmer le jeu.

### 3.4. Interface Admin

Les admins ont des pouvoirs spéciaux pour gérer les utilisateurs.

#### Fichiers Utilisés :
- `ListUtilisateur.php` : montre les demandes de rôles.
- `isRolechange.php` : change les rôles.

#### Exemple de Code :
Quand un admin valide un rôle :

```php
foreach ($users as &$user) {
    if ($user['email'] === $_POST['email']) {
        $user['role'] = json_decode($_POST['newRole'], true);
        if ($_SESSION['user']['email'] === $_POST['email']) {
            $_SESSION['user']['role'] = $user['role'];
        }
        break;
    }
}
```

#### Difficulté :
- **Rôles multiples** : certains utilisateurs voulaient être "Chef" et "Traducteur". On a transformé les rôles en tableau JSON dans `users.json` pour gérer ça facilement.

### 3.5. Barre de Recherche

Pour trouver une recette vite fait, on a une barre de recherche qui fonctionne en temps réel.

#### Fichier Utilisé :
- `issearcheexiste.php` : renvoie les résultats.

#### Comment Ça Marche :
Chaque fois que vous tapez, une requête part :

```javascript
$("#searchInput").on("input", function() {
    const searchText = $(this).val().normalize("NFD");
    $.get("issearcheexiste.php", { query: searchText })
     .done(displayResults);
});
```

On s’est inspirés de code trouvé sur Internet, notamment pour gérer les accents avec `normalize("NFD")`. Ça nous a bien dépannés !

## 4. Les Grosses Difficultés : Les Sessions et les Rôles

S’il y a une chose qui nous a donné des sueurs froides, c’est la **gestion des sessions**. Au début, on ne comprenait pas bien comment ça marchait. Voici ce qu’on a traversé :

- **Sessions perdues** : parfois, elles n’étaient pas initialisées, ou elles expiraient sans prévenir. On a appris à mettre `session_start()` partout et à vérifier les sessions avec `check_session.php`.
- **Rôles capricieux** : un utilisateur pouvait être "Admin" sur une page, puis perdre son rôle sur une autre. On a dû s’assurer que `$_SESSION` soit toujours à jour, surtout quand un admin changeait un rôle.
- **Sécurité bancale** : on a réalisé que les mots de passe étaient en clair dans `users.json`. On n’a pas eu le temps de passer à `password_hash()`, mais c’est prévu pour plus tard.

Ça a été un vrai casse-tête, mais à force de tests et d’ajustements, on a stabilisé le tout.

## 5. Conclusion

Ce projet, c’était comme préparer une recette compliquée : parfois, ça rate, mais on apprend à chaque essai. On a réussi à livrer une application qui fonctionne, avec des fonctionnalités sympas comme les likes, les commentaires, et une gestion des recettes au top. Les appels AJAX ont rendu l’expérience fluide, et l’interface admin donne un vrai contrôle.

### Les Leçons Apprises :
- **Sessions, c’est pas inné** : on a galéré à mettre en place les sessions et à garder les rôles cohérents, mais maintenant, on sait comment faire.
- **Sécurité à améliorer** : stocker les mots de passe en clair, c’est une erreur qu’on ne refera pas.
- **Inspiration bienvenue** : pour la barre de recherche, piocher des idées sur Internet nous a fait gagner du temps.

### Et Après ?
On aimerait sécuriser les mots de passe, ajouter une pagination pour la liste des utilisateurs (parce que s’il y en a trop, ça rame), et pourquoi pas laisser les utilisateurs se suivre entre eux ou recevoir des notifications. Bref, il y a encore plein de bonnes idées à cuisiner !

Ce projet nous a appris à jongler avec PHP, AJAX, et les sessions, tout en résolvant des problèmes concrets. On est fiers du résultat, même s’il reste perfectible – un peu comme une première tarte, délicieuse mais avec une pâte qu’on pourrait encore affiner.