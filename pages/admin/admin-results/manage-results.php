<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
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
    <title>Gestion des resultats - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .action-buttons button {
            background-color: #1b1b1b;
            color: #d7c378;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .action-buttons button:hover {
            background-color: #d7c378;
            color: #1b1b1b;
        }
    </style>


</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
                <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="../admin-sports/manage-sports.php">Gestion Sports</a></li>
                <li><a href="../admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-calendar/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-pays/manage-contries.php">Gestion Pays</a></li>
                <li><a href="../admin-gender/manage-genders.php">Gestion Genres</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="./manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Gestion des resultats</h1>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<p style="color: green;">' . $_SESSION['success'] . '</p>';
            unset($_SESSION['success']);
        }
        ?>

        <div class="action-buttons">
            <button onclick="openAddResultForm()">Ajouter un resultat</button>
            <!-- Autres boutons... -->
        </div>

        <?php
        require_once("../../../database/database.php");

        try {
            // Requête pour récupérer la liste des résultats depuis la base de données
            $query = "SELECT * FROM participer 
            INNER JOIN athlete ON participer.id_athlete = athlete.id_athlete 
            INNER JOIN pays ON athlete.id_pays = pays.id_pays
            INNER JOIN epreuve ON participer.id_epreuve = epreuve.id_epreuve";

            $statement = $connexion->prepare($query);
            $statement->execute();

            // Vérifier s'il y a des résultats
            if ($statement->rowCount() > 0) {
                echo "<table><tr><th>Athlete</th><th>epreuve</th><th>Résultat</th><th>Action</th></tr>";

                // Afficher les données dans un tableau
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    // Assainir les données avant de les afficher
                    echo "<td>" . htmlspecialchars($row['prenom_athlete']) . " " . htmlspecialchars($row['nom_athlete']) .  "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_epreuve']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['resultat']) . "</td>";


                    // Utilisation de la combinaison id_athlete, id_epreuve, resultat comme identifiant unique pour le bouton Modifier
                    $uniqueIdentifiantAction = $row['id_athlete'] . '_' . $row['id_epreuve'] . '_' . $row['resultat'];
                    // echappement des données
                    $uniqueIdentifiantAction = htmlspecialchars($uniqueIdentifiantAction);

                    echo "<td><button onclick='openModifyResultForm(\"{$uniqueIdentifiantAction}\")'>Modifier</button> <button onclick='deleteResultConfirmation(\"{$uniqueIdentifiantAction}\")'>Supprimer</button></td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p>Aucun resultat trouvé.</p>";
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
            <a class="link-home" href="../admin.php">Accueil administration</a>
        </p>

    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
    <script>
        function openAddResultForm() {
            // Ouvrir une fenêtre pop-up avec le formulaire de modification
            // L'URL contien un paramètre "id"
            window.location.href = 'add-result.php';
        }

        function openModifyResultForm(uniqueIdentifiantAction) {
            // Ajoutez ici le code pour afficher un formulaire stylisé pour modifier un resultat
            window.location.href = 'modify-result.php?uniqueIdentifiantAction=' + uniqueIdentifiantAction;
        }

        function deleteResultConfirmation(uniqueIdentifiantAction) {
            // Ajoutez ici le code pour afficher une fenêtre de confirmation pour supprimer un resultat
            if (confirm("Êtes-vous sûr de vouloir supprimer ce resultat ?")) {
                // Ajoutez ici le code pour la suppression du resultat
                // alert(id_resultat);
                window.location.href = 'delete-result.php?uniqueIdentifiantAction=' + uniqueIdentifiantAction;
    }
        }
    </script>
</body>

</html>