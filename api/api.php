<?php
/*se importan archivos requeridos*/
require_once '../includes/dbconnect.php';
require_once '../includes/bootstrap.php';

/*validación del método de solicitud, tiene que ser GET*/ 
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $db = db_connect();
        /*consulta y recuperación de eventos de la BD*/
        $stmt = $db->query("SELECT * FROM events");
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /*convertir los datos a formato JSON y se muestra de manera legible*/
        echo "<pre>" . json_encode([
            "status" => "Éxito",
            "data" => $events
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
        /*captura de excepción*/
    } catch (PDOException $e) {
        echo "<pre>" . json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    }
} else {
    echo "<pre>" . json_encode([
        "status" => "Error",
        "message" => "Método de solicitud inváido"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
}
?>
