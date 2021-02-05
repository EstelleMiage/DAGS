<?php
//PAGE QUI CONTIENT LES FONCTIONS POUR LA GESTION DES UTILISATEURS

//FONCTION QUI VERIFIE SI L'UTILISATEUR EST CONNECTÉ (AUTREMENT DIT : POSSEDE UNE SESSION)
function isAuthenticated() {
    if (isset($_SESSION["user"]) || isset($_SESSION["id"])) {
        return true;
    } else if (isset($_COOKIE["user"]) || isset($_COOKIE["id"])) {
        $_SESSION["user"] = $_COOKIE["user"];
        $_SESSION["id"] = $_COOKIE["id"];
        return true;
    }
    return false;
}

//FONCTION QUI VERIFIE SI L'UTILISATEUR EXISTE DANS LA BDD
function isUserExist ($username, $password) {
    $mysql = connect ();
    $query = "SELECT * FROM user WHERE username=:username AND password=:password";
    $request = $mysql->prepare ($query);
    $data = array (
        "username" => $username,
        "password" => $password
    );
    execute ($request, $data);
    $userInfo=array();

    while ($rows = $request->fetch()) {
        $username = $rows['username'];
        $password = $rows['password'];

        $userInfo=array (
            "username" => $username,
            "password" => $password
        );
    }
    $request->closeCursor();

    if(!empty($userInfo)) {
        return true;
    }
        return false;
}

//FONCTION QUI RETOURNE L'USERNAME DE L'UTILISATEUR DE LA SESSION EN COURS
function getUsername(){
    $username=$_SESSION["user"];
    return $username;
}

//FONCTION QUI DECONNECTE UN UTILISATEUR
//DETRUIT LA SESSION ET EXPIRE LES COOKIES
function logOut() {
    session_destroy();
    setcookie("user", $username, 0, "/", "", false, true);
    setcookie("id", $id, 0, "/", "", false, true);
}

//FONCTION QUI CONNECTE UN UTILISATEUR
//CREATION D'UNE SESSION ET DES COOKIES
function logUser ($username, $id) {
    $_SESSION["user"] = $username;
    $_SESSION["id"] = $id;
    setcookie("user", $username, time()+3600*24, "/", "", false, true);
    setcookie("id", $id, time()+3600*24, "/", "", false, true);
}

//FONCTION QUI RECUPERE L'ID D'UN UTILISATEUR EN FONCTION DE SON USERNAME
function getUserId ($username) {
    $mysql = connect ();
    $query = "SELECT id, username FROM user WHERE username=:username";
    $request = $mysql->prepare ($query);
    $data = array (
        "username" => $username
    );

    execute ($request, $data);
    $id = null;

    if ($row = $request->fetch()) {
        $id = $row['id'];
    }

    return $id;
}

?>