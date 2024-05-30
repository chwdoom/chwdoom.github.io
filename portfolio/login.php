<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Iniciar Sesión - Gametica</title>
    <style>
        body {
            background-image: url('../imagenes/fondo2.avif');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: white;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        @media (max-width: 768px) {
            body {
                background-size: auto 100%;
                background-position: top;
            }
        }
        @media (max-width: 480px) {
            body {
                background-size: auto 100%;
                background-position: top;
            }
        }
        main {
            flex: 1;
        }
        footer {
            background-color: #111;
            color: white;
            padding: 20px 0;
        }
        footer a {
            color: #b3b3b3;
            text-decoration: none;
        }
        footer a:hover {
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">
                <img src="../imagenes/Gametica.png" alt="Gametica" class="mx-5">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
                <form class="d-flex" role="search" action="busqueda.php" method="GET">
                    <input class="form-control me-2" type="search" placeholder="Nombre" aria-label="Search" name="nombre">
                    <select class="form-select me-2" id="plataforma" name="plataforma">
                        <option value="">Plataforma</option>
                        <option value="PC">PC</option>
                        <option value="PlayStation">PlayStation</option>
                        <option value="Xbox">Xbox</option>
                        <option value="Nintendo">Nintendo</option>
                    </select>
                    <select class="form-select me-2" id="genero" name="genero">
                        <option value="">Género</option>
                        <?php
                            require_once "logueouser.php";
                            session_start();
                            $conn = new mysqli($hn, $un, $pw, $db);

                            if ($conn->connect_error) {
                                die("Error de conexión: " . $conn->connect_error);
                            }

                            $sql = "SELECT idGenero, nombre FROM generos";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row["idGenero"] . "'>" . $row["nombre"] . "</option>";
                                }
                                echo "</select>";
                            }
                        ?>
                    </select>
                    <button class="btn btn-success" type="submit" name="buscarJuego">Buscar</button>
                </form>
                <a href="login.php" class="d-inline-flex align-items-center text-black text-decoration-none">
                    <img src="../imagenes/perfil.png" alt="Perfil" height="40px" class="mx-3">
                    <?php if(isset( $_SESSION['usuario'])) { 
                        echo $_SESSION['usuario'];
                        echo '<form action="logout.php" method="POST"><button class="btn btn-danger mx-3">Cerrar Sesión</button></form>';
                    } else { echo "Iniciar Sesión"; } ?>
                </a>
            </div>
        </div>
    </nav>
    
    <main class="container py-4">
        <section class="row justify-content-center mt-5 py-5">
            <div class="col-6 form-container">
                <h2 class="text-center mb-4">Iniciar Sesión</h2>
                <form id="loginForm" method="POST">
                    <div class="mb-3">
                        <label for="loginNombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="loginNombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="loginPassword" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Iniciar Sesión</button>
                </form>
                <p class="mt-3 text-center">o</p>
                <a href="register.php" class="btn btn-primary w-100">Registrarse</a>
                <div id="loginFeedback" class="mt-3 text-center"></div>
            </div>
        </section>
    </main>

    <footer class="bg-dark text-white text-center py-4 mt-auto">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <p>Email: info@gametica.com</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p>Teléfono: +123 456 7890</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p class="mb-0">&copy; 2024 Gametica. Todos los derechos reservados.</p>
                    </div>
                </div>
            </div>
        </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('loginForm').addEventListener('submit', function (e) {
                e.preventDefault();

                var formData = new FormData(this);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'login_handler.php', true);
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        var feedback = document.getElementById('loginFeedback');

                        if (response.status === 'success') {
                            feedback.innerHTML = '<p style="color: lightgreen;">' + response.message + '</p>';
                            document.querySelector('.navbar-collapse .d-inline-flex').innerHTML = '<img src="../imagenes/perfil.png" alt="Perfil" height="40px" class="mx-3">' + response.usuario + '<form action="logout.php" method="POST" class="d-inline"><button type="submit" class="btn btn-danger mx-3">Cerrar Sesión</button></form>';
                        } else {
                            feedback.innerHTML = '<p style="color: red;">' + response.message + '</p>';
                        }
                    } else {
                        alert('Error en la solicitud AJAX.');
                    }
                };
                xhr.onerror = function () {
                    alert('Error en la solicitud AJAX.');
                };
                xhr.send(formData);
            });
        });
    </script>

</body>
</html>
