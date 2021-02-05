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

//VARIABLES DU FORMULAIRE POUR LA CREATION D'UNE RESSOURCE PATIENT
$nom=null;
$prenom=null;
$dateNaiss=null;
$tel=null;
$libelle_adresse=null;
$cp=null;
$ville=null;
$pays=null;
$genre=null;
?>

<!--FORMULAIRE D'AJOUT DE PATIENT-->
<div id=container>
    <h1>Ajouter un nouveau patient<h1>
        
    <form name=createForm method='POST' action='./scripts/create_patient.php'>
    <?php
    echo "<table id=form>";
        
    echo "<tr>";
        echo "<td><a>Prénom : </a></td>";
            //PRENOM OBLIGATOIRE ALLANT DE 2 A 30 CARACTERES AVEC LETTRES AUTORISEES UNIQUEMENT
            echo "<td><input type='text' name='prenomPatient' pattern='[A-Za-zÀ-ž\s]{2,30}' value='".$prenom."' required></td>";
            echo "<td><a id=details>Texte uniquement (30 caractères maximum)</a></td>";
    echo "</tr>";

    echo "<tr>";
        echo "<td><a>Nom : </a></td>";
            //NOM OBLIGATOIRE ALLANT DE 2 A 30 CARACTERES AVEC LETTRES AUTORISEES UNIQUEMENT
            echo "<td><input type='text' name='nomPatient' pattern='[A-Za-zÀ-ž\s]{2,30}' value='".$nom."' required></td>";
            echo "<td><a id=details>Texte uniquement (30 caractères maximum)</a></td>";
    echo "</tr>";

    echo "<tr>";
        echo "<td><a>Date de naissance : </a></td>";
            //DATE DE NAISSANCE OBLIGATOIRE
            echo "<td><input type='date' name='dateNaissPatient' min='1900-01-01' max='2021-01-01' value='".$dateNaiss."' required></td>";        
    echo "</tr>";

    echo "<tr>";
        echo "<td><a>Numéro de téléphone : </a></td>";
            //NUMERO DE TELEPHONE OBLIGATOIRE DE 10 CARACTERES AVEC CHIFFRES AUTORISES UNIQUEMENT
            echo "<td><input type='text' name='telPatient' pattern='[0-9]{10}' value='".$tel."' required></td>"; 
            echo "<td><a id=details>Numéro sans indicatif pays et symboles (10 chiffres)</a></td>";
    echo "</tr>";       
            
    echo "<tr>";
        echo "<td><a>Genre : </a></td>";
            //GENRE OBLIGATOIRE AVEC LISTE DEROULANTE
            echo "<td><select name='genrePatient' required>";
            echo "<option value='male'>Male (Homme)</option>";
            echo "<option value='female'>Female (Femme)</option>";
            echo "</select></td>";
    echo "</tr>";
                    
    echo "<tr>";
        echo "<td><a>Adresse : </a></td>";
            //ADRESSE OBLIGATOIRE ALLANT DE 2 A 30 CARACTERES AVEC LETTRES (ET ACCENTS) ET CHIFFRES AUTORISES
            echo "<td><input type='text' name='adressePatient' pattern='[A-z0-9À-ž\s]{2,30}' value='".$libelle_adresse."' required></td>";  
            echo "<td><a id=details>Numéro et libellé de rue (30 caractères maximum)</a></td>";
    echo "</tr>";
                    
    echo "<tr>";
        echo "<td><a>Ville : </a></td>";
            //VILLE OBLIGATOIRE ALLANT DE 2 A 30 CARACTERES AVEC LETTRES (ET ACCENTS) ET CHIFFRES AUTORISES
            echo "<td><input type='text' name='villePatient' pattern='[A-Za-zÀ-ž\s]{2,30}' value='".$ville."' required></td>"; 
            echo "<td><a id=details>Texte uniquement (30 caractères maximum)</a></td>";
    echo "</tr>";  

    echo "<tr>";
        echo "<td><a>Code postal : </a></td>";
            //CODE POSTAL OBLIGATOIRE DE 5 CHIFFRES UNIQUEMENT
            echo "<td><input type='text' name='cpPatient'  pattern='[0-9]{5}' value='".$cp."' required><b/td>"; 
            echo "<td><a id=details>Nombre uniquement (5 chiffres)</a></td>";
    echo "</tr>";  

    echo "<tr>";
        echo "<td><a>Pays : </a></td>";
            //PAYS OBLIGATOIRE AVEC LISTE DEROULANTE
            //FRANCE ET US UNIQUEMENT AFIN DE RESPECTER LES CONTRAIRES DU CODE POSTAL A 5 CHIFFRES
            echo "<td><select name='paysPatient' required>";
            echo "<option value='FR'>France</option>";
            echo "<option value='US'>United States</option>";
            echo "</select></td>";
    echo "</tr>";

    echo "</table>";

    echo "<input type=submit value='Créer'>";
    ?>
            
    </form>

  </div>     
</body>

</html>