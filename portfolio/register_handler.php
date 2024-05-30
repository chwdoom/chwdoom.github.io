<?php
require_once 'logueouser.php';
session_start();
$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error) {
    die(json_encode(array("status" => "error", "message" => "Error de conexión: " . $conn->connect_error)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el usuario ya existe
    $sql = "SELECT * FROM usuarios WHERE login = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die(json_encode(array("status" => "error", "message" => "Error en la preparación de la consulta: " . $conn->error)));
    }
    $stmt->bind_param("ss", $nombre, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(array("status" => "error", "message" => "El nombre de usuario o el email ya está en uso."));
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios (tipo, login, email, password) VALUES ('usuario', ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nombre, $email, $hashedPassword);
        
        if ($stmt->execute() === TRUE) {
            $_SESSION['usuario'] = $nombre;
            $_SESSION['idusuario'] = $stmt->insert_id; // Obtener el ID del nuevo usuario
            $_SESSION['rol'] = 'usuario';
            echo json_encode(array("status" => "success", "message" => "Registro exitoso. La sesión se ha iniciado.", "usuario" => $nombre));
        } else {
            echo json_encode(array("status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error));
        }
    }
    $stmt->close();
}

$conn->close();
