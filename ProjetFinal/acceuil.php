<?php 
session_start();
if (!isset($_SESSION['nomutilisateur'])) {
    header('Location: index.php');
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "shelbyfripe");
if ($mysqli->connect_error) {
    die('Erreur de connexion : ' . $mysqli->connect_error);
}

$filterQuery = "SELECT * FROM articles";
$filterConditions = [];

if (isset($_GET['genre']) && !empty($_GET['genre'])) {
    $genre = $mysqli->real_escape_string($_GET['genre']);
    $filterConditions[] = "genre = '$genre'";
}

if (isset($_GET['categorie']) && !empty($_GET['categorie'])) {
    $categorie = $mysqli->real_escape_string($_GET['categorie']);
    $filterConditions[] = "categorie = '$categorie'";
}

if (!empty($filterConditions)) {
    $filterQuery .= " WHERE " . implode(' AND ', $filterConditions);
}

$result = $mysqli->query($filterQuery);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - ShelbyFripe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <header class="text-center py-5">
        <h1 class="animate__animated animate__fadeInDown">Bienvenue sur ShelbyFripe</h1>
        <p class="lead animate__animated animate__fadeInUp">Découvrez des articles vintage uniques et élégants.</p>
    </header>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-md-6">
                <select name="categorie" class="form-select" onchange="filterResults()" id="categorie">
                    <option value="">Toutes les catégories</option>
                    <option value="Vêtement">Vêtements</option>
                    <option value="Chaussure">Chaussures</option>
                    <option value="Accessoire">Accessoires</option>
                    <option value="Divers">Divers</option>
                </select>
            </div>
            <div class="col-md-6">
                <select name="genre" class="form-select" onchange="filterResults()" id="genre">
                    <option value="">Tous les genres</option>
                    <option value="Homme">Homme</option>
                    <option value="Femme">Femme</option>
                    <option value="Unisexe">Unisexe</option>
                </select>
            </div>
        </div>

        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100 animate__animated animate__fadeInUp">
                        <img src="<?= htmlspecialchars($row['photo']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['nom']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['nom']) ?></h5>
                            <p class="card-text"><?= substr(htmlspecialchars($row['description']), 0, 100) ?>...</p>
                            <p class="text-muted"><?= htmlspecialchars($row['prix']) ?> €</p>
                            <p><strong>Ajouté par :</strong> <span class="text-primary"><?= htmlspecialchars($row['nomutilisateur']) ?></span></p>
                        </div>
                        <div class="card-footer">
                            <a href="details.php?id=<?= $row['id_article'] ?>" class="btn btn-primary btn-sm w-100">Voir Détails</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function filterResults() {
            const categorie = document.getElementById('categorie').value;
            const genre = document.getElementById('genre').value;

            let url = "acceuil.php";
            if (categorie || genre) {
                url += `?categorie=${categorie}&genre=${genre}`;
            }
            window.location.href = url;
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
