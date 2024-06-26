<?php
/**
 * Script para la activación del rol clientes
 * Autor: Carlos Andrés Romero
 * 
 */

require_once 'config/config.php';
require_once 'clases/clienteFunciones.php';
$db = new Database();
$con = $db->conectar();
$id = isset($_GET['id']) ? $_GET['id'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';
if ($id == '' || $token == '') {
    header('Location: index.php');
    exit;
}

$db = new Database();
$con = $db->conectar();
echo validaToken($id, $token, $con);

$msg = "";
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
    <main>
        <div class="album py-5 bg-body-tertiary">
            <div class="container">
                <div class="card text-center">
                    <img class="card-img-top" src="#" alt="Cuenta activada" />
                    <div class="card-body">
                        <h4 class="card-title"><?php echo $msg; ?></h4>
                        <p class="card-text"><strong>Su cuenta ha sido activada correctamente! bienvenido.</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'layout/footer.php'; ?>