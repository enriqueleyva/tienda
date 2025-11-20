<?php


require '../config/config.php';

$datos = ['ok' => false];

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($action === 'eliminar') {
        if ($id > 0 && isset($_SESSION['carrito']['productos'][$id])) {
            unset($_SESSION['carrito']['productos'][$id]);
            $datos['ok'] = true;
        }
    } elseif ($action === 'agregar') {
        $cantidad = isset($_POST['cantidad']) ? (int) $_POST['cantidad'] : 0;
        $cantidadAnterior = isset($_SESSION['carrito']['productos'][$id]) ? (int) $_SESSION['carrito']['productos'][$id] : 0;

        if ($id > 0 && $cantidad > 0) {
            $subtotal = agregar($id, $cantidad);

if ($subtotal > 0) {
                $_SESSION['carrito']['productos'][$id] = $cantidad;
                $datos['ok'] = true;
                $datos['sub'] = MONEDA . number_format($subtotal, 2, '.', ',');
            } else {
                $datos['cantidadAnterior'] = $cantidadAnterior;
            }
        }
    }
}

echo json_encode($datos);

function eliminar($id)
{
    if ($id > 0) {
        if (isset($_SESSION['carrito']['productos'][$id])) {
            unset($_SESSION['carrito']['productos'][$id]);
            return true;
        }
    } else {
        return false;
    }
}

function agregar($id, $cantidad)
{
    if ($id > 0 && $cantidad > 0 && is_numeric($cantidad) && isset($_SESSION['carrito']['productos'][$id])) {

        $db = new Database();
        $con = $db->conectar();
        $sql = $con->prepare("SELECT precio, descuento, stock FROM productos WHERE id=? AND activo=1");
        $sql->execute([$id]);
        $producto = $sql->fetch(PDO::FETCH_ASSOC);

        $descuento = $producto['descuento'];
        $precio = $producto['precio'];
        $stock = $producto['stock'];

        if ($stock >= $cantidad) {
            $precio_desc = $precio - (($precio * $descuento) / 100);
            return $cantidad * $precio_desc;
        }
    }
    return 0;
}
