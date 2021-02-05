<?php
//BARRE DE MENU QUI EST INCLUT DANS PLUSIEURS PAGES
//EVITE LA REPETITION DE CODE

echo "<section id=toolbar>";
    echo "<ul id=toolbar>";
        echo "<li><a href=afficher_patient.php>Voir tous les patients</a></li>";
        echo "<li><a href=recherche_patient.php>Rechercher un patient</a></li>";
        echo "<li><a href=ajout_patient.php>Ajouter un patient</a></li>";
        echo "<li id=logout><a href=./scripts/logout.php>Deconnexion</a></li>";
    echo "</ul>";
echo "</section>";
?>