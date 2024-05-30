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
    $idUsuario = intval($_POST['id']);
    error_log("ID de usuario recibido: " . $idUsuario);

    // Verifica que el idUsuario es válido
    if ($idUsuario > 0) {
        $sql = "DELETE FROM usuarios WHERE idUsuario = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            error_log("Error preparando la consulta: " . $conn->error);
            die("Error preparando la consulta: " . $conn->error);
        }

        $stmt->bind_param('i', $idUsuario);
        
        if ($stmt->execute()) {
            error_log("Usuario eliminado con éxito. ID: " . $idUsuario);
            echo "Usuario eliminado con éxito.";
        } else {
            error_log("Error al ejecutar la consulta: " . $stmt->error);
            echo "Error al eliminar el usuario: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        error_log("ID de usuario no válido: " . $idUsuario);
        echo "ID de usuario no válido.";
    }
} else {
    error_log("Solicitud no válida o permisos insuficientes.");
    echo "Solicitud no válida.";
}

$conn->close();
