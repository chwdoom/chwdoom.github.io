<?php
session_start();
require_once 'logueoadmin.php';
$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error) {
    error_log("Error de conexión: " . $conn->connect_error);
    die("Error de conexión: " . $conn->connect_error);
}

error_log("Método de solicitud: " . $_SERVER['REQUEST_METHOD']);
error_log("Datos de POST: " . print_r($_POST, true));
error_log("Sesión de usuario: " . print_r($_SESSION, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    $idGuia = intval($_POST['id']);
    error_log("ID de guía recibido: " . $idGuia);

    // Verifica que el idGuia es válido
    if ($idGuia > 0) {
        $sql = "DELETE FROM guias WHERE idGuia = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            error_log("Error preparando la consulta: " . $conn->error);
            die("Error preparando la consulta: " . $conn->error);
        }

        $stmt->bind_param('i', $idGuia);
        
        if ($stmt->execute()) {
            error_log("Guía eliminada con éxito. ID: " . $idGuia);
            echo "Guía eliminada con éxito.";
        } else {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            echo "Error al eliminar la guía: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        error_log("ID de guía no válido: " . $idGuia);
        echo "ID de guía no válido.";
    }
} else {
    error_log("Solicitud no válida o permisos insuficientes.");
    echo "Solicitud no válida.";
}

$conn->close();
