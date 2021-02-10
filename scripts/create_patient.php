<?php
//PAGE SCRIPT QUI CRÉE UNE NOUVELLE RESSOURCE PATIENT DANS LE SERVEUR FHIR

//SESSION UTILISATEUR
session_start();

//PARTAGE DES FONCTIONS UTILISATEUR
include_once('../utils/user_utils.php');

//SECURITÉ : SI L'UTILISATEUR N'EST PAS CONNECTÉ, LA PAGE EST INNACCESSIBLE
//ET L'UTILISATEUR EST RENVOYÉ SUR LA PAGE DE CONNEXION
if(isAuthenticated()===false){
    header('location: ./index.php');
}

//RECUPARTION DES VARIABLES ENVOYÉES PAR LE FORMULAIRE (METHODE POST)
$nom = $_POST['nomPatient'];
$prenom = $_POST['prenomPatient'];
$dateNaiss = $_POST['dateNaissPatient'];
$tel = $_POST['telPatient'];
$genre = $_POST['genrePatient'];
$adresse = $_POST['adressePatient'];
$ville = $_POST['villePatient'];
$cp = $_POST['cpPatient'];
$pays = $_POST['paysPatient'];

//VARIABLE CONTENANT LA RESSOURCE A CREER
$data = '{
    "resourceType": "Patient",
    "name": [
        {
            "use": "official",
            "text": "'.$prenom." ".$nom.'",
            "family": "'.$nom.'",
            "given": [
                "'.$prenom.'"
            ],
            "prefix": [
                "M"
            ]
        }
    ],
    "telecom": [
        {
            "system": "phone",
            "value": "'.$tel.'",
            "use": "mobile"
        }
    ],
    "gender": "'.$genre.'",
    "birthDate": "'.$dateNaiss.'",
    "address": [
        {
            "use": "work",
            "line": [
                "'.$adresse.'"
            ],
            "city": "'.$ville.'",
            "state": "QLD",
            "postalCode": "'.$cp.'",
            "country": "'.$pays.'"
        }
    ]
}';

//TRANSFORMATION DE LA VARIABLE EN FORMAT JSON
json_encode($data);

//INITIALISATION DE LA REQUETE FHIR
$ch = curl_init();

//SERVEUR FHIR
$url="https://stu3.test.pyrohealth.net/fhir/Patient";

//PARAMETRAGE DE LA REQUETE
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/fhir+json',
    'Content-Type: application/json'
));
        
//EXECUTION DE LA REQUETE
$json = curl_exec($ch);

//SI LA REQUETE S'EST EXECUTÉE, ENVOI D'UN MESSAGE DE SUCCÉS
//SINON, ENVOI D'UN MESSAGE D'ERREUR
if (isset($json)) {
    $msg = "Patient créé";
    $success = urldecode($msg);
    header ('location: ../ajout_patient.php?success='.$success);
    exit();
} else {
    $msg = "Une erreur est survenue";
    $error = urldecode($msg);
    header ('location: ../ajout_patient.php?error='.$error);
    exit();
}

//FERMETURE
curl_close($ch);      

?>
