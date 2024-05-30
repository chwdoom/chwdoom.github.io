<?php
session_start();
require_once 'logueoadmin.php';
$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error) {
    error_log("Error de conexión: " . $conn->connect_error);
    die("Error de conexión: " . $conn->connect_error);
}

error_log("Metodo de solicitud: " . $_SERVER['REQUEST_METHOD']);
error_log("Datos de POST: " . print_r($_POST, true));
error_log("Sesion de usuario: " . print_r($_SESSION, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin') {
    $idResena = intval($_POST['id']);
    error_log("ID de reseña recibido: " . $idResena);

    // Verifica que el idResena es válido
    if ($idResena > 0) {
        $sql = "DELETE FROM reseñas WHERE idReseña = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            error_log("Error preparando la consulta: " . $conn->error);
            die("Error preparando la consulta: " . $conn->error);
        }

        $stmt->bind_param('i', $idResena);
        
        if ($stmt->execute()) {
            error_log("Reseña eliminada con éxito. ID: " . $idResena);
            echo "Reseña eliminada con éxito.";
        } else {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            echo "Error al eliminar la reseña: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        error_log("ID de reseña no válido: " . $idResena);
        echo "ID de reseña no válido.";
    }
} else {
    error_log("Solicitud no válida o permisos insuficientes.");
    echo "Solicitud no válida.";
}

$conn->close();
