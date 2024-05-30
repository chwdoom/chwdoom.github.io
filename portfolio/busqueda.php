<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Búsqueda - Gametica</title>
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
                        <?php
                            require_once "logueouser.php";
                            session_start();
                            $conn = new mysqli($hn, $un, $pw, $db);

                            if ($conn->connect_error) {
                                die("Error de conexión: " . $conn->connect_error);
                            }

                            $sql = "SELECT idPlataforma, nombre FROM plataformas";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row["idPlataforma"] . "'>" . $row["nombre"] . "</option>";
                                }
                                echo "</select>";
                            }
                        ?>
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
                    <h2 class="text-center mb-4">Resultados de búsqueda</h2>
                    <div class="row row-cols-1 row-cols-md-3 g-4" id="resultados">
                        <?php
                            require_once 'logueouser.php';
                            $conn = new mysqli($hn, $un, $pw, $db);

                            if ($conn->connect_error) {
                                die("Error de conexión: " . $conn->connect_error);
                            }

                            if ($_SERVER["REQUEST_METHOD"] == "GET"){
                                $nombre = isset($_GET['nombre']) ? $_GET['nombre'] : null;
                                $plataforma = isset($_GET['plataforma']) ? $_GET['plataforma'] : null;
                                $genero = isset($_GET['genero']) ? $_GET['genero'] : null;

                                $sql = "SELECT j.idJuego, j.nombre, j.descripcion, 
                                            GROUP_CONCAT(DISTINCT p.nombre SEPARATOR ', ') as plataformas, 
                                            GROUP_CONCAT(DISTINCT g.nombre SEPARATOR ', ') as generos 
                                        FROM JUEGOS j
                                        LEFT JOIN JUEGOS_PLATAFORMAS jp ON j.idJuego = jp.idJuego
                                        LEFT JOIN PLATAFORMAS p ON jp.idPlataforma = p.idPlataforma
                                        LEFT JOIN JUEGOS_GENEROS jg ON j.idJuego = jg.idJuego
                                        LEFT JOIN GENEROS g ON jg.idGenero = g.idGenero
                                        WHERE 1=1";
                                $params = [];
                                $types = '';

                                if (!empty($nombre)) {
                                    $sql .= " AND j.nombre LIKE ?";
                                    $params[] = '%' . $nombre . '%'; // Usar LIKE para búsqueda parcial
                                    $types .= 's';
                                }

                                if (!($plataforma == '')) {
                                    $sql .= " AND p.idPlataforma = ?";
                                    $params[] = $plataforma;
                                    $types .= 'i';
                                }

                                if (!($genero == '')) {
                                    $sql .= " AND g.idGenero = ?";
                                    $params[] = $genero;
                                    $types .= 'i';
                                }

                                $sql .= " GROUP BY j.idJuego";

                                // Preparar y ejecutar la consulta
                                $stmt = $conn->prepare($sql);

                                if ($params) {
                                    $stmt->bind_param($types, ...$params);
                                }

                                $stmt->execute();
                                $result = $stmt->get_result();

                                // Obtener los resultados
                                while ($row = $result->fetch_assoc()) {
                                    $imagePath = '../imagenes/juegos/portada' . htmlspecialchars($row['idJuego']) . '.webp';
                                    echo '<div class="col-md-12 mb-4">';
                                    echo '  <div class="card h-100 d-flex flex-row">';
                                    echo '    <img src="' . $imagePath . '" class="card-img-left" alt="Portada del juego" style="width: 300px; height: auto; border-radius: 2%">';
                                    echo '    <div class="card-body">';
                                    echo '      <h5 class="card-title">' . htmlspecialchars($row['nombre']) . '</h5>';
                                    echo '      <p class="card-text">Descripción: ' . htmlspecialchars($row['descripcion']) . '</p>';
                                    echo '      <a href="pagina_juego.php?id=' . htmlspecialchars($row['idJuego']) . '" class="btn btn-primary">Ver Juego</a>';
                                    echo '    </div>';
                                    echo '  </div>';
                                    echo '</div>';
                                }

                            } else {
                                echo "<h2>No has realizado ninguna búsqueda</h2>";
                            }
                        ?>
                    </div>
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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    </body>
</html>
