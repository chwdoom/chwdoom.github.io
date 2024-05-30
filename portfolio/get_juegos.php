<?php
require_once 'logueouser.php';

// Crear conexión
$conn = new mysqli($hn, $un, $pw, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT idJuego, nombre, descripcion FROM juegos LIMIT 3";
$result = $conn->query($sql);

$juegos = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $juegos[] = $row;
    }
} else {
    echo "0 resultados";
}
$conn->close();

echo json_encode($juegos);
?>
