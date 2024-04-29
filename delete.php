<?php
// Votre connexion à la base de données
$host = 'localhost';
$dbname = 'data';
$username = 'root';
$password = '';

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si l'ID de l'utilisateur à supprimer est présent dans la requête
    if (isset($_POST['userId'])) {
        $userId = $_POST['userId'];

        // Préparer et exécuter la requête DELETE
        $stmt = $pdo->prepare("DELETE FROM user WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Réponse de succès
        echo "User deleted successfully!";
    } else {
        // Si userId n'est pas présent
        echo "User ID not provided.";
    }
} catch (PDOException $e) {
    // En cas d'erreur
    echo "Error: " . $e->getMessage();
}
?>
