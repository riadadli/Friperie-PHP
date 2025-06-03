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

// Vérifier si un ID de produit est transmis
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: acceuil.php');
    exit();
}

$id_article = $mysqli->real_escape_string($_GET['id']);
$query = "SELECT * FROM articles WHERE id_article = '$id_article'";
$result = $mysqli->query($query);

if (!$result || $result->num_rows !== 1) {
    header('Location: acceuil.php');
    exit();
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Produit - ShelbyFripe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'menu.php'; ?>

    <div class="container my-5">
        <!-- En-tête -->
        <div class="text-center mb-4">
            <h1>Détails du produit</h1>
        </div>

        <!-- Détails du produit -->
        <div class="row mb-4">
            <div class="col-md-6">
                <img src="<?= htmlspecialchars($product['photo']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($product['nom']) ?>">
            </div>
            <div class="col-md-6">
                <h3><?= htmlspecialchars($product['nom']) ?></h3>
                <p class="text-muted"><?= htmlspecialchars($product['description']) ?></p>
                <p><strong>Catégorie :</strong> <?= htmlspecialchars($product['categorie']) ?></p>
                <p><strong>Genre :</strong> <?= htmlspecialchars($product['genre']) ?></p>
                <p><strong>Taille :</strong> <?= htmlspecialchars($product['taille']) ?></p>
                <p><strong>Quantité en stock :</strong> <?= htmlspecialchars($product['quantite']) ?></p>
                <h4 class="text-success"><?= htmlspecialchars($product['prix']) ?> €</h4>
                
                <!-- Ajouté par avec une couleur spéciale -->
                <p><strong>Ajouté par : </strong><span class="text-primary"><?= htmlspecialchars($product['nomutilisateur']) ?></span></p>

                <!-- Bouton pour ajouter au panier -->
                <form method="post" action="ajouter_panier.php">
                    <input type="hidden" name="id_article" value="<?= $product['id_article'] ?>">
                    <button type="submit" class="btn btn-primary btn-lg mt-3 w-100">Ajouter au panier</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
