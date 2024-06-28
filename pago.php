<?php

/**
 * Script para el procesamiento de pago
 * Autor: Carlos Andrés Romero
 */
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';


MercadoPago\SDK::setAccessToken(TOKEN_MP);

$preference = new MercadoPago\Preference();
$productos_mp = array();

$db = new Database();
$con = $db->conectar();

$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

// print_r($_SESSION);

$lista_carrito = array();

if ($productos != null) {
    foreach ($productos as $clave => $cantidad) {
        $sql = $con->prepare("SELECT id, nombre, precio, descuento, $cantidad AS cantidad FROM productos WHERE id = ? AND activo = 1");
        $sql->execute([$clave]);
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);
    }
} else {
    header("Location: index.php");
    exit;
}
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <title><?= TITLE; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href=" https://cdn.jsdelivr.net/npm/sweetalert2@11.10.8/dist/sweetalert2.min.css " rel="stylesheet">
    <!-- SDK PayPal -->
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo CLIENT_ID; ?>&currency=<?php echo CURRENCY; ?>">
    </script>

    <!-- SDK MercadoPago.js -->
    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>

<body>
    <?php include 'layout/menu.php'; ?>
    <!-- Contenido -->
    <main class="flex-shrink-0">
        <div class="album py-5 bg-body-tertiary">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-12">
                        <h4>Detalles de pago</h4>
                        <div class="row">
                            <div class="col-10">
                                <div id="paypal-button-container"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-10">
                                <div id="mp-button-container"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-12">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>SubTotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($lista_carrito == null) {
                                        echo '<tr><td colspan="5" class="text-center"><b>Tú carrito está vacio</b></td></tr>';
                                    } else {
                                        $total = 0;
                                        foreach ($lista_carrito as $producto) {
                                            $_id = $producto['id'];
                                            $nombre = $producto['nombre'];
                                            $descuento = $producto['descuento'];
                                            $precio = $producto['precio'];
                                            $cantidad = $producto['cantidad'];
                                            $precio_desc = $precio - (($precio * $descuento) / 100);
                                            $subtotal = $cantidad * $precio_desc;
                                            $total += $subtotal;

                                            $item = new MercadoPago\Item();
                                            $item->id = $_id;
                                            $item->title = $nombre;
                                            $item->quantity = $cantidad;
                                            $item->unit_price = $precio_desc;
                                            $item->currency_id = "COP";
                                            array_push($productos_mp, $item);
                                            unset($item);
                                            ?>
                                    <tr>
                                        <td><?php echo $nombre; ?></td>
                                        <td>
                                            <div id="subtotal_<?php echo $_id; ?>" name="subtotal[]">
                                                <?php echo MONEDA . number_format($subtotal, 2, '.', ','); ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td colspan="2">
                                            <p class="h3 text-end" id="total">
                                                <?php echo MONEDA . number_format($total, 2, '.', ','); ?>
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php
    $preference->items = $productos_mp;
    $preference->back_urls = array(
        "success" => BASE_URL . "/clases/captura.php",
        "failure" => BASE_URL . "/fallo.php",
    );

    $preference->auto_return = "approved";
    $preference->binary_mode = true;
    $preference->save();
    ?>
    <script>
    paypal.Buttons({
        style: {
            color: 'blue',
            shape: 'pill',
            label: 'pay'
        },
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: <?php echo $total; ?>
                    },
                    description: 'Compra Tienda Online'
                }],
                aplication_context: {
                    shipping_preference: "NO_SHIPPING"
                }
            });
        },
        onApprove: function(data, actions) {
            let url = 'clases/captura.php'
            actions.order.capture().then(function(detalles) {
                console.log(detalles)
                let trans = detalles.purchase_units[0].payments.captures[0].id;
                return fetch(url, {
                    method: 'POST',
                    mode: 'cors',
                    headers: {
                        'content-type': 'application/json'
                    },
                    body: JSON.stringify({
                        detalles: detalles
                    })
                }).then(function(response) {
                    window.location.href = "completado.php?key=" + trans;
                })
            });
        },

        onCancel: function(data) {
            Swal.fire({
                position: 'top-center',
                title: 'Pago cancelado',
                text: 'El pago ha sido cancelado!',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
            ("Pago cancelado");
            console.log(data);
        }
    }).render('#paypal-button-container');

    //Mercado Pago
    const mp = new MercadoPago('TEST-f10feedf-6a6b-4826-9fe6-2111f9090927', {
        locale: 'es-CO'
    });

    mp.checkout({
        preference: {
            id: '<?php echo $preference->id; ?>',
        },
        render: {
            cantainer: '#mp-button-container',
            type: 'wallet',
            label: 'Pagar con Mercado Pago',
        }
    })
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.8/dist/sweetalert2.all.min.js"></script>
    <?php include 'layout/footer.php'; ?>