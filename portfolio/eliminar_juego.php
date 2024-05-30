<?php
require_once 'logueoadmin.php';
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    die("Acceso denegado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $idJuego = intval($_POST['id']);
        $conn = new mysqli($hn, $un, $pw, $db);

        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }

        $sql = "DELETE FROM juegos WHERE idJuego = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $idJuego);

        if ($stmt->execute()) {
            echo "Juego eliminado correctamente.";
        } else {
            echo "Error al eliminar el juego.";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "ID de juego no especificado.";
    }
} else {
    echo "Método no permitido.";
}
