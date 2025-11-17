<?php

require '../config/config.php';
require '../clases/adminFunciones.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

$db = new Database();
$con = $db->conectar();

$id = validarNumero($_POST['id']);
$nombre = trim($_POST['nombre']);
$slug =  crearTituloURL($nombre);
$descripcion = $_POST['descripcion'];
$precio = validarNumero($_POST['precio'], true);
$descuento = validarNumero($_POST['descuento'], true);
$stock = validarNumero($_POST['stock']);
$categoria = validarNumero($_POST['categoria']);

$erroresValidacion = [];

if ($id === null) {
    $erroresValidacion[] = 'El identificador del producto no es válido.';
}

if ($precio === null) {
    $erroresValidacion[] = 'El precio debe ser numérico.';
}

if ($descuento === null) {
    $erroresValidacion[] = 'El descuento debe ser numérico.';
}

if ($stock === null) {
    $erroresValidacion[] = 'El stock debe ser un número entero.';
}

if ($categoria === null) {
    $erroresValidacion[] = 'Seleccione una categoría válida.';
}

if (empty($nombre)) {
    $erroresValidacion[] = 'El nombre es obligatorio.';
}

if (!empty($erroresValidacion)) {
    $_SESSION['error_validacion'] = implode(' ', $erroresValidacion);
    header('Location: edita.php?id=' . ($id ?? 0));
    exit;
}

$sql = "UPDATE productos SET slug=?, nombre=?, descripcion=?, precio=?, descuento=?, stock=?, id_categoria=? WHERE id = ?";
$stm = $con->prepare($sql);
if ($stm->execute([$slug, $nombre, $descripcion, $precio, $descuento, $stock, $categoria, $id])) {

    // Subir imagen principal
    if ($_FILES['imagen_principal']['error'] == UPLOAD_ERR_OK) {
        $dir = '../../images/productos/' . $id . '/';
        $permitidos = ['jpeg', 'jpg'];

        $arregloImagen = explode('.', $_FILES['imagen_principal']['name']);
        $extension = strtolower(end($arregloImagen));

        if (in_array($extension, $permitidos)) {
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $ruta_img = $dir . 'principal.' . $extension;
            if (move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $ruta_img)) {
                echo "El archivo se cargó correctamente.";
            } else {
                echo "Error al cargar el archivo.";
            }
        } else {
            echo "Archivo no permitido";
        }
    } else {
        echo "No eviaste archivo";
    }

    // Subir otras imagenes
    if (isset($_FILES['otras_imagenes'])) {
        $dir = '../../images/productos/' . $id . '/';
        $permitidos = ['jpeg', 'jpg'];

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        foreach ($_FILES['otras_imagenes']['tmp_name'] as $key => $tmp_name) {
            $fileName = $_FILES['otras_imagenes']['name'][$key];

            $arregloImagen = explode('.', $fileName);
            $extension = strtolower(end($arregloImagen));

            $nuevoNombre = $dir . uniqid() . '.' . $extension;

            if (in_array($extension, $permitidos)) {
                if (move_uploaded_file($tmp_name, $nuevoNombre)) {
                    echo "El archivo se cargó correctamente.";
                } else {
                    echo "Error al cargar el archivo.";
                }
            } else {
                echo "Archivo no permitido";
            }
        }
    }
}

header('Location: index.php');
