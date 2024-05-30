<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>
        <?php 
            require_once 'logueouser.php';
            session_start();
            $conn = new mysqli($hn, $un, $pw, $db);

            if ($conn->connect_error) {
                die("Error de conexión: " . $conn->connect_error);
            }

            $id = $_GET['id'];

            $sql = "SELECT nombre FROM juegos WHERE idJuego = $id";
            $result = $conn->query($sql);
            if(!$result->num_rows > 0){
                header("Location: ../index.php");
            }
            $row = $result->fetch_assoc();
            echo $row['nombre'];
        ?> - Gametica</title>
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
        <section class="py-5">
            <div class="container">
                <?php
                    require_once 'logueouser.php';
                    $conn = new mysqli($hn, $un, $pw, $db);

                    if ($conn->connect_error) {
                        die("Error de conexión: " . $conn->connect_error);
                    }

                    if (isset($_GET['id'])) {
                        $idJuego = $_GET['id'];

                        // Consulta para obtener los detalles del juego
                        $sql = "SELECT j.idJuego, j.nombre, j.nota, j.descripcion,
                                    GROUP_CONCAT(DISTINCT p.nombre SEPARATOR ', ') as plataformas, 
                                    GROUP_CONCAT(DISTINCT g.nombre SEPARATOR ', ') as generos
                                FROM JUEGOS j
                                LEFT JOIN JUEGOS_PLATAFORMAS jp ON j.idJuego = jp.idJuego
                                LEFT JOIN PLATAFORMAS p ON jp.idPlataforma = p.idPlataforma
                                LEFT JOIN JUEGOS_GENEROS jg ON j.idJuego = jg.idJuego
                                LEFT JOIN GENEROS g ON jg.idGenero = g.idGenero
                                WHERE j.idJuego = ?
                                GROUP BY j.idJuego";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('i', $idJuego);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($row = $result->fetch_assoc()) {
                            $imagePath = '../imagenes/juegos/portada' . htmlspecialchars($row['idJuego']) . '.webp';
                            echo '<div class="row">';
                            echo '  <div class="col-md-6">';
                            echo '    <img src="' . $imagePath . '" class="img-fluid" alt="Portada del juego" style="border-radius: 2%">';
                            echo '  </div>';
                            echo '  <div class="col-md-6">';
                            echo '    <h1>' . htmlspecialchars($row['nombre']) . '</h1>';
                            echo '    <h1>Nota media: ' . htmlspecialchars($row['nota']) . '</h1>';
                            echo '    <p><strong>Descripción:</strong> ' . htmlspecialchars($row['descripcion']) . '</p>';
                            echo '    <p><strong>Plataformas:</strong> ' . htmlspecialchars($row['plataformas']) . '</p>';
                            echo '    <p><strong>Géneros:</strong> ' . htmlspecialchars($row['generos']) . '</p>';
                            if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'){
                                echo '<button class="btn btn-danger delete-game-button" data-game-id="' . htmlspecialchars($row['idJuego']) . '">Eliminar juego</button>';
                            }
                            echo '  </div>';
                            echo '</div>';
                        } else {
                            echo "<h2>Juego no encontrado</h2>";
                        }
                    } else {
                        echo "<h2>No se ha especificado ningún juego</h2>";
                    }
                ?>
            </div>
        </section>

        <article class="row">
            <!-- Sección de reseñas -->
            <section class="py-5 col-6">
                <div class="container">
                    <h2>Reseñas</h2>

                    <?php
                        if(isset($_SESSION['idusuario'])){
                            // Consulta para obtener las reseñas del juego
                            $sql_formulario = "SELECT *
                            FROM reseñas
                            WHERE idJuego = ? AND idUsuario = ?";
                            $stmt_formulario = $conn->prepare($sql_formulario);
                            $stmt_formulario->bind_param('ii', $idJuego, $_SESSION['idusuario']);
                            $stmt_formulario->execute();
                            $result_formulario = $stmt_formulario->get_result();

                            if (!$result_formulario->num_rows > 0){
                                echo 
                                '<div class="mb-4">
                                    <h3>Añadir Reseña</h3>
                                    <form action="agregar_resena.php" method="POST">
                                        <div class="mb-3">
                                            <label for="nota" class="form-label">Nota</label>
                                            <input type="number" class="form-control" id="nota" name="nota" required max="10" min="0">
                                        </div>
                                        <div class="mb-3">
                                            <label for="contenido" class="form-label">Contenido</label>
                                            <textarea class="form-control" id="contenido" name="contenido" rows="5" required></textarea>
                                        </div>
                                        <input type="hidden" name="idJuego" value="' . htmlspecialchars($idJuego) . '">
                                        <button type="submit" class="btn btn-primary">Añadir Reseña</button>
                                    </form>
                                </div>';
                            }
                        } else {
                            echo 
                            '<div class="mb-4">
                                <h3>Añadir Reseña</h3>
                                <form action="agregar_resena.php" method="POST">
                                    <div class="mb-3">
                                        <label for="nota" class="form-label">Nota</label>
                                        <input type="number" class="form-control" id="nota" name="nota" required max="10" min="0">
                                    </div>
                                    <div class="mb-3">
                                        <label for="contenido" class="form-label">Contenido</label>
                                        <textarea class="form-control" id="contenido" name="contenido" rows="5" required></textarea>
                                    </div>
                                    <input type="hidden" name="idJuego" value="' . htmlspecialchars($idJuego) . '">
                                    <button type="submit" class="btn btn-primary">Añadir Reseña</button>
                                </form>
                            </div>';
                        }
                        
                    ?>

                    <!-- Mostrar reseñas existentes -->
                    <div class="row">
                        <?php
                            // Consulta para obtener las reseñas del juego
                            $sql_resena = "SELECT r.idReseña, r.nota, r.texto, u.login, u.idUsuario
                            FROM reseñas r
                            JOIN usuarios u ON r.idUsuario = u.idUsuario
                            WHERE idJuego = ?";
                            $stmt_resena = $conn->prepare($sql_resena);
                            $stmt_resena->bind_param('i', $idJuego);
                            $stmt_resena->execute();
                            $result_resena = $stmt_resena->get_result();

                            if ($result_resena->num_rows > 0) {
                                while ($resena = $result_resena->fetch_assoc()) {
                                    echo '<div class="col-12 mb-3">';
                                    echo '  <div class="card bg-dark text-white">';
                                    echo '    <div class="card-body">';
                                    echo '      <h5 class="card-title">' . htmlspecialchars($resena['login']) . ' : ' . htmlspecialchars($resena['nota']) . ' / 10</h5>';
                                    echo '      <p class="card-text">' . nl2br(htmlspecialchars($resena['texto'])) . '</p>';
                                    if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'){
                                        echo '      <button class="btn btn-danger delete-review-button" data-review-id="' . htmlspecialchars($resena['idReseña']) . '">Eliminar reseña</button>';
                                        echo '      <button class="btn btn-danger delete-user-button float-end" data-user-id="' . htmlspecialchars($resena['idUsuario']) . '">Eliminar Usuario</button>';
                                    }
                                    echo '    </div>';
                                    echo '  </div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="col-12">';
                                echo '  <p>Aún no hay reseñas para este juego.</p>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                </div>
            </section>

            <!-- Sección de guías -->
            <section class="py-5 col-6">
                <div class="container">
                    <h2>Guías</h2>
                    
                    <!-- Formulario para añadir una nueva guía -->
                    <div class="mb-4">
                        <h3>Añadir Guía</h3>
                        <form action="agregar_guia.php" method="POST">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>
                            <div class="mb-3">
                                <label for="contenido" class="form-label">Contenido</label>
                                <textarea class="form-control" id="contenido" name="contenido" rows="5" required></textarea>
                            </div>
                            <input type="hidden" name="idJuego" value="<?php echo htmlspecialchars($idJuego); ?>">
                            <button type="submit" class="btn btn-primary">Añadir Guía</button>
                        </form>
                    </div>

                    <!-- Mostrar guías existentes -->
                    <div class="row">
                        <?php
                            // Consulta para obtener las guías del juego
                            $sql_guia = "SELECT g.idGuia, g.titulo, g.texto, u.login, u.idUsuario
                            FROM guias g
                            JOIN usuarios u ON g.idUsuario = u.idUsuario
                            WHERE idJuego = ?";
                            $stmt_guia = $conn->prepare($sql_guia);
                            $stmt_guia->bind_param('i', $idJuego);
                            $stmt_guia->execute();
                            $result_guia = $stmt_guia->get_result();

                            if ($result_guia->num_rows > 0) {
                                while ($guia = $result_guia->fetch_assoc()) {
                                    echo '<div class="col-12 mb-3">';
                                    echo '  <div class="card bg-dark text-white">';
                                    echo '    <div class="card-body">';
                                    echo '      <h5 class="card-title">' . htmlspecialchars($guia['login']) . ' : ' . htmlspecialchars($guia['titulo']) . '</h5>';
                                    echo '      <p class="card-text">' . nl2br(htmlspecialchars($guia['texto'])) . '</p>';
                                    if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'){
                                        echo '      <button class="btn btn-danger delete-guide-button" data-guide-id="' . htmlspecialchars($guia['idGuia']) . '">Eliminar guía</button>';
                                        echo '      <button class="btn btn-danger delete-user-button float-end" data-user-id="' . htmlspecialchars($guia['idUsuario']) . '">Eliminar Usuario</button>';
                                    }
                                    echo '    </div>';
                                    echo '  </div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<div class="col-12">';
                                echo '  <p>Aún no hay guías para este juego.</p>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                </div>
            </section>
        </article>

        <!-- Modal de confirmación para eliminar juegos -->
        <div class="modal fade" id="confirmDeleteGameModal" tabindex="-1" aria-labelledby="confirmDeleteGameModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content bg-dark text-white">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteGameModalLabel">Confirmar eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que quieres eliminar este juego?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteGameButton">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación para reseñas -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content bg-dark text-white">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que quieres eliminar esta reseña?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteButton">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación para guías -->
        <div class="modal fade" id="confirmDeleteGuideModal" tabindex="-1" aria-labelledby="confirmDeleteGuideModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content bg-dark text-white">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteGuideModalLabel">Confirmar eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que quieres eliminar esta guía?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteGuideButton">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación para eliminar usuarios -->
        <div class="modal fade" id="confirmDeleteUserModal" tabindex="-1" aria-labelledby="confirmDeleteUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content bg-dark text-white">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteUserModalLabel">Confirmar eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que quieres eliminar este usuario?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteUserButton">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
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
            var deleteReviewId = null;
            var deleteGuideId = null;
            var deleteUserId = null;
            var deleteGameId = null;

            // Obtener todos los botones de eliminar reseña
            var deleteReviewButtons = document.querySelectorAll('.delete-review-button');
            var deleteGuideButtons = document.querySelectorAll('.delete-guide-button');
            var deleteUserButtons = document.querySelectorAll('.delete-user-button');
            var deleteGameButton = document.querySelector('.delete-game-button');

            deleteReviewButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    deleteReviewId = this.getAttribute('data-review-id');
                    var myModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                    myModal.show();
                });
            });

            deleteGuideButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    deleteGuideId = this.getAttribute('data-guide-id');
                    var myModal = new bootstrap.Modal(document.getElementById('confirmDeleteGuideModal'));
                    myModal.show();
                });
            });

            deleteUserButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    deleteUserId = this.getAttribute('data-user-id');
                    var myModal = new bootstrap.Modal(document.getElementById('confirmDeleteUserModal'));
                    myModal.show();
                });
            });

            if (deleteGameButton) {
                deleteGameButton.addEventListener('click', function () {
                    deleteGameId = this.getAttribute('data-game-id');
                    var myModal = new bootstrap.Modal(document.getElementById('confirmDeleteGameModal'));
                    myModal.show();
                });
            }

            // Confirmar eliminación de reseña
            document.getElementById('confirmDeleteButton').addEventListener('click', function () {
                if (deleteReviewId) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'eliminar_resena.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            location.reload();
                        } else {
                            alert('Error al eliminar la reseña.');
                        }
                    };
                    xhr.onerror = function () {
                        alert('Error en la solicitud AJAX.');
                    };
                    xhr.send('id=' + encodeURIComponent(deleteReviewId));
                } else {
                    alert('ID de reseña no definido.');
                }
            });

            // Confirmar eliminación de guía
            document.getElementById('confirmDeleteGuideButton').addEventListener('click', function () {
                if (deleteGuideId) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'eliminar_guia.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            location.reload();
                        } else {
                            alert('Error al eliminar la guía.');
                        }
                    };
                    xhr.onerror = function () {
                        alert('Error en la solicitud AJAX.');
                    };
                    xhr.send('id=' + encodeURIComponent(deleteGuideId));
                } else {
                    alert('ID de guía no definido.');
                }
            });

            // Confirmar eliminación de usuario
            document.getElementById('confirmDeleteUserButton').addEventListener('click', function () {
                if (deleteUserId) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'eliminar_usuario.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            location.reload();
                        } else {
                            alert('Error al eliminar el usuario.');
                        }
                    };
                    xhr.onerror = function () {
                        alert('Error en la solicitud AJAX.');
                    };
                    xhr.send('id=' + encodeURIComponent(deleteUserId));
                } else {
                    alert('ID de usuario no definido.');
                }
            });

            // Confirmar eliminación de juego
            document.getElementById('confirmDeleteGameButton').addEventListener('click', function () {
                if (deleteGameId) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'eliminar_juego.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            window.location.href = '../index.php';
                        } else {
                            alert('Error al eliminar el juego.');
                        }
                    };
                    xhr.onerror = function () {
                        alert('Error en la solicitud AJAX.');
                    };
                    xhr.send('id=' + encodeURIComponent(deleteGameId));
                } else {
                    alert('ID de juego no definido.');
                }
            });
        });
    </script>
</body>
</html>
