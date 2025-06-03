<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mysqli = new mysqli("localhost", "root", "", "shelbyfripe");
    if ($mysqli->connect_error) {
        die('Erreur de connexion : ' . $mysqli->connect_error);
    }

    // Récupération des données du formulaire
    $nom = $mysqli->real_escape_string($_POST['nom']);
    $prenom = $mysqli->real_escape_string($_POST['prenom']);
    $datenaissance = $mysqli->real_escape_string($_POST['datenaissance']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $adresse = $mysqli->real_escape_string($_POST['adresse']);
    $telephone = $mysqli->real_escape_string($_POST['telephone']);
    $nomutilisateur = $mysqli->real_escape_string($_POST['nomutilisateur']);
    $motdepasse = $mysqli->real_escape_string($_POST['motdepasse']);
    $confirm_motdepasse = $mysqli->real_escape_string($_POST['confirm_motdepasse']);

    // Vérification que les mots de passe correspondent
    if ($motdepasse === $confirm_motdepasse) {
        $checkUserQuery = "SELECT * FROM utilisateur WHERE nomutilisateur = '$nomutilisateur' OR email = '$email'";
        $result = $mysqli->query($checkUserQuery);

        if ($result->num_rows > 0) {
            $error = "Le nom d'utilisateur ou l'email est déjà utilisé.";
        } else {
            $req = "INSERT INTO utilisateur (nom, prenom, datenaissance, email, adresse, telephone, nomutilisateur, motdepasse) 
                    VALUES ('$nom', '$prenom', '$datenaissance', '$email', '$adresse', '$telephone', '$nomutilisateur', '$motdepasse')";
            if ($mysqli->query($req)) {
                $success = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            } else {
                $error = "Erreur lors de l'inscription : " . $mysqli->error;
            }
        }
    } else {
        $error = "Les mots de passe ne correspondent pas.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - ShelbyFripe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8">
      <div class="card">
        <div class="card-header text-center">
          <h3>Inscription</h3>
        </div>
        <div class="card-body">
          <form method="post" action="">
            <div class="row g-3">
              <!-- Colonne gauche -->
              <div class="col-lg-6 col-12">
                <div class="mb-3">
                  <label for="nom" class="form-label">Nom</label>
                  <input type="text" name="nom" id="nom" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="prenom" class="form-label">Prénom</label>
                  <input type="text" name="prenom" id="prenom" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="datenaissance" class="form-label">Date de naissance</label>
                  <input type="date" name="datenaissance" id="datenaissance" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Adresse Email</label>
                  <input type="email" name="email" id="email" class="form-control" required>
                </div>
              </div>
              <!-- Colonne droite -->
              <div class="col-lg-6 col-12">
                <div class="mb-3">
                  <label for="adresse" class="form-label">Adresse</label>
                  <input type="text" name="adresse" id="adresse" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="telephone" class="form-label">Numéro de téléphone</label>
                  <input type="text" name="telephone" id="telephone" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="nomutilisateur" class="form-label">Nom d'utilisateur</label>
                  <input type="text" name="nomutilisateur" id="nomutilisateur" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="motdepasse" class="form-label">Mot de passe</label>
                  <input type="password" name="motdepasse" id="motdepasse" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="confirm_motdepasse" class="form-label">Confirmez le mot de passe</label>
                  <input type="password" name="confirm_motdepasse" id="confirm_motdepasse" class="form-control" required>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">S'inscrire</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

</div>
</body>
</html>
