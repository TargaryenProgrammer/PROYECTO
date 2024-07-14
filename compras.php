<?php
/**
 * Script para mostrar las compras de cada cliente
 * Desarrollado por Carlos Romero - CARTECH
 * 2024
 */

require_once 'config/config.php';
require_once 'clases/clienteFunciones.php';
$db = new Database();
$con = $db->conectar();

$token = generarToken();
$_SESSION['token'] = $token;
$idCliente = $_SESSION['user_cliente'];
$sql = $con->prepare("SELECT id_transaccion, fecha, status, total, medio_pago FROM compra WHERE id_cliente = ? ORDER BY DATE(fecha) DESC");
$sql->execute([$idCliente]);

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
                <h4>Mis compras</h4>
                <hr>
                <?php while ($row = $sql->fetch(PDO::FETCH_ASSOC)) { ?>
                <div class="card mb-3 border-primary">
                    <div class="card-header bg-secondary text-white">
                        <?php echo $row['fecha']; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Folio: <?php echo $row['id_transaccion']; ?></h5>
                        <p class="card-text">Total: <?php echo MONEDA . '' . number_format($row['total'], '.', ','); ?>
                        </p>
                        <a href="compra_detalle.php?orden=<?php echo $row['id_transaccion']; ?>&token=<?php echo $token; ?>"
                            class="btn btn-primary btn-sm">Ver compra</a>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </main>
    <section>

    </section>
    <?php include 'layout/footer.php'; ?>