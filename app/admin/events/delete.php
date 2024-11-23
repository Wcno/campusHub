<?php
require_once '../../../includes/bootstrap.php';
require_once '../../../includes/dbconnect.php';
$pdo = db_connect();

if (isset($_GET['id'])) {
  $eventId = $_GET['id'];

  // Eliminar el evento de la base de datos
  $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
  $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);

  try {
    if ($stmt->execute()) {
      header("Location: list?success=Evento eliminado con éxito.");
      exit();
    }
  } catch (PDOException $e) {
    echo "Error al eliminar el evento: " . $e->getMessage();
  }
} else {
  echo "No se especificó un ID de evento válido.";
}
