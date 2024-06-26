<?php
/**
 * Script de prueba de la pasarela de pago de Mercado Pago
 * Autor: Carlos Andrés Romero
 */

require_once 'vendor/autoload.php';

MercadoPago\SDK::setAccessToken("TEST-7999496333983920-042519-9226c42041f936e14ef01e85e07b44ab-1280778030");

$preference = new MercadoPago\Preference();

$item = new MercadoPago\Item();
$item->id = '0001';
$item->title = 'Producto Pasarela On Line';
$item->quantity = 1;
$item->unit_price = 150000;
$item->currency_id = "COP";

$preference->items = array($item);

$preference->back_urls = array(
    "success" => "http://http://pasarela.test/captura.php",
    "failure" => "http://http://pasarela.test/fallo.php",
);

$preference->auto_return = "approved";
$preference->binary_mode = true;

$preference->save();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>

<body>
    <h3>Mercado pago</h3>
    <div class="wallet_container"></div>
    <script>
        const mp = new MercadoPago('TEST-4e809d84-ab6a-4773-ae9a-33921d5588a3', {
            locale: 'es-CO'
        });

        mp.checkout({
            preference: {
                id: '<?php echo $preference->id; ?>'
            },
            render: {
                container: '.wallet_container',
                type: 'wallet',
                label: 'Pagar con Mercado Pago'
            }
        })
    </script>

</body>

</html>