<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_article'], $_POST['action'])) {
    $id_article = $_POST['id_article'];
    $action = $_POST['action'];

    if (isset($_SESSION['panier'][$id_article])) {
        if ($action === 'augmenter') {
            $_SESSION['panier'][$id_article]['quantite']++;
        } elseif ($action === 'diminuer') {
            $_SESSION['panier'][$id_article]['quantite']--;
            if ($_SESSION['panier'][$id_article]['quantite'] <= 0) {
                unset($_SESSION['panier'][$id_article]);
            }
        } elseif ($action === 'supprimer') {
            unset($_SESSION['panier'][$id_article]);
        }
    }

    header('Location: panier.php');
    exit();
}
?>
