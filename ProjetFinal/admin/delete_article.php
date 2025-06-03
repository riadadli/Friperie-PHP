<?php
session_start();

// Vérification si l'utilisateur est authentifié en tant qu'admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "shelbyfripe");
if ($mysqli->connect_error) {
    die('Erreur de connexion : ' . $mysqli->connect_error);
}

// Vérification de l'ID envoyé via POST
if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = intval($_POST['id']);

    // Préparation de la requête SQL
    $query = $mysqli->prepare("DELETE FROM articles WHERE id_article = ?");
    $query->bind_param("i", $id);

    if ($query->execute()) {
        header('Location: admin_dashboard.php');
        exit();
    } else {
        echo "<div class='alert alert-danger'>Échec de la suppression de l'article</div>";
    }
} else {
    header('Location: admin_dashboard.php');
    exit();
}
?>
