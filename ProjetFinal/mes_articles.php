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

$nomutilisateur = $_SESSION['nomutilisateur'];

// Récupérer les articles publiés
$articlesPublies = $mysqli->query("SELECT * FROM articles WHERE nomutilisateur = '" . $mysqli->real_escape_string($nomutilisateur) . "'");

// Récupérer les articles en attente
$articlesEnAttente = $mysqli->query("SELECT * FROM articleattente WHERE nomutilisateur = '" . $mysqli->real_escape_string($nomutilisateur) . "'");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Articles - ShelbyFripe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'menu.php'; ?>

    <div class="container my-5">
        <h1 class="mb-4">Mes Articles</h1>
        
        <!-- Section des articles publiés -->
        <h2 class="mb-3">Articles publiés</h2>
        <div class="row">
            <?php if ($articlesPublies->num_rows > 0): ?>
                <?php while ($row = $articlesPublies->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <img src="<?= htmlspecialchars($row['photo']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['nom']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['nom']) ?></h5>
                                <p class="card-text"><?= substr(htmlspecialchars($row['description']), 0, 100) ?>...</p>
                                <p class="text-muted"><?= htmlspecialchars($row['prix']) ?> €</p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Aucun article publié pour le moment.</p>
            <?php endif; ?>
        </div>

        <!-- Section des articles en attente -->
        <h2 class="mt-5 mb-3">Articles en attente</h2>
        <div class="row">
            <?php if ($articlesEnAttente->num_rows > 0): ?>
                <?php while ($row = $articlesEnAttente->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <img src="<?= htmlspecialchars($row['photo']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['nom']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['nom']) ?></h5>
                                <p class="card-text"><?= substr(htmlspecialchars($row['description']), 0, 100) ?>...</p>
                                <p class="text-muted"><?= htmlspecialchars($row['prix']) ?> €</p>
                                <span class="badge bg-warning text-dark">En attente de validation</span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Aucun article en attente.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
