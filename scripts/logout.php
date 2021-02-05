<?php
//PAGE SCRIPT QUI DECONNECTE UN UTILISATEUR

//SESSION UTILISATEUR
session_start();

//PARTAGE DES FONCTIONS UTILISATEUR
include_once(__DIR__.'/../utils/user_utils.php');

//DECONNEXION
logOut();

//REDIRECTION VERS LA PAGE D'ACCUEIL
header('location:../index.php');
exit();

?>