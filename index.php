<?php

require 'config/config.php';

$db = new Database();
$con = $db->conectar();

$idCategoria = $_GET['cat'] ?? '';
$orden = $_GET['orden'] ?? '';
$buscar = $_GET['q'] ?? '';
$precioMin = $_GET['min'] ?? '';
$precioMax = $_GET['max'] ?? '';

if ($precioMin !== '' && !is_numeric($precioMin)) {
    $precioMin = '';
}

if ($precioMax !== '' && !is_numeric($precioMax)) {
    $precioMax = '';
}

$orders = [
    'asc' => 'nombre ASC',
    'desc' => 'nombre DESC',
    'precio_alto' => 'precio DESC',
    'precio_bajo' => 'precio ASC',
];

$order = $orders[$orden] ?? '';
$params = [];

$sql = "SELECT id, slug, nombre, precio FROM productos WHERE activo=1";

if (!empty($buscar)) {
    $sql .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
    $params[] = "%$buscar%";
    $params[] = "%$buscar%";
}

if (!empty($idCategoria)) {
    $sql .= " AND id_categoria = ?";
    $params[] = $idCategoria;
}

if ($precioMin !== '' && is_numeric($precioMin)) {
    $sql .= " AND precio >= ?";
    $params[] = $precioMin;
}

if ($precioMax !== '' && is_numeric($precioMax)) {
    $sql .= " AND precio <= ?";
    $params[] = $precioMax;
}

if (!empty($order)) {
    $sql .= " ORDER BY $order";
}

$query = $con->prepare($sql);
$query->execute($params);
$resultado = $query->fetchAll(PDO::FETCH_ASSOC);
$totalRegistros = count($resultado);

$categoriaSql = $con->prepare("SELECT id, nombre FROM categorias WHERE activo=1");
$categoriaSql->execute();
$categorias = $categoriaSql->fetchAll(PDO::FETCH_ASSOC);

$destacadosSql = $con->prepare("SELECT id, slug, nombre, precio FROM productos WHERE activo=1 ORDER BY id DESC LIMIT 4");
$destacadosSql->execute();
$destacados = $destacadosSql->fetchAll(PDO::FETCH_ASSOC);

$filterParams = [];
if (!empty($buscar)) {
    $filterParams['q'] = $buscar;
}
if (!empty($orden)) {
    $filterParams['orden'] = $orden;
}
if ($precioMin !== '') {
    $filterParams['min'] = $precioMin;
}
if ($precioMax !== '') {
    $filterParams['max'] = $precioMax;
}

$filterQueryString = '';
if (!empty($filterParams)) {
    $filterQueryString = '&' . http_build_query($filterParams);
}

?>
<!DOCTYPE html>
<html lang="es" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNIMANIA - Tienda No Oficial</title>
    
    <!-- Bootstrap 3 -->
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <style>
    .carousel-inner .item a {
        display: block;
        width: 100%;
        height: 100%;
    }
    .carousel-inner .item img {
        width: 100%;
        height: auto;
    }
    </style>
</head>

<body class="d-flex flex-column h-100">

    <?php include 'menu.php'; ?>

    <!-- Contenido principal -->
    <div class="main-content">
        
        <!--- CARRUSEL --->
        <div class="container">
            <br>
            <div id="theCarousel" class="carousel slide" data-ride="carousel">
                <!-- Indicadores -->
                <ol class="carousel-indicators">
                    <li data-target="#theCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#theCarousel" data-slide-to="1"></li>
                    <li data-target="#theCarousel" data-slide-to="2"></li>
                </ol>
                
                <div class="carousel-inner" role="listbox">
                
                <div class="item active">
                    <a href="acerca.php/">
                        <img src="/tienda/images/unimania_banner_ext.jpeg" alt="Banner informativo sobre UNIMANIA" class="img-responsive">
                    </a>
                    <div class="carousel-caption">
                        <h3>UNIMANIA</h3>
                        <p>¿Quienes somos?</p>
                    </div>
                </div>

                <div class="item">
                    <a href="index.php?cat=6/">
                        <img src="/tienda/images/unimania_banner_playeras.png" alt="Promoción de playeras UASLP" class="img-responsive">
                    </a>
                    <div class="carousel-caption">
                        <h3>Playeras UASLP</h3>
                        <p>Descubre nuestros modelos</p>
                    </div>
                </div>

                <div class="item">
                    <a href="details/peluiche-rei">
                        <img src="/tienda/images/unimania_banner_mascota.png" alt="Banner del peluche Rei" class="img-responsive">
                    </a>
                    <div class="carousel-caption">
                        <h3>Conoce a Rei</h3>
                        <p>Adquiere tu peluche</p>
                    </div>
                </div>

  
                </div>

                <a class="left carousel-control" href="#theCarousel" role="button" data-slide="prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                    <span class="sr-only">anterior</span>
                </a>
                <a class="right carousel-control" href="#theCarousel" role="button" data-slide="next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                    <span class="sr-only">siguiente</span>
                </a>
            </div>
        </div>
        <!--- FIN CARRUSEL --->

        <?php if (!empty($destacados)) { ?>
            <div class="container">
                <div class="row my-4">
                    <div class="col-12">
                        <h2 class="fw-bold">Productos destacados</h2>
                        <p class="text-muted">¡Los productos más vendidos en nuestras sucursales!</p>
                    </div>
                    <?php foreach ($destacados as $destacado) { ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 d-flex">
                            <div class="card w-100 my-2 shadow-2-strong">
                                <?php
                                $idDestacado = $destacado['id'];
                                $imagenDestacada = "images/productos/$idDestacado/principal.jpg";
                                if (!file_exists($imagenDestacada)) {
                                    $imagenDestacada = "images/no-photo.jpg";
                                }
                                ?>
                                <a href="details/<?php echo $destacado['slug']; ?>">
                                    <img src="<?php echo $imagenDestacada; ?>" class="img-thumbnail" style="max-height: 250px">
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="mb-1"><?php echo $destacado['nombre']; ?></h5>
                                    <p class="fw-bold"><?php echo MONEDA . ' ' . number_format($destacado['precio'], 2, '.', ','); ?></p>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a class="btn btn-success" onClick="addProducto(<?php echo $destacado['id']; ?>)">Agregar</a>
                                        <a href="details/<?php echo $destacado['slug']; ?>" class="btn btn-primary">Detalles</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <!-- CONTENIDO DE PRODUCTOS -->
        <div class="container p-3">
            <div class="row">
                <div class="col-12 col-md-3 col-lg-3">

                    <div class="card shadow-sm" style="margin-bottom: 15px;">
                        <div class="card-header">
                            Búsqueda y filtros
                        </div>
                        <div class="card-body">
                            <form action="index.php" method="get" class="form">
                                <div class="form-group">
                                    <label for="q">Buscar producto</label>
                                    <input type="text" class="form-control" id="q" name="q" placeholder="Nombre o descripción" value="<?php echo htmlspecialchars($buscar, ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="min">Precio mínimo</label>
                                    <input type="number" min="0" step="0.01" class="form-control" id="min" name="min" value="<?php echo htmlspecialchars($precioMin, ENT_QUOTES, 'UTF-8'); ?>" placeholder="0.00">
                                </div>
                                <div class="form-group">
                                    <label for="max">Precio máximo</label>
                                    <input type="number" min="0" step="0.01" class="form-control" id="max" name="max" value="<?php echo htmlspecialchars($precioMax, ENT_QUOTES, 'UTF-8'); ?>" placeholder="0.00">
                                </div>
                                <input type="hidden" name="cat" value="<?php echo htmlspecialchars($idCategoria, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="orden" value="<?php echo htmlspecialchars($orden, ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block">Aplicar filtros</button>
                                </div>
                                <div class="form-group">
                                    <a href="index.php" class="btn btn-link btn-block">Limpiar filtros</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header">
                            Categorías
                        </div>
                        <div class="list-group">
                            <?php $cleanQuery = ltrim($filterQueryString, '&'); ?>
                            <a href="index.php<?php echo !empty($cleanQuery) ? '?' . htmlspecialchars($cleanQuery, ENT_QUOTES, 'UTF-8') : ''; ?>" class="list-group-item list-group-item-action">TODO</a>
                            <?php foreach ($categorias as $categoria) { ?>
                                <a href="index.php?cat=<?php echo $categoria['id']; ?><?php echo htmlspecialchars($filterQueryString, ENT_QUOTES, 'UTF-8'); ?>" class="list-group-item list-group-item-action <?php echo ($categoria['id'] == $idCategoria) ? 'active' : ''; ?>">
                                <?php echo $categoria['nombre']; ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-md-9 col-lg-9">
                    <header class="d-sm-flex align-items-center border-bottom mb-4 pb-3">
                        <strong class="d-block py-2"><?php echo $totalRegistros; ?> artículos encontrados </strong>
                        <div class="ms-auto">
                            <form action="index.php" id="ordenForm" method="get" onchange="submitForm()">
                                <input type="hidden" id="cat" name="cat" value="<?php echo $idCategoria; ?>">
                                <input type="hidden" name="q" value="<?php echo htmlspecialchars($buscar, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="min" value="<?php echo htmlspecialchars($precioMin, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="max" value="<?php echo htmlspecialchars($precioMax, ENT_QUOTES, 'UTF-8'); ?>">
                                <label for="cbx-orden" class="form-label">Ordena por</label>
                                <select class="form-select d-inline-block w-auto pt-1 form-select-sm" name="orden" id="orden">
                                    <option value="precio_alto" <?php echo ($orden === 'precio_alto') ? 'selected' : ''; ?>>Precios más altos</option>
                                    <option value="precio_bajo" <?php echo ($orden === 'precio_bajo') ? 'selected' : ''; ?>>Precios más bajos</option>
                                    <option value="asc" <?php echo ($orden === 'asc') ? 'selected' : ''; ?>>Nombre A-Z</option>
                                    <option value="desc" <?php echo ($orden === 'desc') ? 'selected' : ''; ?>>Nombre Z-A</option>
                                </select>
                            </form>
                        </div>
                    </header>

                    <div class="row">
                        <?php foreach ($resultado as $row) { ?>
                            <div class="col-lg-4 col-md-6 col-sm-6 d-flex">
                                <div class="card w-100 my-2 shadow-2-strong"> 
                                    <?php
                                    $id = $row['id'];
                                    $imagen = "images/productos/$id/principal.jpg";
                                    if (!file_exists($imagen)) {
                                        $imagen = "images/no-photo.jpg";
                                    }
                                    ?>
                                    <a href="details/<?php echo $row['slug']; ?>">
                                        <img src="<?php echo $imagen; ?>" class="img-thumbnail" style="max-height: 300px" alt="Imagen del producto <?php echo htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </a>
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex flex-row">
                                            <h5 class="mb-1 me-1"><?php echo MONEDA . ' ' . number_format($row['precio'], 2, '.', ','); ?></h5>
                                        </div>
                                        <p class="card-text"><?php echo $row['nombre']; ?></p>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a class="btn btn-success" onClick="addProducto(<?php echo $row['id']; ?>)">Agregar</a>
                                            <div class="btn-group">
                                                <a href="details/<?php echo $row['slug']; ?>" class="btn btn-primary">Detalles</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        function addProducto(id) {
            var url = 'clases/carrito.php';
            var formData = new FormData();
            formData.append('id', id);

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors',
                }).then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        let elemento = document.getElementById("num_cart")
                        elemento.innerHTML = data.numero;
                        if (typeof showCartFeedback === 'function') {
                            showCartFeedback('Producto añadido al carrito');
                        }
                    } else {
                        alert("No hay suficientes productos en el stock")
                    }
                })
        }

        function submitForm() {
            document.getElementById("ordenForm").submit();
        }
        
        // Inicializar carrusel
        $(document).ready(function(){
            $('#theCarousel').carousel();
        });
    </script>
</body>
</html>