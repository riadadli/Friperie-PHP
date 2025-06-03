<?php
session_start();
if (!isset($_SESSION['nomutilisateur'])) {
    header('Location: index.php');
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "shelbyfripe");
if ($mysqli->connect_error) {
    die('Erreur de connexion : ' . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_article'])) {
    $id_article = $mysqli->real_escape_string($_POST['id_article']);

    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    if (isset($_SESSION['panier'][$id_article])) {
        $_SESSION['panier'][$id_article]['quantite']++;
    } else {
        // Récupérer la quantité actuelle du produit depuis la base de données
        $query = "SELECT quantite FROM articles WHERE id_article = '$id_article'";
        $result = $mysqli->query($query);

        if ($result && $result->num_rows > 0) {
            $product_data = $result->fetch_assoc();
            if ($product_data['quantite'] > 0) {
                $_SESSION['panier'][$id_article] = [
                    'quantite' => 1
                ];
                header('Location: acceuil.php');
                exit();
            } else {
                $_SESSION['message'] = "Le produit n'est pas en stock.";
                header('Location: acceuil.php');
                exit();
            }
        }
    }

    header('Location: acceuil.php');
    exit();
}
