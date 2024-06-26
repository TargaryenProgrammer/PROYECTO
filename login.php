<?php
/**
 * Pantalla de login para clientes
 * Desarrollado por Carlos Romero - CARTECH
 * 2024
 */

require_once 'config/config.php';
require_once 'clases/clienteFunciones.php';
$db = new Database();
$con = $db->conectar();

$proceso = isset($_GET['pago']) ? 'pago' : 'login';

$errors = [];

if (!empty($_POST)) {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $proceso = $_POST['proceso'] ?? 'login';

    if (esNulo([$usuario, $password])) {
        $errors[] = "Debe llenar todos los campos";
    }
    if (count($errors) == 0) {
        $errors[] = login($usuario, $password, $con, $proceso);
    }
}

/**
 * phpinfo();
 * session_destroy();
 */
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <title><?= TITLE; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'layout/menu.php'; ?>
    <!-- Contenido -->
    <main class="mx-auto form-login pt-4" style="width: 350px;">
        <h2 class="text-center">Iniciar sesión</h2>
        <?php mostrarMensajes($errors); ?>
        <form class="row g-3" action="login.php" method="post" autocomplete="off">
            <input type="hidden" name="proceso" value="<?php echo $proceso; ?>" />
            <div class="form-floating">
                <input class="form-control" type="text" name="usuario" id="usuario" placeholder="Usuario" required>
                <label for="usuario">Usuario</label>
            </div>
            <div class="form-floating">
                <input class="form-control" type="password" name="password" id="password" placeholder="Contraseña"
                    required>
                <label for="contraseña">Contraseña</label>
            </div>
            <div class="col-12">
                <a href="recupera.php">¿Olvidaste tu contarseña?</a>
            </div>
            <div class="d-grid gap-3 col-12">
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </div>
            <hr>
            <div class="col-12">
                ¿No tiene una cuenta? <a href="registro.php">Registrate aquí!</a>
            </div>
        </form>
    </main>
    <?php include 'layout/footer.php'; ?>