<!-- Menu de navegación -->
<header>
    
    <link rel="icon" type="image/png" href="favicon.png">
    <link href="css/all.min.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet"/>    
    <!-- Nuevos archivos CSS organizados -->
    <link href="css/layout.css" rel="stylesheet">
    <link href="css/carousel.css" rel="stylesheet">
    <link href="css/custom-buttons.css" rel="stylesheet">
    <link href="css/bootstrap-override.css" rel="stylesheet">
    <link href="css/unimania-theme.css" rel="stylesheet">

    <nav class="navbar navbar-expand-lg navbar-dark border-bottom navbar-unimania">
        <div class="container my-1" >

            <a href="index.php">
                <img src="images/unimania_logo_sf.png" alt="UNIMANIA" style="width:15%">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBarTop" aria-controls="navBarTop" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navBarTop">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Catálogo</a>
                    </li>
                </ul>

                <form method="get" action="index.php" autocomplete="off" class="navbar-search-form">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Buscar..." aria-describedby="icon-buscar">
                        <button class="btn btn-search" type="submit" id="icon-buscar">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <a href="checkout.php" class="btn btn-light btn-outline-primary me-2 btn-lg">
                    <i class="fas fa-shopping-cart"></i>Carrito <span id="num_cart" class="badge bg-secondary"> <?php echo $num_cart; ?> </span>
                </a>
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" id="btn_session" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> &nbsp; <?php echo $_SESSION['user_name']; ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="btn_session">
                            <li><a class="dropdown-item" href="compras.php">Mis compras</a></li>
                            <li><a class="dropdown-item" href="logout.php">Cerrar sesión</a></li>
                        </ul>
                    </div>
                <?php } else { ?>
                    <a href="login.php" class="btn btn-success">
                        <i class="fas fa-user"></i> Ingresar
                    </a>
                <?php } ?>
            </div>
        </div>
    </nav>
</header>