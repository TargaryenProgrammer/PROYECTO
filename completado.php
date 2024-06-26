<?php
/**
 * Script para mostrar un mensaje al momento de concluir la compra
 * Auto: Carlos Andrés Romero
 */
require_once 'config/config.php';
$db = new Database();
$con = $db->conectar();

$id_transaccion = isset($_GET['key']) ? $_GET['key'] : '0';

$error = '';
if ($id_transaccion == '') {
    $error = "Error al procesar la petición.";
} else {
    $sql = $con->prepare("SELECT count(id) FROM compra WHERE id_transaccion = ? AND status = ?");
    $sql->execute([$id_transaccion, 'COMPLETED']);
    if ($sql->fetchColumn() > 0) {
        $sql = $con->prepare("SELECT id, fecha, email, total FROM compra WHERE id_transaccion = ? AND status = ? LIMIT 1");
        $sql->execute([$id_transaccion, 'COMPLETED']);
        $row = $sql->fetch(PDO::FETCH_ASSOC);

        $idCompra = $row['id'];
        $total = $row['total'];
        $fecha = $row['fecha'];

        $sqlDet = $con->prepare("SELECT nombre, precio, cantidad FROM detalle_compra WHERE id_compra = ?");
        $sqlDet->execute([$idCompra]);
    } else {
        $error = "Error al comprobar la compra.";
    }
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= TITLE; ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <?php include 'layout/menu.php'; ?>

    <!-- Contenido -->
    <main>
        <div class="album py-5 bg-body-tertiary">
            <div class="container">
                <?php if (strlen($error) > 0) { ?>
                    <div class="row justify-content-center">
                        <div class="col-md-6 order-md-1">
                            <h3><?php echo $error; ?></h3>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row">
                        <div class="col">
                            <b>Folio de la compra: </b> <?php echo $id_transaccion; ?><br>
                            <b>Fecha de compra: </b> <?php echo $fecha; ?><br>
                            <b>Total: </b> <?php echo MONEDA . number_format($total, 2, '.', ','); ?><br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Cantidad</th>
                                        <th>Producto</th>
                                        <th class="text-center">Importe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row_det = $sqlDet->fetch(PDO::FETCH_ASSOC)) {
                                        $importe = $row_det['precio'] * $row_det['cantidad']; ?>
                                        <tr>
                                            <td><?php echo $row_det['cantidad']; ?></td>
                                            <td><?php echo $row_det['nombre']; ?></td>
                                            <td><?php echo $importe; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>
    <?php include 'layout/footer.php'; ?>