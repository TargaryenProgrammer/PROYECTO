<?php
/**
 * Script para la visualición de los metodos de pago
 * Autor: Carlos Andrés Romero
 */
require_once 'config/config.php';
$db = new Database();
$con = $db->conectar();

$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

$lista_carrito = array();

if ($productos != null) {
    foreach ($productos as $clave => $cantidad) {
        $sql = $con->prepare("SELECT id, nombre, precio, descuento, $cantidad AS cantidad FROM productos WHERE id = ? AND activo = 1");
        $sql->execute([$clave]);
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);
    }
}
/** 
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
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href=" https://cdn.jsdelivr.net/npm/sweetalert2@11.10.8/dist/sweetalert2.min.css " rel="stylesheet">
</head>

<body>
    <?php include 'layout/menu.php'; ?>
    <!-- Contenido -->
    <main>
        <div class="album py-5 bg-body-tertiary">
            <div class="container">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>SubTotal</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($lista_carrito == null) {
                                echo '<tr><td colspan="5" class="text-center"><b>Tú carrito está vacio, echa un vistazo en nuestro catálogo y te ayudamos a resolverlo!</b></td></tr>';
                            } else {
                                $total = 0;
                                foreach ($lista_carrito as $producto) {
                                    $_id = $producto['id'];
                                    $nombre = $producto['nombre'];
                                    $precio = $producto['precio'];
                                    $descuento = $producto['descuento'];
                                    $cantidad = $producto['cantidad'];
                                    $precio_desc = $precio - (($precio * $descuento) / 100);
                                    $subtotal = $cantidad * $precio_desc;
                                    $total += $subtotal;
                                    ?>
                            <tr>
                                <td><?php echo $nombre; ?></td>
                                <td><?php echo MONEDA . number_format($precio_desc, 2, '.', ','); ?></td>
                                <td>
                                    <input type="number" min="1" max="10" step="1" value="<?php echo $cantidad; ?>"
                                        size="4" id="cantidad_<?php echo $_id; ?>"
                                        onchange="actualizaCantidad(this.value, <?php echo $_id; ?>)">
                                </td>
                                <td>
                                    <div id="subtotal_<?php echo $_id; ?>" name="subtotal[]">
                                        <?php echo MONEDA . number_format($subtotal, 2, '.', ','); ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="#" id="eliminar" data-bs-id="<?php echo $_id; ?>" data-bs-toggle="modal"
                                        data-bs-target="#eliminaModal" class="btn btn-warning btn-sm"><i
                                            class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="3"></td>
                                <td colspan="2">
                                    <p class="h3" id="total"><?php echo MONEDA . number_format($total, 2, '.', ','); ?>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                        <?php } ?>
                    </table>
                </div>
                <!-- <div class="row justify-content-end" </div> -->
                <?php if ($lista_carrito != null) { ?>
                <div class="row">
                    <div class="col-md-5 offset-md-7 d-grid gap-2">
                        <?php if (isset($_SESSION['user_cliente'])) { ?>
                        <a href="pago.php" class="btn btn-lg btn-primary">Realizar pago</a>
                        <?php } else { ?>
                        <a href="login.php?pago" class="btn btn-lg btn-primary">Realizar pago</a>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <!-- Modal Body -->
    <div class="modal fade" id="eliminaModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminaModalLabel">
                        Alerta
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">¿Realmente desea eliminar el producto de la lista?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                    <button id="btn-elimina" type="button" class="btn btn-danger" onclick="eliminar()">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    let eliminaModal = document.getElementById('eliminaModal')
    eliminaModal.addEventListener('show.bs.modal', function(event) {
        let button = event.relatedTarget
        let id = button.getAttribute('data-bs-id')
        let buttonElimina = eliminaModal.querySelector('.modal-footer #btn-elimina')
        buttonElimina.value = id
    })

    function actualizaCantidad(cantidad, id) {
        let url = 'clases/actualizar_carrito.php'
        let formData = new FormData()
        formData.append('action', 'agregar')
        formData.append('id', id)
        formData.append('cantidad', cantidad)
        fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            }).then(response => response.json())
            .then(data => {
                if (data.ok) {
                    let divsubtotal = document.getElementById('subtotal_' + id)
                    divsubtotal.innerHTML = data.sub
                    let total = 0.00
                    let list = document.getElementsByName('subtotal[]')
                    for (let i = 0; i < list.length; i++) {
                        total += parseFloat(list[i].innerHTML.replace(/[<?php echo MONEDA ?>,]/g, ''));
                    }
                    total = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2
                    }).format(total)
                    document.getElementById('total').innerHTML = '<?php echo MONEDA; ?>' + total
                }
            })
    }

    function eliminar() {
        let botonElimina = document.getElementById('btn-elimina')
        let id = botonElimina.value

        let url = 'clases/actualizar_carrito.php'
        let formData = new FormData()
        formData.append('action', 'eliminar')
        formData.append('id', id)
        fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            }).then(response => response.json())
            .then(data => {
                if (data.ok) {
                    location.reload()
                }
            })
    }
    </script>

    <!-- <script>
    function eliminar() {
        $("tr td #delete").click(function(ev) {
            ev.preventDefault();
            var nombre = $(this).parents('tr').find('td:first').text();
            var id = $(this).attr('data-id');
            Swal.fire({
                title: '¿Realmente quieres eliminar el producto de ' + nombre + '?',
                text: 'El registro no será eliminado permanentemente!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.value) {
                    // ajax ...
                    $.ajax({
                        type: 'POST',
                        url: 'clases/carrito.php'
                        data: {
                            'id': id
                        },
                        success: function() {
                            Swal.fire({
                                title: 'Eliminado',
                                text: 'El registro ha sido eliminado satisfactoriamente',
                                icon: 'success',
                            })
                        }.statusCode: {
                            400: function() {

                            }
                        }
                    })
                    document.location.href = href;
                }
            })
        })

    }
    </script> -->
    <?php include 'layout/footer.php'; ?>