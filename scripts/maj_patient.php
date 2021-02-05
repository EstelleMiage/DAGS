<?php
//PAGE SCRIPT QUI GERE LA MODIFIER DES DONNEES D'UNE RESSOURCE PATIENT

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
//LES DONNÉES SONT PRÉRENSEIGNÉES DANS LE FORMULAIRE
//AINSI ON RECUPERE TOUTES LES INFORMATIONS, MEME LES DONNÉES QUI N'ONT PAS ÉTÉ MODIFIÉES
$id = $_POST['idPatient'];
$nom = $_POST['nomPatient'];
$prenom = $_POST['prenomPatient'];
$dateNaiss = $_POST['dateNaissPatient'];
$tel = $_POST['telPatient'];
$genre = $_POST['genrePatient'];
$adresse = $_POST['adressePatient'];
$ville = $_POST['villePatient'];
$cp = $_POST['cpPatient'];
$pays = $_POST['paysPatient'];

//VARIABLE CONTENANT LA RESSOURCE AVEC LES DONNÉES A INSERER
$data = '{
    "resourceType": "Patient",
    "id": "'.$id.'",
    "name": [
        {
            "use": "official",
            "text": "'.$prenom." ".$nom.'",
            "family": "'.$nom.'",
            "given": [
                "'.$prenom.'"
            ],
            "prefix": [
                "Ms"
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

//INITIALISATION DE LA REQUETE FHIR
$ch = curl_init();

//SERVEUR FHIR AVEC L'ID DU PATIENT A METTRE A JOUR
$url="https://stu3.test.pyrohealth.net/fhir/Patient/".$id;

//PARAMETRAGE DE LA REQUETE
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/fhir+json',
    'Content-Type: application/json'
));
               
//EXECUTION DE LA REQUETE
$json=curl_exec($ch);

//SI LA REQUETE S'EST EXECUTÉE, ENVOI D'UN MESSAGE DE SUCCES
//SINON, ENVOI D'UN MESSAGE D'ERREUR
//ON RENVOI EGALEMENT L'ID DU PATIENT POUR QUE SES INFORMATIONS SOIENT AFFICHÉES
if (isset($json)) {
    $msg="Patient mis à jour";
    $success=urldecode($msg);
    header ('location: ../modifier_patient.php?success='.$success.'&id='.$id.'');
    exit ();
} else {
    $msg="Une erreur est survenue";
    $error=urldecode($msg);
    header ('location: ../modifier_patient.php?error='.$error.'&id='.$id.'');
    exit ();

}

//FERMETURE
curl_close($ch);    

?>