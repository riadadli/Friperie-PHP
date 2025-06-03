<?php
session_start();
if (!isset($_SESSION['nomutilisateur']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "shelbyfripe");
if ($mysqli->connect_error) {
    die('Erreur de connexion : ' . $mysqli->connect_error);
}

// Valider ou rejeter un article
if (isset($_GET['action'], $_GET['id']) && in_array($_GET['action'], ['valider', 'rejeter'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] === 'valider') {
        // Transfert de l'article vers la table 'articles'
        $query = "INSERT INTO articles (nom, description, prix, categorie, genre, photo, nomutilisateur)
                  SELECT nom, description, prix, categorie, genre, photo, nomutilisateur
                  FROM articleattente WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    // Suppression de l'article dans tous les cas (valide ou rejeté)
    $stmt = $mysqli->prepare("DELETE FROM articleattente WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Récupérer les articles en attente
$result = $mysqli->query("SELECT * FROM articleattente");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - ShelbyFripe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'menu.php'; ?>

    <div class="container my-5">
        <h1>Articles en attente de validation</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Prix</th>
                    <th>Catégorie</th>
                    <th>Genre</th>
                    <th>Photo</th>
                    <th>Ajouté par</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nom']) ?></td>
                        <td><?= substr(htmlspecialchars($row['description']), 0, 50) ?>...</td>
                        <td><?= htmlspecialchars($row['prix']) ?> €</td>
                        <td><?= htmlspecialchars($row['categorie']) ?></td>
                        <td><?= htmlspecialchars($row['genre']) ?></td>
                        <td><img src="<?= htmlspecialchars($row['photo']) ?>" alt="<?= htmlspecialchars($row['nom']) ?>" style="width: 50px;"></td>
                        <td><?= htmlspecialchars($row['nomutilisateur']) ?></td>
                        <td>
                            <a href="?action=valider&id=<?= $row['id'] ?>" class="btn btn-success btn-sm">Valider</a>
                            <a href="?action=rejeter&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Rejeter</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
