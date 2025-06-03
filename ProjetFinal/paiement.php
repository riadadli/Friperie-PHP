<?php
session_start();
if (!isset($_SESSION['nomutilisateur'])) {
    header('Location: index.php');
    exit();
}

// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "shelbyfripe");
if ($mysqli->connect_error) {
    die('Erreur de connexion : ' . $mysqli->connect_error);
}

if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit();
}

$panier = $_SESSION['panier'];
$success = true;

$mysqli->begin_transaction();

try {
    foreach ($panier as $id_article => $details) {
        // Récupérer la quantité actuelle de l'article
        $query = "SELECT quantite FROM articles WHERE id_article = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $id_article);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("L'article ID $id_article n'existe pas.");
        }

        $article = $result->fetch_assoc();
        $quantite_disponible = $article['quantite'];

        if ($quantite_disponible < $details['quantite']) {
            throw new Exception("Stock insuffisant pour l'article ID $id_article.");
        }

        // Mettre à jour la quantité en stock
        $nouvelle_quantite = $quantite_disponible - $details['quantite'];
        $query = "UPDATE articles SET quantite = ? WHERE id_article = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ii", $nouvelle_quantite, $id_article);
        $stmt->execute();

        // Supprimer l'article de la base de données s'il n'est plus en stock
        if ($nouvelle_quantite == 0) {
            $query = "DELETE FROM articles WHERE id_article = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("i", $id_article);
            $stmt->execute();
        }
    }

    // Valider la transaction
    $mysqli->commit();
    unset($_SESSION['panier']);
} catch (Exception $e) {
    $mysqli->rollback();
    $success = false;
    $error_message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Paiement - ShelbyFripe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <div class="container my-5">
        <div class="text-center">
            <?php if ($success): ?>
                <h1 class="text-success">Paiement Réussi</h1>
                <p class="mt-4">Votre commande a été effectuée avec succès. Merci d'avoir utilisé ShelbyFripe !</p>
                <a href="acceuil.php" class="btn btn-primary mt-3">Retour à l'accueil</a>
            <?php else: ?>
                <h1 class="text-danger">Échec du Paiement</h1>
                <p class="mt-4">Une erreur est survenue : <?= htmlspecialchars($error_message) ?></p>
                <a href="panier.php" class="btn btn-secondary mt-3">Retour au panier</a>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
