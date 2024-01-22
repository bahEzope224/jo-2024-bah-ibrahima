<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID du PAYS est fourni dans l'URL
if (!isset($_GET['id_sport'])) {
    $_SESSION['error'] = "ID du PAYS manquant.";
    header("Location: manage-contries.php");
    exit();
}

$id_sport = filter_input(INPUT_GET, 'id_sport', FILTER_VALIDATE_INT);

// Vérifiez si l'ID du PAYS est un entier valide
if (!$id_sport && $id_sport !== 0) {
    $_SESSION['error'] = "ID du PAYS invalide.";
    header("Location: manage-contries.php");
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nomPays = filter_input(INPUT_POST, 'nomPays', FILTER_SANITIZE_STRING);

    // Vérifiez si le nom du PAYS est vide
    if (empty($nomPays)) {
        $_SESSION['error'] = "Le nom du PAYS ne peut pas être vide.";
        header("Location: modify-PAYS.php?id_sport=$id_sport");
        exit();
    }

    try {
        // Vérifiez si le PAYS existe déjà
        $queryCheck = "SELECT id_sport FROM PAYS WHERE nom_pays = :nomPays AND id_sport <> :idSport";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nomPays", $nomPays, PDO::PARAM_STR);
        $statementCheck->bindParam(":idSport", $id_sport, PDO::PARAM_INT);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "Le PAYS existe déjà.";
            header("Location: modify-PAYS.php?id_sport=$id_sport");
            exit();
        }

        // Requête pour mettre à jour le PAYS
        $query = "UPDATE PAYS SET nom_pays = :nomPays WHERE id_sport = :idSport";
        $statement = $connexion->prepare($query);
        $statement->bindParam(":nomPays", $nomPays, PDO::PARAM_STR);
        $statement->bindParam(":idSport", $id_sport, PDO::PARAM_INT);

        // Exécutez la requête
        if ($statement->execute()) {
            $_SESSION['success'] = "Le PAYS a été modifié avec succès.";
            header("Location: manage-contries.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la modification du PAYS.";
            header("Location: modify-PAYS.php?id_sport=$id_sport");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: modify-PAYS.php?id_sport=$id_sport");
        exit();
    }
}

// Récupérez les informations du PAYS pour affichage dans le formulaire
try {
    $querySport = "SELECT nom_pays FROM PAYS WHERE id_sport = :idSport";
    $statementSport = $connexion->prepare($querySport);
    $statementSport->bindParam(":idSport", $id_sport, PDO::PARAM_INT);
    $statementSport->execute();

    if ($statementSport->rowCount() > 0) {
        $PAYS = $statementSport->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['error'] = "PAYS non trouvé.";
        header("Location: manage-contries.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-contries.php");
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
    <title>Modifier un PAYS - Jeux Olympiques 2024</title>
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
                <li><a href="../admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-calendar/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="manage-countries.php">Gestion Pays</a></li>
                <li><a href="../admin-gender/manage-gender.php">Gestion Genres</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Modifier un PAYS</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="modify-PAYS.php?id_sport=<?php echo $id_sport; ?>" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir modifier ce PAYS?')">
            <label for=" nomPays">Nom du PAYS :</label>
            <input type="text" name="nomPays" id="nomPays"
                value="<?php echo htmlspecialchars($PAYS['nom_pays']); ?>" required>
            <input type="submit" value="Modifier le PAYS">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-contries.php">Retour à la gestion des sports</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
</body>

</html>