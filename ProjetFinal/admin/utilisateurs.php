<?php
session_start();

// Vérification pour s'assurer que seul un administrateur peut accéder à cette page
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

// Récupérer tous les utilisateurs de la base de données
$result = $mysqli->query("SELECT * FROM utilisateur");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include('menu_admin.php'); ?>

<div class="container mt-5">
    <h3>Liste des Utilisateurs</h3>
    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom d'utilisateur</th>
                <th>Email</th>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Date de naissance</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($utilisateur = $result->fetch_assoc()): ?>
                <?php
                    // Échapper les valeurs avec real_escape_string pour éviter les failles
                    $id = $utilisateur['utilisateurid'];
                    $nomutilisateur = htmlspecialchars($utilisateur['nomutilisateur']);
                    $email = htmlspecialchars($utilisateur['email']);
                    $prenom = htmlspecialchars($utilisateur['prenom']);
                    $nom = htmlspecialchars($utilisateur['nom']);
                    $datenaissance = $utilisateur['datenaissance'] ? htmlspecialchars($utilisateur['datenaissance']) : 'Non précisé';
                    $adresse = htmlspecialchars($utilisateur['adresse']);
                    $telephone = htmlspecialchars($utilisateur['telephone']);
                ?>
                <tr>
                    <td><?= $id ?></td>
                    <td><?= $nomutilisateur ?></td>
                    <td><?= $email ?></td>
                    <td><?= $prenom ?></td>
                    <td><?= $nom ?></td>
                    <td><?= $datenaissance ?></td>
                    <td><?= $adresse ?></td>
                    <td><?= $telephone ?></td>
                    <td>
                        <a href="?action=supprimer&id=<?= $id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Gestion de l'action de suppression
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id_utilisateur = intval($_GET['id']); // Convertir l'ID pour éviter les failles SQL
    $stmt = $mysqli->prepare("DELETE FROM utilisateur WHERE utilisateurid = ?");
    $stmt->bind_param('i', $id_utilisateur);
    
    if ($stmt->execute()) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<div class='alert alert-danger mt-3'>Erreur lors de la suppression de l'utilisateur.</div>";
    }
}
?>
