<?php
//SESSION UTILISATEUR
session_start();

//PARTAGE DES FONCTIONS UTILISATEUR
include_once("./utils/user_utils.php");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Projet DAGS</title>
    <link rel="stylesheet" href="views/style.css">
    <link rel="icon" href="images/short_logo.png">
</head>

<body>
    <!--FORMULAIRE DE CONNEXION-->
    <div id=signIn>
        <img src='./images/logo.png' width=60%><hr>
        <form method='POST' action='./scripts/auth.php'>
        <input type='text' name='username' placeholder='Nom utilisateur' required/>
        <input type='password' name='password' placeholder='Mot de passe' required/>
        <input type='submit' value='Connexion'/>
        </form>
    </div>
<?php 

//AFFICHAGE D'UN MESSAGE D'ERREUR SI LA CONNEXION ECHOUE
if (isset($_GET["error"])) {
    echo "<div id=error>";
    $error = $_GET["error"];
    echo $error;
    echo "</div>";
    }
?>
        
</body>
</html>