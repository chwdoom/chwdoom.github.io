<?php
require_once 'logueoadmin.php';
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}
$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nota = $_POST['nota'];
    $contenido = $_POST['contenido'];
    $idJuego = $_POST['idJuego'];

    if ($nota < 0 || $nota > 10) {
        echo "Error: La nota debe estar entre 0 y 10.";
        header("Location: pagina_juego.php?id=$idJuego");
        exit();
    }

    $sql = "INSERT INTO reseñas (idJuego, idUsuario, texto, nota) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iisi', $idJuego, $_SESSION['idusuario'], $contenido, $nota);

    if ($stmt->execute()) {
        // Actualizar la nota media del juego
        $sqlMedia = "SELECT AVG(nota) as notaMedia FROM reseñas WHERE idJuego = ?";
        $stmtMedia = $conn->prepare($sqlMedia);
        $stmtMedia->bind_param('i', $idJuego);
        $stmtMedia->execute();
        $resultMedia = $stmtMedia->get_result();
        $rowMedia = $resultMedia->fetch_assoc();
        $notaMedia = $rowMedia['notaMedia'];

        $sqlUpdate = "UPDATE juegos SET nota = ? WHERE idJuego = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param('di', $notaMedia, $idJuego);
        $stmtUpdate->execute();

        header("Location: pagina_juego.php?id=$idJuego");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $stmtMedia->close();
    $stmtUpdate->close();
}

$conn->close();
