<?php
require_once 'logueouser.php';
session_start();
$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error) {
    die(json_encode(array("status" => "error", "message" => "Error de conexión: " . $conn->connect_error)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE login = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die(json_encode(array("status" => "error", "message" => "Error en la preparación de la consulta: " . $conn->error)));
    }
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['usuario'] = $nombre;
            $_SESSION['idusuario'] = $user['idUsuario'];
            $_SESSION['rol'] = $user['tipo'];
            echo json_encode(array("status" => "success", "message" => "Sesión iniciada correctamente", "usuario" => $nombre));
        } else {
            echo json_encode(array("status" => "error", "message" => "Nombre de usuario o contraseña incorrectos."));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Nombre de usuario o contraseña incorrectos."));
    }

    $stmt->close();
}

$conn->close();
