<link rel="icon" type="image/png" href="favicon.png">
    <link href="css/all.min.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet"/>    
    <link href="css/layout.css" rel="stylesheet">
    <link href="css/carousel.css" rel="stylesheet">
    <link href="css/custom-buttons.css" rel="stylesheet">
    <link href="css/bootstrap-override.css" rel="stylesheet">
    <link href="css/unimania-theme.css" rel="stylesheet">

<nav class="navbar navbar-dark border-bottom navbar-unimania">
        <div class="container my-1">
            <div class="navbar-content row w-100 g-3 align-items-center">
                <div class="col-auto order-1 order-lg-1 d-flex justify-content-center justify-content-lg-start">
                    <a href="index.php" class="navbar-brand navbar-home-btn btn btn-warning text-uppercase fw-semibold">
                        Inicio
                    </a>
                </div>
                <div class="col-auto order-2 order-lg-3">
                    <div class="navbar-actions d-flex align-items-center gap-3 flex-lg-nowrap justify-content-center justify-content-lg-end">
                        <a href="checkout.php" class="btn btn-light btn-outline-primary btn-lg navbar-cart-btn">
                            <i class="fas fa-shopping-cart"></i>Carrito <span id="num_cart" class="badge bg-secondary"> <?php echo $num_cart; ?> </span>
                        </a>
                        <?php if (isset($_SESSION['user_id'])) { ?>
                            <div class="dropdown navbar-login">
                                <button class="btn btn-success dropdown-toggle" type="button" id="btn_session" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user"></i> &nbsp; <?php echo $_SESSION['user_name']; ?>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="btn_session">
                                    <li><a class="dropdown-item" href="compras.php">Mis compras</a></li>
                                    <li><a class="dropdown-item" href="logout.php">Cerrar sesión</a></li>
                                </ul>
                            </div>
                        <?php } else { ?>
                            <a href="login.php" class="btn btn-success navbar-login">
                                <i class="fas fa-user"></i> Ingresar
                            </a>
                        <?php } ?>
                    </div>
                    </div>
                <div class="col-12 col-lg order-3 order-lg-2">
                    <form method="get" action="index.php" autocomplete="off" class="navbar-search-form w-100">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Buscar..." aria-describedby="icon-buscar">
                            <button class="btn btn-search" type="submit" id="icon-buscar">
                                <i class="fas fa-search"></i>
                            </button>
                     </div>
                     </form>
                </div>
            </div>
        </div>
    </nav>