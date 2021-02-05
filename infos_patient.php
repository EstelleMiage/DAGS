<?php
//SESSION UTILISATEUR
session_start();

//PARTAGE DES FICHIERS DE CONNEXION A LA BDD ET LES FONCTIONS UTILISATEUR
include_once('utils/user_utils.php');
include_once('utils/db_connexion.php');
?>

<!DOCTYPE html>
<head>
    <title>Projet DAGS</title>
    <link rel="stylesheet" href="views/style.css">
    <link rel="icon" href="images/short_logo.png">
</head>

<body>
<?php
//SECURITÉ : SI L'UTILISATEUR N'EST PAS CONNECTÉ, LA PAGE EST INNACCESSIBLE
//ET L'UTILISATEUR EST RENVOYÉ SUR LA PAGE DE CONNEXION
if(isAuthenticated()===false){
  header('location: ./index.php');
}

//AFFICHAGE DE LA BARRE DE MENU
include('./views/toolbar.inc.php');

//RECUPARATION DE L'ID DU PATIENT POUR LEQUEL L'UTILISATEUR SOUHAITE VOIR LES DETAILS
$idpatient = $_GET['id'];
?>

<!--AFFICHAGE DES INFORMATIONS D'UN PATIENT-->
<div id=container>
  <h1>Informations du patient</h1>
  <?php

  //INITIALISATION DE LA REQUETE FHIR
  $ch = curl_init();

  //SERVEUR FHIR AVEC L'ID DU PATIENT A VOIR
  $url="https://stu3.test.pyrohealth.net/fhir/Patient/".$idpatient;

  //PARAMETRAGE DE LA REQUETE
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/fhir+json'
  ));
     
  //EXECUTION DE LA REQUETE
  $json=curl_exec($ch);

  $parsed_json = json_decode($json, true);

  //VARIABLES A AFFICHER
  $nom=null;
  $prenom=null;
  $dateNaiss=null;
  $tel=null;
  $adresse=null;
  $genre=null;
          
  //RECUPERATION DU PRENOM
  if(isset($parsed_json['name'][0]['given'][0])) {
    $prenom = $parsed_json['name'][0]['given'][0];
  } else {
    $prenom = "Aucun nom renseigné";
  }

  //RECUPERATION DU NOM
  if(isset($parsed_json['name'][0]['family'])) {
    $nom = $parsed_json['name'][0]['family'];
  } else {
    $nom = "Aucun nom renseigné";
  }

  //RECUPERATION DU GENRE
  if(isset($parsed_json['gender'])) {
    $genre = $parsed_json['gender'];
  } else {
    $genre = "Aucun genre renseigné";
  }

  //RECUPERATION DE LA DATE DE NAISSANCE
  if(isset($parsed_json['birthDate'])) {
    $dateNaiss = $parsed_json['birthDate'];
  } else {
    $dateNaiss = "Aucune date de naissance renseignée";
  }

  //RECUPERATION DU NUMERO DE TELEPHONE
  if(isset($parsed_json['telecom'][0]['value'])) {
    $tel = $parsed_json['telecom'][0]['value'];
  } else {
    $tel = "Aucun numéro de téléphone renseigné";
  }

  //RECUPERATION DE L ADRESSE
  if(isset($parsed_json['address'])) {
    $adresse = $parsed_json['address'][0]['line'][0];
    $adresse = $adresse.", ".$parsed_json['address'][0]['postalCode'];
    $adresse = $adresse." ".$parsed_json['address'][0]['city'];
    $adresse = $adresse." (".$parsed_json['address'][0]['country'].")";
  } else {
    $adresse = "Aucune adresse renseignée";
  }

  echo "<table id=infos>";
    echo "<tr><td>Id : </td><td>".$idpatient."</td></tr>";
    echo "<tr><td>Prénom : </td><td>".$prenom."</td></tr>";
    echo "<tr><td>Nom : </td><td>".$nom."</td></tr>";
    echo "<tr><td>Date de naissance : </td><td>".$dateNaiss."</td></tr>";
    echo "<tr><td>Numéro de téléphone : </td><td>".$tel."</td></tr>";
    echo "<tr><td>Genre : </td><td>".$genre."</td></tr>";
    echo "<tr><td>Adresse : </td><td>".$adresse."</td></tr>";          
  echo "</table>";

  //FERMETURE
  curl_close($ch);
  ?>

</div>
</body>

</html>