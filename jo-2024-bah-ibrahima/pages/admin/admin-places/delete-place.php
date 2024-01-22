<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'identifiant de l'utilisateur à supprimer est présent dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Identifiant du lieu manquant.";
    header("Location: manage-places.php");
    exit();
}
$idLieu = $_GET['id'];

try {
    // Vérifiez si le lieu existe
    $queryCheck = "SELECT id_lieu FROM lieu WHERE id_lieu = :idLieu";
    $statementCheck = $connexion->prepare($queryCheck);
    $statementCheck->bindParam(":idLieu", $idLieu, PDO::PARAM_INT); // Utilisez $idLieu ici, pas $idUtilisateur
    $statementCheck->execute();

    if ($statementCheck->rowCount() === 0) {
        $_SESSION['error'] = "Le lieu n'existe pas.";
        header("Location: manage-places.php");
        exit();
    }

    // Requête pour supprimer un lieu
    $queryDelete = "DELETE FROM lieu WHERE id_lieu = :idLieu";
    $statementDelete = $connexion->prepare($queryDelete);
    $statementDelete->bindParam(":idLieu", $idLieu, PDO::PARAM_INT);

    // Exécutez la requête
    if ($statementDelete->execute()) {
        $_SESSION['success'] = "Le lieu a été supprimé avec succès.";
        header("Location: manage-places.php");
        exit();
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression du lieu.";
        header("Location: manage-places.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-places.php");
    exit();
}

?>