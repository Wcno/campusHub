<?php
require_once '../../../includes/bootstrap.php';
require_once '../../../includes/dbconnect.php';
$pdo = db_connect();

if (isset($_GET['id'])) {
    $eventId = $_GET['id'];

    // Actualizar el evento para marcarlo como publicado
    $stmt = $pdo->prepare("UPDATE events SET post = TRUE, post_date = NOW() WHERE id = :id");
    $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);

    try {
        if ($stmt->execute()) {
            header("Location: list.php?success=Evento publicado con éxito.");
            exit();
        }
    } catch (PDOException $e) {
        echo "Error al publicar el evento: " . $e->getMessage();
    }
} else {
    echo "No se especificó un ID de evento válido.";
}
?>
