<?php
require 'config/config.php';
?>



<!DOCTYPE html>
<html lang="es" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="http://localhost/tienda/">
    <title>UNIMANIA - Contacto</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/all.min.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet"/>
</head>

<body class="d-flex flex-column h-100">

    <?php include 'menu.php'; ?>

    <!-- ======== SECCIÓN DE CONTACTO ======== -->
    <main class="flex-shrink-0">
        <section class="py-5 bg-light">
            <div class="container">
                <div class="text-center mb-5">
                    <h1 class="fw-bold">Contáctanos</h1>
                    <p class="text-muted">¿Tienes dudas, sugerencias o comentarios? ¡Nos encantaría saber de ti!</p>
                </div>

                <div class="row g-4">
                    <!-- Información de contacto -->
                    <div class="col-md-5">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h4 class="card-title mb-3">Información de contacto</h4>
                                <p><i class="fas fa-map-marker-alt me-2 text-primary"></i>Av. Dr. Salvador Nava Martínez s/n, Zona Universitaria, San Luis Potosí, S.L.P.</p>
                                <p><i class="fas fa-phone me-2 text-primary"></i><strong>Teléfono:</strong> (444) 826-2300</p>
                                <p><i class="fas fa-envelope me-2 text-primary"></i><strong>Email:</strong> <a href="mailto:unimaniauaslp@gmail.com">unimaniauaslp@gmail.com</a></p>
                                <hr>
                                <h5 class="mt-4">Horario de atención</h5>
                                <p>Lunes a Viernes: 8:00 a 18:00<br>Sábado y Domingo: Cerrado</p>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de contacto -->
                    <div class="col-md-7">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h4 class="card-title mb-3">Envíanos un mensaje</h4>
                                <form action="enviar_contacto.php" method="POST">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Correo electrónico</label>
                                        <input type="email" name="email" id="email" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="mensaje" class="form-label">Mensaje</label>
                                        <textarea name="mensaje" id="mensaje" rows="5" class="form-control" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Enviar mensaje</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mapa -->
                <div class="mt-5">
                    <h4 class="text-center mb-4">Ubicación</h4>
                    <div class="ratio ratio-16x9">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3733.3692858033865!2d-100.98637482481895!3d22.14251914794905!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x842aa218d4f9e38f%3A0x54f8b502a544ea29!2sUniversidad%20Aut%C3%B3noma%20de%20San%20Luis%20Potos%C3%AD!5e0!3m2!1ses-419!2smx!4v1692204988697!5m2!1ses-419!2smx" 
                            width="600" 
                            height="450" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- ======== FIN SECCIÓN CONTACTO ======== -->

    <?php include 'footer.php'; ?>

</body>
</html>
