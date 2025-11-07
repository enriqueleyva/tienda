<?php
// --- CONFIGURACIÓN ---
$destinatario = "unimaniauaslp@gmail.com"; // Donde llegará el mensaje
$asunto = "Nuevo mensaje desde el formulario de contacto - UNIMANIA";

// --- OBTENER DATOS DEL FORMULARIO ---
$nombre  = trim($_POST['nombre'] ?? '');
$email   = trim($_POST['email'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

// --- VALIDAR ---
if ($nombre === '' || $email === '' || $mensaje === '') {
    echo "<script>alert('Por favor completa todos los campos.'); history.back();</script>";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('El correo electrónico no es válido.'); history.back();</script>";
    exit;
}

// --- CUERPO DEL MENSAJE ---
$cuerpo = "Has recibido un nuevo mensaje desde la página de contacto:\n\n";
$cuerpo .= "Nombre: $nombre\n";
$cuerpo .= "Email: $email\n\n";
$cuerpo .= "Mensaje:\n$mensaje\n";

// --- CABECERAS ---
$headers = "From: $nombre <$email>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// --- ENVIAR CORREO ---
if (mail($destinatario, $asunto, $cuerpo, $headers)) {
    echo "<script>alert('Tu mensaje ha sido enviado correctamente. ¡Gracias por contactarnos!'); window.location='contacto.php';</script>";
} else {
    echo "<script>alert('Ocurrió un error al enviar el mensaje. Intenta más tarde.'); history.back();</script>";
}
?>