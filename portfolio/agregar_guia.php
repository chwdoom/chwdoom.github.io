<?php
require_once 'logueouser.php';
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
}
$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $idJuego = $_POST['idJuego'];

    $sql = "INSERT INTO guias (idJuego, idUsuario, texto, titulo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiss', $idJuego, $_SESSION['idusuario'], $contenido, $titulo);

    if ($stmt->execute()) {
        header("Location: pagina_juego.php?id=$idJuego");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
