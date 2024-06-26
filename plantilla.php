<?php
/**
 * Plantilla principal
 * Desarrollado por Carlos Romero - CARTECH
 * 2024
 */

require_once 'config/config.php';
require_once 'clases/clienteFunciones.php';
$db = new Database();
$con = $db->conectar();

$errors = [];

if (!empty($_POST)) {
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $nip = trim($_POST['nip']);
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if (esNulo([$nombres, $apellidos, $email, $telefono, $nip, $password, $repassword])) {
        $errors[] = "Debe llenar todos los campos";
    }

    if (!esEmail($email)) {
        $errors[] = "La dirección de correo no es válida";
    }

    if (!validaPassword($password, $repassword)) {
        $errors[] = "Las contraseñas no coinciden";
    }

    if (usuarioExiste($usuario, $con)) {
        $errors[] = "El nombre de usuario $usuario ya existe";
    }

    if (emailExiste($email, $con)) {
        $errors[] = "El correo electrónico $email ya existe";
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
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="d-flex flex-column h-100">
    <?php include 'layout/menu.php'; ?>

    <!-- Contenido -->

    <main class="flex-shrink-0">
        <div class="album py-5 bg-body-tertiary flex-shrink-0">
            <div class="container">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                    <div id="carouselId" class="carousel slide" data-bs-ride="carousel">
                        <ol class="carousel-indicators">
                            <li data-bs-target="#carouselId" data-bs-slide-to="0" class="active" aria-current="true"
                                aria-label="First slide"></li>
                            <li data-bs-target="#carouselId" data-bs-slide-to="1" aria-label="Second slide"></li>
                            <li data-bs-target="#carouselId" data-bs-slide-to="2" aria-label="Third slide"></li>
                        </ol>
                        <div class="carousel-inner" role="listbox">
                            <div class="carousel-item active">
                                <img src="holder.js/900x500/auto/#777:#555/text:First slide" class="w-100 d-block"
                                    alt="First slide" />
                            </div>
                            <div class="carousel-item">
                                <img src="holder.js/900x500/auto/#666:#444/text:Second slide" class="w-100 d-block"
                                    alt="Second slide" />
                            </div>
                            <div class="carousel-item">
                                <img src="holder.js/900x500/auto/#666:#444/text:Third slide" class="w-100 d-block"
                                    alt="Third slide" />
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselId"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <section>

    </section>
    <?php include 'layout/footer.php'; ?>