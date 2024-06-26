<header data-bs-theme="dark">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a href="<?= BASE_URL; ?>" class="navbar-brand">
                <strong><?= TITLE; ?></strong>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader"
                aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarHeader">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="catalogo.php" class="nav-link active">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Vestidos</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Camisas</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Blusas</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">Contacto</a>
                    </li>
                </ul>
                <a href="checkout.php" class="btn btn-sm btn-primary me-2">
                    <i class="fa-solid fa-cart-plus"></i> <span id="num_cart"
                        class="badge bg-secondary"><?php echo $num_cart; ?></span> Carrito
                </a>
                <?php if (isset($_SESSION['user_id'])) { ?>
                <div class="dropdown">
                    <button class="btn btn-sm btn-success dropdown-toggle" type="button" id="btn_session"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-user"></i> &nbsp;
                        <?php echo $_SESSION['user_name']; ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btn_session">
                        <a class="dropdown-item" href="compras.php"><i class="fa-solid fa-receipt"></i>
                            Mis compras</a>
                        <a class="dropdown-item" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i>
                            Cerrar sesión</a>
                    </div>
                </div>
                <?php } else { ?>
                <a href="login.php" class="btn btn-sm btn-success"><i class="fas fa-user"></i> Ingresar</a>
                <?php } ?>
            </div>
        </div>
    </nav>
</header>
<!-- Contenido -->