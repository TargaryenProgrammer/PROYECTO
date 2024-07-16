<?php
/**
 * Pantalla principal para resetear la contraseña de clientes
 * Desarrollado por Carlos Andrés Romero - CARTECH
 * 2024
 */

require_once 'config/config.php';
require_once 'clases/clienteFunciones.php';

$user_id = $_GET['id'] ?? $_POST['user_id'] ?? '';
$token = $_GET['token'] ?? $_POST['token'] ?? '';
if ($user_id == '' || $token == '') {
    header("Location: index.php");
    exit;
}
$db = new Database();
$con = $db->conectar();

$errors = [];

if (!verificaTokenRequest($user_id, $token, $con)) {
    echo "No se pudo verificar la información.";
    exit;
}

if (!empty($_POST)) {
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if (esNulo([$user_id, $token, $password, $repassword])) {
        $errors[] = "Debe llenar todos los campos";
    }
    if (!validaPassword($password, $repassword)) {
        $errors[] = "Las contraseñas no coinciden";
    }

    if (count($errors) == 0) {
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        if (actualizaPassword($user_id, $pass_hash, $con)) {
            echo "Contraseña modificada.<br><a href='login.php'>Iniciar sesión</a>";
            exit;
        } else {
            $errors[] = "Error al modificar la contraseña. Intentalo nuevamente.";
        }
    }
}

?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <title><?= TITLE; ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <?php include 'layout/menu.php'; ?>
    <!-- Contenido -->
    <main class="mx-auto form-login pt-4" style="width: 350px;">
        <h3>Cambiar contraseña</h3>
        <?php mostrarMensajes($errors); ?>
        <form action="reset_password.php" method="post" class="row g-3" autocomplete="off">
            <input type="hidden" name="user_id" id="user_id" value="<?= $user_id; ?>" />
            <input type="hidden" name="token" id="token" value="<?= $token; ?>" />
            <div class="form-floating">
                <input class="form-control" type="password" name="password" id="password" placeholder="Nueva contraseña"
                    required>
                <label for="password">Nueva contraseña</label>
            </div>
            <div class="form-floating">
                <input class="form-control" type="password" name="repassword" id="repassword"
                    placeholder="Confirmar contraseña" required>
                <label for="repassword">Confirmar contraseña</label>
            </div>
            <div class="d-grid gap-3 col-12">
                <button type="submit" class="btn btn-primary">Continuar</button>
            </div>
            <hr>
            <div class="col-12">
                <a href="login.php">Iniciar sesión</a>
            </div>
        </form>
    </main>
    <?php include 'layout/footer.php'; ?>