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

// Récupérer les informations actuelles de l'utilisateur
$stmt = $mysqli->prepare("SELECT email, prenom, nom, datenaissance, adresse, telephone FROM utilisateur WHERE nomutilisateur = ?");
$stmt->bind_param("s", $nomutilisateur);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Utilisateur introuvable.";
    exit();
}

$utilisateur = $result->fetch_assoc();

// Mise à jour des informations de l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $datenaissance = $_POST['datenaissance'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];

    $updateStmt = $mysqli->prepare("UPDATE utilisateur SET email = ?, prenom = ?, nom = ?, datenaissance = ?, adresse = ?, telephone = ? WHERE nomutilisateur = ?");
    $updateStmt->bind_param("sssssss", $email, $prenom, $nom, $datenaissance, $adresse, $telephone, $nomutilisateur);

    if ($updateStmt->execute()) {
        echo "<div class='alert alert-success'>Profil mis à jour avec succès.</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la mise à jour du profil.</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil - ShelbyFripe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'menu.php'; ?>

    <div class="container my-5">
        <h1>Modifier Mes Informations</h1>
        <form method="POST" action="modifier_profil.php" class="mt-4">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($utilisateur['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($utilisateur['prenom']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($utilisateur['nom']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Date de Naissance</label>
                <input type="date" name="datenaissance" class="form-control" value="<?= htmlspecialchars($utilisateur['datenaissance']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Adresse</label>
                <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($utilisateur['adresse']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Téléphone</label>
                <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($utilisateur['telephone']) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
