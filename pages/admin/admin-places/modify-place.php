<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID de l'utilisateur est fourni dans l'URL
if (!isset($_GET['id_lieu'])) {
    $_SESSION['error'] = "ID du lieu manquant.";
    header("Location: manage-places.php");
    exit();
}
$id_lieu = isset($_GET['id_lieu']) ? $_GET['id_lieu'] : null;

$id_lieu = filter_input(INPUT_GET, 'id_lieu', FILTER_VALIDATE_INT);

// Vérifiez si l'ID de l'utilisateur est un entier valide
if (!$id_lieu && $id_lieu !== 0) {
    $_SESSION['error'] = "ID du lieu invalide.";
    header("Location: manage-places.php");
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nomLieu = filter_input(INPUT_POST, '$nomLieu', FILTER_SANITIZE_STRING);
    $adresseLieu = filter_input(INPUT_POST, '$adresseLieu', FILTER_SANITIZE_STRING);
    $codePostal = filter_input(INPUT_POST, '$codePostal', FILTER_SANITIZE_STRING);
    $villeLieu = filter_input(INPUT_POST, '$villeLieu', FILTER_SANITIZE_STRING);

    // Vérifiez si les champs obligatoires ne sont pas vides
    if (empty($nomLieu) || empty($adresseLieu) || empty($codePostal) || empty($villeLieu)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header("Location: modify-place.php?id_lieu=$id_lieu");
        exit();
    }

    try {
        // Vérifiez si le login existe déjà pour un autre utilisateur
        $queryCheck = "SELECT id_lieu FROM lieu WHERE nom_lieu = :nomLieu AND id_lieu  <> :id_lieu";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nom_lieu", $nomLieu, PDO::PARAM_STR);
        $statementCheck->bindParam(":id_lieu", $id_lieu, PDO::PARAM_INT);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "Le lieu existe déjà.";
            header("Location: modify-place.php?id_lieu=$id_lieu");
            exit();
        }

        // Requête pour mettre à jour l'utilisateur
        $query = "UPDATE lieu SET nom_lieu = :nomLieu, adresse_lieu = :adresseLieu, cp_lieu = :codePostal, ville_lieu = :villeLieu WHERE id_lieu = :id_lieu";
        $statement = $connexion->prepare($query);
        $statement->bindParam(":nom_lieu", $nomLieu, PDO::PARAM_STR);
        $statement->bindParam(":adresse_lieu", $adresseLieu, PDO::PARAM_STR);
        $statement->bindParam(":cp_lieu", $codePostal, PDO::PARAM_STR);
        $statement->bindParam(":ville_lieu", $villeLieu, PDO::PARAM_STR);
        $statement->bindParam(":id_lieu", $id_lieu, PDO::PARAM_INT);

        // Exécutez la requête
        if ($statement->execute()) {
            $_SESSION['success'] = "Le lieu a été modifié avec succès.";
            header("Location: manage-places.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la modification du lieu.";
            header("Location: modify-place.php?id_lieu=$id_lieu");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: modify-place.php?id_lieu=$id_lieu");
        exit();
    }
}

// Récupérez les informations du lieu pour affichage dans le formulaire
try {
    $queryUser = "SELECT nom_lieu, adresse_lieu, cp_lieu, ville_lieu FROM lieu WHERE id_lieu = :id_lieu";
    $statementUser = $connexion->prepare($queryUser);
    $statementUser->bindParam(":idUtilisateur", $id_utilisateur, PDO::PARAM_INT);
    $statementUser->execute();

    if ($statementUser->rowCount() > 0) {
        $user = $statementUser->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['error'] = "lieu non trouvé.";
        header("Location: manage-places.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-places.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/normalize.css">
    <link rel="stylesheet" href="../../../css/styles-computer.css">
    <link rel="stylesheet" href="../../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../../img/favicon-jo-2024.ico" type="image/x-icon">
    <title>Modifier un lieu - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
    </style>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
                <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="../admin-users/manage-users.php">Gestion Utilisateurs</a></li>
                <li><a href="../admin-sports/manage-sports.php">Gestion Sports</a></li>
                <li><a href="manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-calendar/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-pays/manage-countries.php">Gestion Pays</a></li>
                <li><a href="../admin-gender/manage-gender.php">Gestion Genres</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a>
                </li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Modifier un lieu</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="modify-place.php?id_lieu=<?php echo $id_lieu; ?>" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir modifier cet lieu?')">
            <label for="nomLieu">Nom lieu:</label>
            <input type="text" name="nomLieu" id="nomLieu" value="<?php echo htmlspecialchars($user['nom_lieu']); ?>"
                required>
            <label for="adresseLieu">adresse lieu :</label>
            <input type="text" name="adresseLieu" id="adresseLieu"
                value="<?php echo htmlspecialchars($user['adresse_lieu']); ?>" required>
            <label for="codePostal">Code postal :</label>
            <input type="number" name="codePostal" id="codePostal"
                value="<?php echo htmlspecialchars($user['cp_lieu']); ?>" required>
            <label for="villeLieu">Ville :</label>
            <input type="villeLieu" name="villeLieu" id="villeLieu"
                value="<?php echo htmlspecialchars($user['ville_lieu']); ?>" required>
            <input type="submit" value="Modifier le lieu">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-places.php">Retour à la gestion des utilisateurs</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
</body>

</html>