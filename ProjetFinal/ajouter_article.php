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

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $categorie = $_POST['categorie'];
    $genre = $_POST['genre'];

    // Gestion de l'image
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'images/';
        $uploadFile = $uploadDir . basename($_FILES['photo']['name']);

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
            $photo = $uploadFile;
        } else {
            echo "Erreur lors du téléchargement de l'image.";
            exit();
        }
    } else {
        echo "Veuillez fournir une image valide.";
        exit();
    }

    $stmt = $mysqli->prepare("INSERT INTO articleattente (nom, description, prix, categorie, genre, photo, nomutilisateur) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $nom, $description, $prix, $categorie, $genre, $photo, $_SESSION['nomutilisateur']);
    if ($stmt->execute()) {
        header('Location: mes_articles.php');
        exit();
    } else {
        echo "Erreur lors de l'ajout de l'article.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Article - ShelbyFripe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/site.css" rel="stylesheet"> <!-- Votre CSS personnalisé -->
</head>
<body>
<?php include 'menu.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg rounded p-4">
                <h1 class="text-center mb-4">Ajouter un nouvel article</h1>
                <form method="POST" action="ajouter_article.php" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nom de l'article</label>
                        <input type="text" name="nom" class="form-control" placeholder="Entrez le nom de l'article" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" placeholder="Entrez une description de l'article" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prix (€)</label>
                        <input type="number" name="prix" step="0.01" class="form-control" placeholder="Ex : 49.99" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catégorie</label>
                        <select name="categorie" class="form-select" required>
                            <option value="Vêtement">Vêtements</option>
                            <option value="Chaussure">Chaussures</option>
                            <option value="Accessoire">Accessoires</option>
                            <option value="Divers">Divers</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Genre</label>
                        <select name="genre" class="form-select" required>
                            <option value="Homme">Homme</option>
                            <option value="Femme">Femme</option>
                            <option value="Unisexe">Unisexe</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*" required>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-primary btn-lg" type="submit">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
