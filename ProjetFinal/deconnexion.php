<?php
session_start(); // Démarrer la session

// Vérifier si une session est active
if (isset($_SESSION['nomutilisateur'])) {
    // Détruire toutes les données de session
    session_unset();
    session_destroy();
}

// Rediriger vers la page de connexion
header('Location: index.php');
exit();
?>
