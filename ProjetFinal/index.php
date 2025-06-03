<?php 
session_start();
$mysqli = new mysqli("localhost", "root", "", "shelbyfripe");

// Vérifier la connexion à la base de données
if ($mysqli->connect_error) {
    die("Erreur de connexion à la base de données : " . $mysqli->connect_error);
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomutilisateur = $mysqli->real_escape_string($_POST['nomutilisateur']);
    $motdepasse = $mysqli->real_escape_string($_POST['motdepasse']);

    // Vérification des identifiants administrateur
    if ($nomutilisateur === 'ShelbyFripe' && $motdepasse === '123') {
        $_SESSION['role'] = 'admin';
        $_SESSION['nomutilisateur'] = 'admin';
        header('Location: admin/admin_dashboard.php');
        exit();
    }

    // Vérification des identifiants utilisateur dans la base de données
    $query = "SELECT * FROM utilisateur WHERE nomutilisateur = '$nomutilisateur'";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($motdepasse == $user['motdepasse']) {
            $_SESSION['role'] = 'utilisateur';
            $_SESSION['nomutilisateur'] = $user['nomutilisateur'];
            header('Location: acceuil.php');
            exit();
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Utilisateur inconnu ou non enregistré.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ShelbyFripe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card shadow-lg">
            <div class="card-header text-center text-white">
                <h3>Connexion - ShelbyFripe</h3>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="nomutilisateur" class="form-label">Nom d'utilisateur</label>
                        <input type="text" name="nomutilisateur" id="nomutilisateur" class="form-control" placeholder="Votre nom d'utilisateur" required>
                    </div>
                    <div class="mb-3">
                        <label for="motdepasse" class="form-label">Mot de passe</label>
                        <input type="password" name="motdepasse" id="motdepasse" class="form-control" placeholder="Votre mot de passe" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>
            </div>
            <div class="card-footer text-center">
                <p>Pas encore inscrit ? <a href="inscription.php">Créer un compte</a></p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
