<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nomAthlete = filter_input(INPUT_POST, 'nomAthlete', FILTER_SANITIZE_STRING);
    $prenomAthlete = filter_input(INPUT_POST, 'prenomAthlete', FILTER_SANITIZE_STRING);
    $idPays = filter_input(INPUT_POST, 'idPays', FILTER_VALIDATE_INT);
    $idGenre = filter_input(INPUT_POST, 'idGenre', FILTER_VALIDATE_INT);

    // Vérifiez si les champs requis sont vides
    if (empty($nomAthlete) || empty($prenomAthlete) || !$idPays || !$idGenre) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header("Location: add-athlete.php");
        exit();
    }

    try {
    // Vérifiez si l'utilisateur existe déjà
    $queryCheck = "SELECT id_athlete FROM athlete WHERE nom_athlete = :nomAthlete AND prenom_athlete = :prenomAthlete";
    $statementCheck = $connexion->prepare($queryCheck);
    $statementCheck->bindParam(":nomAthlete", $nomAthlete, PDO::PARAM_STR);
    $statementCheck->bindParam(":prenomAthlete", $prenomAthlete, PDO::PARAM_STR);
    $statementCheck->execute();

    if ($statementCheck !== null && $statementCheck->rowCount() > 0) {
        $_SESSION['error'] = "L'athlète existe déjà.";
        header("Location: add-athlete.php");
        exit();
    } else {
            // Requête pour ajouter un athlète
            $query = "INSERT INTO athlete (nom_athlete, prenom_athlete, id_pays, id_genre) VALUES (:nomAthlete, :prenomAthlete, :idPays, :idGenre)";
            $statement = $connexion->prepare($query);
            $statement->bindParam(":nomAthlete", $nomAthlete, PDO::PARAM_STR);
            $statement->bindParam(":prenomAthlete", $prenomAthlete, PDO::PARAM_STR);
            $statement->bindParam(":idPays", $idPays, PDO::PARAM_INT);
            $statement->bindParam(":idGenre", $idGenre, PDO::PARAM_INT);

            // Exécutez la requête
            if ($statement->execute()) {
                $_SESSION['success'] = "L'athlète a été ajouté avec succès.";
                header("Location: manage-athletes.php");
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout de l'athlète.";
                header("Location: add-athlete.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-athlete.php");
        exit();
    }
}

// Afficher les erreurs en PHP (fonctionne à condition d’avoir activé l’option en local)
error_reporting(E_ALL);
ini_set("display_errors", 1);
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
    <title>Ajouter un Athlète - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
    </style>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
                <li><a href="../admin-users/manage-users.php">Gestion Utilisateurs</a></li>
                <li><a href="../admin-sports/manage-sports.php">Gestion Sports</a></li>
                <li><a href="../admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-calendar/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-pays/manage-countries.php">Gestion Pays</a></li>
                <li><a href="../admin-gender/manage-genders.php">Gestion Genres</a></li>
                <li><a href="manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Ajouter un Athlète</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="add-athlete.php" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter cet athlète?')">
            <label for="nomAthlete">Nom de l'Athlète :</label>
            <input type="text" name="nomAthlete" id="nomAthlete" required>
            <label for="prenomAthlete">Prénom de l'Athlète :</label>
            <input type="text" name="prenomAthlete" id="prenomAthlete" required>
            <label for="idPays">Pays :</label>
            <!-- Assuming you have a list of countries in your database -->
            <select name="idPays" id="idPays" required>
                <?php
                $queryCountries = "SELECT * FROM pays";
                $resultCountries = $connexion->query($queryCountries);

                while ($country = $resultCountries->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $country['id_pays'] . '">' . $country['nom_pays'] . '</option>';
                }
                ?>
            </select>
            <label for="idGenre">Genre :</label>
            <!-- Assuming you have a list of genres in your database -->
            <select name="idGenre" id="idGenre" required>
                <?php
                $queryGenres = "SELECT * FROM genre";
                $resultGenres = $connexion->query($queryGenres);

                while ($genre = $resultGenres->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $genre['id_genre'] . '">' . $genre['nom_genre'] . '</option>';
                }
                ?>
            </select>
            <input type="submit" value="Ajouter l'Athlète">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-athletes.php">Retour à la gestion des athlètes</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>

</body>

</html>
