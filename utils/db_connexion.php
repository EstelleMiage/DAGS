<?php
//PAGE SCRIPT QUI PERMET LA CONNEXION A LA BDD PHPMYADMIN
//POUR GERER LA CONNEXION DES UTILISATEURS

$DB = null;

//FONCTION QUI PERMET DE SE CONNECTER A LA BDD PHPMYADMIN
function connect() {
    global $DB;
    if($DB == null) {
        //VARIABLES DE CONNEXION A LA BASE DE DONNEES
        $dbType = "mysql";
        $host = "localhost";
        $port = 3306;
        $dbname = "dags";
        $user = "root";
        $password = "";
    //GESTION DES EXCEPTIONS
    try {
        //TENTATIVE DE CONNEXION A LA BASE DE DONNEES
        $DB = new PDO("$dbType:host=$host;port=$port;dbname=$dbname", $user, $password);
    } catch(Exception $e) {
        //EN CAS D'ECHEC, BLOC DE GESTION DE L'ERREUR
        die('Erreur : '.$e->getMessage());
        }
    }
    return $DB;
}

//FONCTION QUI RECUPERE LES RESULTATS D'UNE REQUETE
function execute($request, $data=null) {
    $request->execute($data);
    if($request->errorInfo()[2]) {
        throw new Exception($request->errorInfo()[2]);
    }
}

?>