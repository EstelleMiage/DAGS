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

//VARIABLE QUI PERMET DE SAVOIR SI L'UTILISATEUR FAIT UNE DEMANDE DE SUPPRESSION DE PATIENT
$showDelete=false;

//SI LE MOT CLÉ 'DELETEID' EST PASSÉ PAR URL, IL SERA AUTOMATIQUEMENT RECUPERÉ ET PLACÉ DANS LA VARIABLE D'ÉTAT
if (isset($_GET['deleteId'])){
  $showDelete=$_GET['deleteId'];
}

//SI LE MOT CLÉ 'SUCCESS' EST PASSÉ PAR URL, ON AFFICHE UN MESSAGE DE SUCCÉS
if (isset($_GET["success"])) {
  echo "<div id=success>";
  $success = $_GET["success"];
  echo $success;
  echo "</div>";
}

//SI LE MOT CLÉ 'ERROR' EST PASSÉ PAR URL, ON AFFICHE UN MESSAGE D'ERREUR
if (isset($_GET["error"])) {
    echo "<div id=error>";
    $error = $_GET["error"];
    echo $error;
    echo "</div>";
}

//SI L'UTILISATEUR SOUHAITE SUPPRIMER UNE RESSOURCE PATIENT
if($showDelete==true) {

  //INITIALISATION DE LA REQUETE FHIR
  $ch2 = curl_init();
  
  //SERVEUR FHIR AVEC L'ID DU PATIENT A SUPPRIMER
  $url2="https://stu3.test.pyrohealth.net/fhir/Patient/".$showDelete;
  
  //PARAMETRAGE DE LA REQUETE
  curl_setopt($ch2, CURLOPT_URL,$url2);
  curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "DELETE");                        

  //EXECUTION DE LA REQUETE
  $json2=curl_exec($ch2);

  //SI LA REQUETE S'EST EXECUTÉE, ENVOI D'UN MESSAGE DE SUCCÉS
  //SINON, ENVOI D'UN MESSAGE D'ERREUR
  if (isset($json2)) {
    $msg="Patient supprimé";
    $success=urldecode($msg);
    header ('location: ./afficher_patient.php?success='.$success);
    exit();
  } else {
    $msg="Une erreur est survenue";
    $error=urldecode($msg);
    header ('location: ./afficher_patient.php?error='.$error);
    exit();
  }
  
  //FERMETURE
  curl_close($ch2);
}

?>


<!--AFFICHAE DES RESSOURCES PATIENT-->
<div id=container>
  <h1>Liste des patients</h1>

  <table id=patients>
    <tr>
      <th>Prénom</th>
      <th>Nom</th>
      <th>Numéro d'identification</th>
      <th>Aperçu</th>
      <th>Modifier</th>
      <th>Supprimer</th>
    </tr>
    
    <?php

    //INITIALISATION DE LA REQUETE FHIR
    $ch = curl_init();

    //SERVEUR FHIR
    $url="https://stu3.test.pyrohealth.net/fhir/Patient";

    //PARAMETRAGE DE LA REQUETE
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Accept: application/fhir+json'
    ));
                        
    //EXECUTION DE LA REQUETE
    $json=curl_exec($ch);

    //PARSAGE DU JSON
    $parsed_json = json_decode($json, true);

    //VARIALES QUI SERONT AFFICHÉES DANS LA LISTE
    $id=null;
    $nom=null;
    $prenom=null;

    $json_size = 0;
    foreach($parsed_json['entry'] as $value) {
      $json_size+=1;
    }

    for($i=0; $i<=$json_size-1; $i++) {
      //RECUPERATION DU PRENOM
      if(isset($parsed_json['entry'][$i]['resource']['name'][0]['given'][0])) {
        $prenom = $parsed_json['entry'][$i]['resource']['name'][0]['given'][0];
      } else {
        $prenom = "Aucun nom renseigné";
      }

      //RECUPERATION DU NOM
      if(isset($parsed_json['entry'][$i]['resource']['name'][0]['family'])) {
        $nom = $parsed_json['entry'][$i]['resource']['name'][0]['family'];
      } else {
        $nom = "Aucun nom renseigné";
      }

      //RECUPERATION DE L'ID
      $id=$parsed_json['entry'][$i]['resource']['id'];

      //AFFICHAE DES DONNEES PATIENT
      echo "<tr>";
        echo "<td>".$prenom." </td>";
        echo "<td>".$nom." </td>";
        echo "<td>".$id." </td>";

        //AFFICHAGE DES ICONES
        echo "<td><a href='./infos_patient.php?id=".$id."'><img src='images/see.png' height=20 width=20>"."</a></td>";
        echo "<td><a href='./modifier_patient.php?id=".$id."'><img src='images/modif.png' height=20 width=20>"."</a></td>";
        echo "<td><a href='./afficher_patient.php?deleteId=".$id."'><img src='images/delete.jpg' height=25 width=25></td>";    
    }
    
    echo "</tr></table>";
    
    //FERMETURE
    curl_close($ch);
    ?>
        
</div>
</body>

</html>