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

// Récupération des articles dans le panier
$panier = isset($_SESSION['panier']) ? $_SESSION['panier'] : [];
$articles = [];
$total = 0;

if (!empty($panier)) {
    $ids = implode(',', array_keys($panier));
    $query = "SELECT * FROM articles WHERE id_article IN ($ids)";
    $result = $mysqli->query($query);

    while ($row = $result->fetch_assoc()) {
        $row['quantite'] = $panier[$row['id_article']]['quantite'];
        $row['sous_total'] = $row['prix'] * $row['quantite'];
        $articles[] = $row;
        $total += $row['sous_total'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - ShelbyFripe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <div class="container my-5">
        <h1 class="mb-4">Votre Panier</h1>

        <?php if (empty($articles)): ?>
            <p class="alert alert-info">Votre panier est vide.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Produit</th>
                        <th>Description</th>
                        <th>Prix Unitaire (€)</th>
                        <th>Quantité</th>
                        <th>Sous-total (€)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($article['photo']) ?>" alt="<?= htmlspecialchars($article['nom']) ?>" class="img-fluid" style="width: 80px; height: 80px;">
                                <br><?= htmlspecialchars($article['nom']) ?>
                            </td>
                            <td><?= htmlspecialchars($article['description']) ?></td>
                            <td><?= htmlspecialchars($article['prix']) ?></td>
                            <td><?= htmlspecialchars($article['quantite']) ?></td>
                            <td><?= htmlspecialchars($article['sous_total']) ?></td>
                            <td>
                                <form method="post" action="modifier_panier.php">
                                    <input type="hidden" name="id_article" value="<?= $article['id_article'] ?>">
                                    <button type="submit" name="action" value="augmenter" class="btn btn-success btn-sm">+</button>
                                    <button type="submit" name="action" value="diminuer" class="btn btn-warning btn-sm">-</button>
                                    <button type="submit" name="action" value="supprimer" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total :</strong></td>
                        <td colspan="2"><strong><?= number_format($total, 2) ?> €</strong></td>
                    </tr>
                </tfoot>
            </table>
            <div class="text-end">
                <a href="paiement.php" class="btn btn-primary btn-lg">Passer au paiement</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
