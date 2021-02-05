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

//VARIABLE QUI PERMET DE SAVOIR SI L'UTILISATEUR A LANCER LA RECHERCHE
$showResult=false;

//VARAIBLE CONTENANT LE NOMBRE DE PATIENTS RESULTANTS DE LA REQUETE
$json_size = 0;

//VARIABLE QUI PERMET DE SAVOIR SI L'UTILISATEUR FAIT UNE DEMANDE DE SUPPRESSION DE PATIENT
$showDelete=false;

//SI LE MOT CLÉ 'DELETEID' EST PASSÉ PAR URL, IL SERA AUTOMATIQUEMENT RECUPERÉ ET PLACÉ DANS LA VARIABLE D'ÉTAT
if (isset($_GET['deleteId'])){
  $showDelete=$_GET['deleteId'];
}

//SI LE MOT CLÉ 'SHOWRESULT' EST PASSÉ PAR URL, IL SERA AUTOMATIQUEMENT RECUPERÉ ET PLACÉ DANS LA VARIABLE D'ÉTAT
if(isset($_GET['showResult'])) {
    $showResult  = $_GET['showResult'];
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
    header ('location: ./recherche_patient.php?success='.$success);
    exit();
  } else {
    $msg="Une erreur est survenue";
    $error=urldecode($msg);
    header ('location: ./recherche_patient.php?error='.$error);
    exit();
  }

  //FERMETURE
  curl_close($ch2);
}
?>

<!--FORMULAIRE DE RECHERCHE DE PATIENT-->
<div id=container>
  <h1>Rechercher un patient<h1>     
    <form name=searchPatient method='POST' action='./recherche_patient.php?showResult=true'>
      <input type='text' name='prenomPatient' placeholder='Prénom'>
      <input type='text' name='nomPatient' placeholder='Nom'><br>
      <input type='date' min='1900-01-01' max='2021-01-01' name='dateNaissPatient' placeholder='Date de naissance'>
      <input type='text' name='villePatient' placeholder='Ville de résidence'>
      <input type='submit' value='Lancer la recherche'>
    </form>
</div>     

<?php

//SI L'UTILISATEUR A LANCÉ LA RECHERCHE
if($showResult==true) {
  //RECUPERATION DES VARIABLES ENVOYÉES PAR LE FORMULAIRE DE RECHERCHE
  $nom = $_POST['nomPatient'];
  $prenom = $_POST['prenomPatient'];
  $dateNaiss = $_POST['dateNaissPatient'];
  $ville = $_POST['villePatient'];

  //INITIALISATION DE LA REQUETE FHIR
  $ch = curl_init();

  //SERVEUR FHIR
  $url = "https://stu3.test.pyrohealth.net/fhir/Patient";
      
  //CONDITION POUR AJOUTER LES PARAMETRES DE RECHERCHE
  if ($nom!="") {
    $url=$url."?family=".$nom;
    if ($prenom!="") {
      $url=$url."&given=".$prenom;
      if ($dateNaiss!="") {
        $url=$url."&birthDate=".$dateNaiss;
        if ($ville!="") {
          $url=$url."&city=".$ville;
        }
      }
    } 
    if ($dateNaiss!="") {
      $url=$url."&birthDate=".$dateNaiss;
      if ($ville!="") {
        $url=$url."&city=".$ville;
      }
    }
    if ($ville!="") {
      $url=$url."&city=".$ville;
    }
  } 
  
  if ($prenom!="" && $nom=="") {
    $url=$url."?given=".$prenom;
    if ($dateNaiss!="") {
      $url=$url."&birthDate=".$dateNaiss;
      if ($ville!="") {
        $url=$url."&city=".$ville;
      }
    } 
    if ($ville!="") {
      $url=$url."&city=".$ville;
    }
  } 

  if ($dateNaiss!="" && $prenom=="" && $nom=="") {
    $url=$url."?birthDate=".$dateNaiss;
    if ($ville!="") {
      $url=$url."&city=".$ville;
    }
  }

  if ($ville!="" && $prenom=="" && $nom=="" && $dateNaiss=="") {
    $url=$url."?city=".$ville;
  }

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
  
  //FERMETURE
  curl_close($ch);

  //SI IL N'Y A PAS DE RESULTAT CORRESPONDANT A LA REQUETE
  if ($parsed_json['total']==0) {
    echo "<div id=container>";
      echo "<h1>Resultat de la requête : ".$url."</h1>";
      echo "Aucun résultat";
    echo "</div>";
  } else {
    //SINON AFFICHAGE DE LA LISTE DE RESULTAT ET DE L'URL DE LA REQUETE
    echo "<div id=container><table id=patients>";
      echo "<h1>Resultat de la requête : ".$url."</h1>";
      
      for($i=0; $i<=$parsed_json['total']-1; $i++) {
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
        if(isset($parsed_json['entry'][$i]['resource']['id'])) {
          $id = $parsed_json['entry'][$i]['resource']['id'];
        } else {
          $id = "Aucun id renseigné";
        }
  
        //AFFICHAGE DE LA LISTE DES PATIENTS RESULTANTS
        echo "<tr><td>".$prenom." </td>";
        echo "<td>".$nom." </td>";
        echo "<td>".$id." </td>";

        //AFFICHAGE DES ICONES
        echo "<td><a href='./infos_patient.php?id=".$id."'><img src='images/see.png' height=20 width=20>"."</a></td>";
        echo "<td><a href='./modifier_patient.php?id=".$id."'><img src='images/modif.png' height=20 width=20>"."</a></td>";
        echo "<td><a href='./recherche_patient.php?deleteId=".$id."'><img src='images/delete.jpg' height=25 width=25></td>";
              
      }
      
      echo "</tr></table>";
      echo ("</div>");
    }
  }
  ?>
</body>

</html>