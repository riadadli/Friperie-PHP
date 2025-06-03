<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "shelbyfripe");

// Vérifier la connexion à la base de données
if ($mysqli->connect_error) {
    die("Erreur de connexion à la base de données : " . $mysqli->connect_error);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $mysqli->real_escape_string($_POST['nom']);
    $description = $mysqli->real_escape_string($_POST['description']);
    $categorie = $mysqli->real_escape_string($_POST['categorie']);
    $genre = $mysqli->real_escape_string($_POST['genre']);
    $taille = $mysqli->real_escape_string($_POST['taille']);
    $quantite = intval($_POST['quantite']);
    $prix = floatval($_POST['prix']);
    $dateajout = date('Y-m-d H:i:s');
    $nomutilisateur = 'admin';

    // Vérifier si une photo a été uploadée
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoTmp = $_FILES['photo']['tmp_name'];
        $photoName = basename($_FILES['photo']['name']);
        $photoPath = "images/$photoName"; // Chemin ajusté pour remonter au répertoire images

        if (move_uploaded_file($photoTmp, "../$photoPath")) {
            $query = "INSERT INTO articles (nom, description, categorie, genre, taille, quantite, prix, photo, dateajout, nomutilisateur) 
                      VALUES ('$nom', '$description', '$categorie', '$genre', '$taille', '$quantite', '$prix', '$photoPath', '$dateajout', '$nomutilisateur')";

            if ($mysqli->query($query)) {
                header('Location: admin_dashboard.php');
                exit();
            } else {
                $error = "Erreur lors de l'ajout de l'article en base de données.";
            }
        } else {
            $error = "Erreur lors de l'upload de l'image.";
        }
    } else {
        $error = "Une erreur est survenue lors de l'upload de l'image.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un article - ShelbyFripe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('menu_admin.php'); ?>

<div class="container mt-5">
    <h3 class="text-primary mb-4">Ajouter un nouvel article</h3>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom de l'article</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>
        
        <div class="mb-3">
            <label for="categorie" class="form-label">Catégorie</label>
            <select name="categorie" class="form-select" required>
                <option value="Vêtement">Vêtement</option>
                <option value="Chaussure">Chaussure</option>
                <option value="Accessoire">Accessoire</option>
                <option value="Divers">Divers</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="genre" class="form-label">Genre</label>
            <select name="genre" class="form-select" required>
                <option value="Homme">Homme</option>
                <option value="Femme">Femme</option>
                <option value="Unisexe">Unisexe</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="taille" class="form-label">Taille</label>
            <input type="text" name="taille" class="form-control" placeholder="Ex: M, L, XL" required>
        </div>

        <div class="mb-3">
            <label for="quantite" class="form-label">Quantité</label>
            <input type="number" name="quantite" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label for="prix" class="form-label">Prix (€)</label>
            <input type="number" step="0.01" name="prix" class="form-control" placeholder="Ex: 49.99" required>
        </div>

        <div class="mb-3">
            <label for="photo" class="form-label">Photo de l'article</label>
            <input type="file" name="photo" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Ajouter l'article</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
