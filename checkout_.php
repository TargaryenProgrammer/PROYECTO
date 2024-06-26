/** Implementación de prueba para las pasarelas de pago de PayPal */
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasarela de pago</title>
    <script
        src="https://www.paypal.com/sdk/js?client-id=Aa1Y8hbUvDzUQSMndPFFgRv7QoKNFUiZEAdSDYeceX2D_zVSe4XxSvYzMkrt-vnhmBsP8g4EumR3lh-k&currency=MXN">
    </script>
</head>

<body>
    <div id="paypal-button-container"></div>

    <script>
    paypal.Buttons({
        style: {
            color: 'gold',
            shape: 'pill',
            label: 'pay'
        },
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: 5000
                    }
                }]
            });
        },
        onAprove: function(data, actions) {
            actions.order.capture().then(function(detalles) {
                window.location.href = "competado.html"
            });
        },

        onCancel: function(data) {
            Swal.fire({
                title: 'Pago cancelado',
                text: 'El pago a sido cancelado, se redireccionará al inicio!',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
            console.log(data);
        }
    }).render('#paypal-button-container');
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>