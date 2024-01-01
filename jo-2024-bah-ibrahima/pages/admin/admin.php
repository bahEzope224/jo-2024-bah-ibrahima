<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../index.php');
    exit;
}

$login = $_SESSION['login'];
$nom_utilisateur = $_SESSION['prenom_utilisateur'];
$prenom_utilisateur = $_SESSION['nom_utilisateur'];
// Afficher les erreurs en PHP
// (fonctionne à condition d’avoir activé l’option en local)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/normalize.css">
    <link rel="stylesheet" href="../../css/styles-computer.css">
    <link rel="stylesheet" href="../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../img/favicon-jo-2024.ico" type="image/x-icon">
    <title>Jeux Olympiques - Paris 2024</title>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
                <li><a href="../admin/admin.php">Accueil Administration</a></li>
                <li><a href="./admin-sports/manage-sports.php">Gestion Sports</a></li>
                <li><a href="./admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="./admin-events/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="./admin-countries/manage-countries.php">Gestion Pays</a></li>
                <li><a href="./admin-gender/manage-gender.php">Gestion Genres</a></li>
                <li><a href="./admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="./admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../logout.php"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.51428 20H4.51428C3.40971 20 2.51428 19.1046 2.51428 18V6C2.51428 4.89543 3.40971 4 4.51428 4H8.51428V6H4.51428V18H8.51428V20Z"
                                fill="currentColor" />
                            <path
                                d="M13.8418 17.385L15.262 15.9768L11.3428 12.0242L20.4857 12.0242C21.038 12.0242 21.4857 11.5765 21.4857 11.0242C21.4857 10.4719 21.038 10.0242 20.4857 10.0242L11.3236 10.0242L15.304 6.0774L13.8958 4.6572L7.5049 10.9941L13.8418 17.385Z"
                                fill="currentColor" />
                        </svg>
                        <span>déconnecter</span>
                    </a></li>
            </ul>
        </nav>
    </header>
    <main>
        <p class="info-login">
            Bonjour
            <?php echo htmlspecialchars($nom_utilisateur) . " " . htmlspecialchars($prenom_utilisateur) ?>
        </p>
        <p class="category-site">
            <a class="link-category" href="./admin-users/manage-users.php">Gestion Administrateurs</a>
            <a class="link-category" href="./admin-sports/manage-sports.php">Gestion Sports</a>
            <a class="link-category" href="./admin-places/manage-places.php">Gestion Lieux</a>
            <a class="link-category" href="./admin-events/manage-events.php">Gestion Calendrier</a>
            <a class="link-category" href="./admin-countries/manage-countries.php">Gestion Pays</a>
            <a class="link-category" href="./admin-gender/manage-genders.php">Gestion Genres</a>
            <a class="link-category" href="./admin-athletes/manage-athletes.php">Gestion Athlètes</a>
            <a class="link-category" href="./admin-results/manage-results.php">Gestion Résultats</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
</body>
</html>