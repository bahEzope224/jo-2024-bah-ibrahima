<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/styles-computer.css">
    <link rel="stylesheet" href="../css/styles-responsive.css">
    <link rel="shortcut icon" href="../img/favicon-jo-2024.ico" type="image/x-icon">
    <title>Liste des Sports - Jeux Olympiques 2024</title>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="sports.php">Sports</a></li>
                <li><a href="events.php">Calendrier des évènements</a></li>
                <li><a href="results.php">Résultats</a></li>
                <li><a href="login.php">Accès administrateur</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Résultats des épreuves</h1>
        <form action="results.php" method="GET">
    <label for="search">Rechercher un resultat :</label>
    <input type="text" id="search" name="search">
    <button class="link-home" type="submit">Rechercher</button>
</form>

        <?php
        require_once("../database/database.php");

        try {
            // Requête pour récupérer la liste des sports depuis la base de données
           // Récupérer le terme de recherche s'il est fourni
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Modifier la requête SQL pour prendre en compte le terme de recherche
$query = "SELECT nom_athlete, prenom_athlete, nom_pays, nom_sport, nom_epreuve, resultat
            FROM athlete
            INNER JOIN pays ON athlete.id_pays = pays.id_pays
            INNER JOIN participer ON athlete.id_athlete = participer.id_athlete
            INNER JOIN epreuve ON participer.id_epreuve = epreuve.id_epreuve
            INNER JOIN sport ON epreuve.id_sport = sport.id_sport
            WHERE nom_athlete LIKE :searchTerm OR prenom_athlete LIKE :searchTerm
            OR nom_pays LIKE :searchTerm OR nom_sport LIKE :searchTerm
            OR nom_epreuve LIKE :searchTerm OR resultat LIKE :searchTerm
            ORDER BY nom_athlete";

$statement = $connexion->prepare($query);
$statement->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
$statement->execute();

            
            // Vérifier s'il y a des résultats
            if ($statement->rowCount() > 0) {
                echo "<table>";
                echo "<tr>
                <th class='color'>Nom </th>
                <th class='color'>Prénom </th>
                <th class='color'>Pays</th>
                <th class='color'>Sport</th>
                <th class='color'>Epreuves</th>
                <th class='color'>Résultats</th>
                </tr>";

                // Afficher les données dans un tableau
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nom_athlete']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['prenom_athlete']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_pays']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_sport']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_epreuve']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['resultat']) . "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p>Aucun sport trouvé.</p>";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
        // Afficher les erreurs en PHP
// (fonctionne à condition d’avoir activé l’option en local)
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        ?>
         <p class="paragraph-link">
            <a class="link-home" href="../index.php">Retour Accueil</a>
        </p>

    </main>
    <footer>
        <figure>
            <img src="../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
</body>

</html>
