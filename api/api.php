<?php
/*se importan archivos requeridos*/
require_once '../includes/dbconnect.php';
require_once '../includes/bootstrap.php';

/*validación del método de solicitud, tiene que ser GET*/ 
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $db = db_connect();
        /*consulta y recuperación de eventos de la BD*/
        $tables = ['users', 'category', 'locations', 'tags', 'events', 'inscriptions'];
        foreach ($tables as $table) {
            $stmt = $db->query("SELECT * FROM $table");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $results[$table] = $data; 
        }

        /*convertir los datos a formato JSON y se muestra de manera legible*/
        echo "<pre>" . json_encode([
            "status" => "Éxito",
            "data" => $results
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
