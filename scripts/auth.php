<?php
//PAGE SCRIPT QUI GERE LA CONNECTION DES UTILISATEURS
//LES UTILISATEURS SONT ENREGISTRÉS DANS PHPMYADMIN
//PAR MESURES DE SECURITÉ, L'INSCRIPTION EST IMPOSSIBLE VIA L'APPLICATION WEB
//ON SUPPOSE QUE LA CREATION DE COMPTES EST GERÉE PAR UN ORGANISME EXTERNE

//SESSION UTILISATEUR
session_start();

//PARTAGE DES FICHIERS DE CONNEXION A LA BDD ET LES FONCTIONS UTILISATEUR
include_once('../utils/db_connexion.php');
include_once('../utils/user_utils.php');

//SECURITÉ : SI L'UTILISATEUR N'EST PAS CONNECTÉ, LA PAGE EST INNACCESSIBLE
//ET L'UTILISATEUR EST RENVOYÉ SUR LA PAGE DE CONNEXION
if (isAuthenticated()===false) {
    header('location: ./index.php');
}

//RECUPARTION DES VARIABLES ENVOYÉES PAR LE FORMULAIRE (METHODE POST)
$username = $_POST["username"];
$password = $_POST["password"];

//VERIFICATION DE L'EXISTANCE DE L'UTILISATEUR DANS LE BDD
$exist = isUserExist ($username, $password);

//RECUPERATION DE L'ID UTILISATEUR
$id = getUserId ($username);

//SI L'UTILISATEUR N'EXISTE PAS
//IL EST RENVOYÉ SUR LA PAGE DE CONNECTION AVEC UN MESSAGE D'ERREUR
//SINON, IL EST CONNECTÉ ET DIRIGÉ VERS LA PREMIERE PAGE DU SITE WEB
if ($exist == false) {
    $msg = "Wrong Username or Wrong Password";
    $error = urldecode($msg);
    header('location: ../index.php?error='.$error.'');
    exit();
}
else {
    logUser($username, $id);
    header('location: ../afficher_patient.php');
    exit();
}

?>