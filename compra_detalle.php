<?php
/**
 * Script para visualizar los detalles de las compras del cliente
 * Desarrollado por Carlos Romero - CARTECH - 2024
 * GitHub: https://github.com/KrlsRomero/
 */

require_once 'config/config.php';
require_once 'clases/clienteFunciones.php';

$token_session = $_SESSION['token'];
$orden = $_GET['orden'] ?? null;
$token = $_GET['token'] ?? null;

if ($orden == null || $token == null || $token != $token_session) {
    header("Location: compras.php");
    exit;
}
$db = new Database();
$con = $db->conectar();

$sqlCompra = $con->prepare("SELECT id, id_transaccion, fecha, total FROM compra WHERE id_transaccion = ? LIMIT 1");
$sqlCompra->execute([$orden]);
$rowCompra = $sqlCompra->fetch(PDO::FETCH_ASSOC);
$idCompra = $rowCompra['id'];

$fecha = new DateTime($rowCompra['fecha']);
$fecha = $fecha->format('d/m/Y H:i:s');

$sqlDetalle = $con->prepare("SELECT id, nombre, precio, cantidad FROM detalle_compra WHERE id_compra = ?");
$sqlDetalle->execute([$idCompra]);

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
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="card mb-3 border-warning">
                            <div class="card-header">
                                <strong>Detalle de la compra</strong>
                            </div>
                            <div class="card-body">
                                <p><strong>Fecha: </strong> <?php echo $fecha; ?></p>
                                <p><strong>Orden: </strong> <?php echo $rowCompra['id_transaccion']; ?></p>
                                <p><strong>Total: </strong>
                                    <?php echo MONEDA . ' ' . number_format($rowCompra['total'], 2, '.', ','); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $sqlDetalle->fetch(PDO::FETCH_ASSOC)) {
                                        $precio = $row['precio'];
                                        $cantidad = $row['cantidad'];
                                        $subtotal = $precio * $cantidad;
                                        ?>
                                    <tr>
                                        <td><?php echo $row['nombre']; ?></td>
                                        <td><?php echo MONEDA . ' ' . number_format($precio, 2, '.', ','); ?></td>
                                        <td><?php echo $cantidad; ?></td>
                                        <td><?php echo MONEDA . ' ' . number_format($subtotal, 2, '.', '.'); ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include 'layout/footer.php'; ?>