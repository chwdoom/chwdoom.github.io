<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
        <title>Inicio - Gametica</title>
        <style>
            body {
                background-image: url('imagenes/fondo2.avif');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
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

            h2{
                color: white;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="">
                    <img src="imagenes/Gametica.png" alt="Gametica" class="mx-5">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
                    <form class="d-flex" role="search" action="portfolio/busqueda.php" method="GET">
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
                                    require_once "portfolio/logueouser.php";
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
                    <a href="portfolio/login.php" class="d-inline-flex align-items-center text-black text-decoration-none">
                        <img src="imagenes/perfil.png" alt="Perfil" height="40px" class="mx-3">
                        <?php if(isset( $_SESSION['usuario'])) { 
                        echo $_SESSION['usuario'];
                        echo '<form action="portfolio/logout.php" method="POST"><button class="btn btn-danger mx-3">Cerrar Sesión</button></form>';
                    } else { echo "Iniciar Sesión"; } ?>
                    </a>
                </div>
            </div>
        </nav>
        
        <main class="container py-4">
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="imagenes/banner.jpg" class="d-block w-100" alt="banner1">
                    </div>
                    <div class="carousel-item">
                        <img src="imagenes/banner0.jpg" class="d-block w-100" alt="banner2">
                    </div>
                </div>
            </div>

            <section class="py-5">
                <div class="container">
                    <h2 class="text-center mb-4">Juegos Destacados</h2>
                    <div class="row row-cols-1 row-cols-md-3 g-4" id="juegosDestacados">

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
        <script>
            //Obtener datos sección Juegos Destacados
            document.addEventListener('DOMContentLoaded', function() {
                const juegosContainer = document.getElementById('juegosDestacados');
                fetch('portfolio/get_juegos.php')
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(juego => {
                            const juegoCard = `
                                <div class="col">
                                    <div class="card h-100">
                                        <img src="imagenes/juegos/portada${juego.idJuego}.webp" class="card-img-top" alt="Portada ${juego.nombre}" height="225px">
                                        <div class="card-body">
                                            <h5 class="card-title">${juego.nombre}</h5>
                                            <p class="card-text">${juego.descripcion}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                            juegosContainer.innerHTML += juegoCard;
                        });
                    })
                    .catch(error => console.error('Error:', error));
                }
            );
        </script>
    </body>
</html>
