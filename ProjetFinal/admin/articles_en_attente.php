<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "shelbyfripe");

// Vérification de la connexion
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

// Gestion de l'action de validation ou de suppression
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    
    if ($action === 'valider') {
        // Insérer l'article dans la table 'articles' et supprimer de la table 'articleattente' après validation
        $mysqli->query("
            INSERT INTO articles (nom, description, categorie, genre, taille, quantite, prix, photo, dateajout, nomutilisateur) 
            SELECT nom, description, categorie, genre, taille, quantite, prix, photo, NOW(), nomutilisateur 
            FROM articleattente 
            WHERE id_articleattente = $id
        ");
        
        $mysqli->query("DELETE FROM articleattente WHERE id_articleattente = $id");
    } elseif ($action === 'supprimer') {
        // Supprimer l'article directement de la table 'articleattente'
        $mysqli->query("DELETE FROM articleattente WHERE id_articleattente = $id");
    }
    
    header('Location: articles_en_attente.php');
    exit();
}

// Récupérer tous les articles en attente
$result = $mysqli->query("SELECT * FROM articleattente");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles en Attente - ShelbyFripe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include('menu_admin.php'); ?>

<div class="container mt-5">
    <h3>Articles en attente de validation</h3>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Catégorie</th>
                <th>Genre</th>
                <th>Taille</th>
                <th>Quantité</th>
                <th>Prix</th>
                <th>Photo</th>
                <th>Utilisateur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($article = $result->fetch_assoc()): ?>
                <?php
                    // Échapper les valeurs avec real_escape_string pour éviter les failles
                    $id = $article['id_articleattente'];
                    $nom = $mysqli->real_escape_string($article['nom']);
                    $description = $mysqli->real_escape_string($article['description']);
                    $categorie = $mysqli->real_escape_string($article['categorie']);
                    $genre = $mysqli->real_escape_string($article['genre']);
                    $taille = $mysqli->real_escape_string($article['taille']);
                    $quantite = $article['quantite'];
                    $prix = $article['prix'];
                    $photo = $article['photo'];
                    $nomutilisateur = $mysqli->real_escape_string($article['nomutilisateur']);
                ?>
                <tr>
                    <td><?= htmlspecialchars($id) ?></td>
                    <td><?= htmlspecialchars($nom) ?></td>
                    <td><?= htmlspecialchars($description) ?></td>
                    <td><?= htmlspecialchars($categorie) ?></td>
                    <td><?= htmlspecialchars($genre) ?></td>
                    <td><?= htmlspecialchars($taille) ?></td>
                    <td><?= htmlspecialchars($quantite) ?></td>
                    <td><?= htmlspecialchars($prix) ?> €</td>
                    <td>
                        <img src="../<?= htmlspecialchars($photo) ?>" alt="Photo" width="100">
                    </td>
                    <td><?= htmlspecialchars($nomutilisateur) ?></td>
                    <td>
                        <a href="?action=valider&id=<?= $id ?>" class="btn btn-success btn-sm">Valider</a>
                        <a href="?action=supprimer&id=<?= $id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
