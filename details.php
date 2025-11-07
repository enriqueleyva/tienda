<?php

require 'config/config.php';

$db = new Database();
$con = $db->conectar();

$slug = $_GET['slug'] ?? '';

if ($slug == '') {
    echo 'Error al procesar la petición';
    exit;
}

// 📌 1. GUARDAR COMENTARIO EN BD
if (isset($_POST['enviar_comentario'])) {
    $usuario = $_POST['usuario'];
    $comentario = $_POST['comentario'];
    $estrellas = (int)$_POST['estrellas'];
    $id_producto = (int)$_POST['id_producto'];
    $id_usuario = 3;
    //$id_usuario = $_SESSION['id_usuario'] ?? 0; // si usas sesión para usuario

    $sql_insert = $con->prepare("INSERT INTO comentarios (usuario, comentario, estrellas, id_usuario, id_producto)
                                 VALUES (?, ?, ?, ?, ?)");
    $sql_insert->execute([$usuario, $comentario, $estrellas, $id_usuario, $id_producto]);
}

// 📌 2. OBTENER DETALLES DEL PRODUCTO
$sql = $con->prepare("SELECT count(id) FROM productos WHERE slug=? AND activo=1");
$sql->execute([$slug]);
if ($sql->fetchColumn() > 0) {

    $sql = $con->prepare("SELECT id, nombre, descripcion, precio, descuento FROM productos WHERE slug=? AND activo=1");
    $sql->execute([$slug]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $id = $row['id'];
    $descuento = $row['descuento'];
    $precio = $row['precio'];
    $precio_desc = $precio - (($precio * $descuento) / 100);
    $dir_images = 'images/productos/' . $id . '/';

    // 📌 3. OBTENER IMÁGENES
    $imagenes = array();
    if (is_dir($dir_images)) {
        $archivos = scandir($dir_images);
        foreach ($archivos as $archivo) {
            if ($archivo != '.' && $archivo != '..') {
                $ruta_relativa = $dir_images . $archivo;
                $ruta_absoluta = __DIR__ . '/' . $ruta_relativa;
                $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']) && is_file($ruta_absoluta)) {
                    $imagenes[] = $ruta_relativa;
                }
            }
        }

        usort($imagenes, function($a, $b) {
            if (strpos($a, 'principal.jpg') !== false) return -1;
            if (strpos($b, 'principal.jpg') !== false) return 1;
            return strcmp($a, $b);
        });
    }

    if (empty($imagenes)) {
        $imagenes[] = 'images/no-photo.jpg';
    }

    // 📌 4. OBTENER COMENTARIOS EXISTENTES
    $sql_coment = $con->prepare("SELECT usuario, comentario, estrellas 
                                FROM comentarios 
                                WHERE id_producto = ? 
                                ORDER BY id_comentario DESC");
    $sql_coment->execute([$id]);
    $comentarios = $sql_coment->fetchAll(PDO::FETCH_ASSOC);

} else {
    echo 'Producto no encontrado';
    exit;
}

?>
<!DOCTYPE html>
<html lang="es" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="http://localhost/tienda/">
    <title>UNIMANIA - Detalle de producto</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AfSeuCSA99CXfnkYqNeOpfvISgP8mBC0xAiSxNGJANCpwaFVwTzzaHQECo6WsGzlL7G4IVaFZSmwCI0o&currency=MXN"></script>
    <link rel="icon" type="image/png" href="favicon.png">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/all.min.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet"/>
    <style>
        .carousel-image-container {
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .carousel-image {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }
        .comentarios {
            margin-top: 30px;
        }
        .comentario {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .estrellas {
            color: gold;
            font-size: 1.1em;
        }
    </style>
</head>

<body class="d-flex flex-column h-100">

    <?php include 'menu.php'; ?>

    <!-- Contenido -->
    <main class="flex-shrink-0">
        <div class="container p-3">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="card shadow-sm">
                        <div class="row g-0">
                            <!-- Carrusel -->
                            <div class="col-md-6">
                                <div id="carouselImages" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                                    <div class="carousel-inner">
                                        <?php
                                        $first = true;
                                        foreach ($imagenes as $img) {
                                        ?>
                                            <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                                                <div class="carousel-image-container">
                                                    <img src="<?php echo $img; ?>" class="carousel-image rounded-start" alt="<?php echo $row['nombre']; ?>">
                                                </div>
                                            </div>
                                        <?php
                                            $first = false;
                                        }
                                        ?>
                                    </div>
                                    <?php if (count($imagenes) > 1): ?>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Anterior</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Siguiente</span>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Información del producto -->
                            <div class="col-md-6">
                                <div class="card-body">
                                    <h3 class="card-title mb-3"><?php echo $row['nombre']; ?></h3>

                                    <?php if ($descuento > 0) { ?>
                                        <p><del><?php echo MONEDA . ' ' . number_format($precio, 2, '.', ','); ?></del></p>
                                        <h4 class="text-success mb-3">
                                            <?php echo MONEDA . ' ' . number_format($precio_desc, 2, '.', ','); ?>
                                            <small>(<?php echo $descuento; ?>% off)</small>
                                        </h4>
                                    <?php } else { ?>
                                        <h4 class="mb-3"><?php echo MONEDA . ' ' . number_format($precio, 2, '.', ','); ?></h4>
                                    <?php } ?>

                                    <p class="card-text mb-4"><?php echo $row['descripcion']; ?></p>

                                    <div class="mb-3" style="max-width: 120px;">
                                        <input class="form-control" id="cantidad" name="cantidad" type="number" min="1" max="10" value="1">
                                    </div>
                                </div>

                                <div class="card-footer bg-transparent border-top">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-success flex-fill" type="button" onClick="comprarAhora(<?php echo $id; ?>, cantidad.value)">Comprar ahora</button>
                                        <button class="btn btn-outline-primary flex-fill" type="button" onClick="addProducto(<?php echo $id; ?>, cantidad.value)">Agregar al carrito</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 📌 FORMULARIO DE COMENTARIOS -->
                    <div class="comentarios">
                        <h4>Deja tu comentario</h4>
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="usuario" id="usuario" required>
                            </div>
                            <div class="mb-3">
                                <label for="estrellas" class="form-label">Calificación</label>
                                <select name="estrellas" id="estrellas" class="form-select" required>
                                    <option value="5">★★★★★</option>
                                    <option value="4">★★★★☆</option>
                                    <option value="3">★★★☆☆</option>
                                    <option value="2">★★☆☆☆</option>
                                    <option value="1">★☆☆☆☆</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="comentario" class="form-label">Comentario</label>
                                <textarea name="comentario" id="comentario" class="form-control" rows="3" required></textarea>
                            </div>
                            <input type="hidden" name="id_producto" value="<?php echo $id; ?>">
                            <button type="submit" name="enviar_comentario" class="btn btn-primary">Enviar</button>
                        </form>
                    </div>

                    <!-- 📌 LISTA DE COMENTARIOS -->
                    <div class="comentarios mt-4">
                        <h4>Comentarios</h4>
                        <?php if (count($comentarios) > 0): ?>
                            <?php foreach ($comentarios as $c): ?>
                                <div class="comentario">
                                    <strong><?php echo htmlspecialchars($c['usuario']); ?></strong>
                                    <div class="estrellas">
                                        <?php
                                        for ($i = 0; $i < $c['estrellas']; $i++) echo '★';
                                        for ($i = $c['estrellas']; $i < 5; $i++) echo '☆';
                                        ?>
                                    </div>
                                    <p><?php echo nl2br(htmlspecialchars($c['comentario'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No hay comentarios aún. ¡Sé el primero!</p>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="<?php echo SITE_URL; ?>js/bootstrap.bundle.min.js"></script>
    <script>
        function addProducto(id, cantidad) {
            var url = 'clases/carrito.php';
            var formData = new FormData();
            formData.append('id', id);
            formData.append('cantidad', cantidad);

            fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors',
            }).then(response => response.json())
            .then(data => {
                if (data.ok) {
                    document.getElementById("num_cart").innerHTML = data.numero;
                }
            })
        }

        function comprarAhora(id, cantidad) {
            var url = 'clases/carrito.php';
            var formData = new FormData();
            formData.append('id', id);
            formData.append('cantidad', cantidad);

            fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors',
            }).then(response => response.json())
            .then(data => {
                if (data.ok) {
                    document.getElementById("num_cart").innerHTML = data.numero;
                    location.href = 'checkout.php';
                }
            })
        }
    </script>
</body>
</html>
