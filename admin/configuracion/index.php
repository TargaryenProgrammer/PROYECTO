<?php
// ob_start();
require '../config/config.php';
require '../layout/header.php';
require '../clases/cifrado.php';

if (!isset($_SESSION['user_type'])) {
    header('Location: ../index.php');
    // echo '<script>window.location="' . ADMIN_URL . 'index.php"</script>';
    exit;
}

if ($_SESSION['user_type'] != 'admin') {
    header('Location: ../../index.php');
    // echo '<script>window.location="'.BASE_UL.'../../index.php"</script>';
    exit;
}

$db = new Database();
$con = $db->conectar();

$sql = "SELECT nombre, valor FROM configuracion";
$resultado = $con->query($sql);
$datos = $resultado->fetchAll(PDO::FETCH_ASSOC);

$config = [];

foreach ($datos as $dato) {
    $config[$dato['nombre']] = $dato['valor'];
}
// print_r($config);
?>

<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Configuración</h1>
        <form action="guarda.php" method="post">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                        type="button" role="tab" aria-controls="general" aria-selected="true">
                        General
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button"
                        role="tab" aria-controls="email" aria-selected="false">
                        Correo Electrónico
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="paypal-tab" data-bs-toggle="tab" data-bs-target="#paypal" type="button"
                        role="tab" aria-controls="paypal" aria-selected="false">
                        PayPal
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="mercado_pago-tab" data-bs-toggle="tab" data-bs-target="#mercado_pago"
                        type="button" role="tab" aria-controls="mercado_pago" aria-selected="false">
                        Mercado Pago
                    </button>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <div class="row">
                        <div class="col-6">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="nombre"
                                value="<?php echo $config['tienda_nombre']; ?>">
                        </div>
                        <div class=" col-6">
                            <label for="direccion">Dirección</label>
                            <input type="text" class="form-control" name="direccion" id="direccion"
                                value="<?php echo $config['tienda_direccion']; ?>">
                        </div>
                        <div class="col-6">
                            <label for="telefono">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" id="telefono"
                                value="<?php echo $config['tienda_telefono']; ?>">
                        </div>
                        <div class=" col-6">
                            <label for="moneda">Moneda</label>
                            <input type="text" class="form-control" name="moneda" id="moneda"
                                value="<?php echo $config['tienda_moneda']; ?>">
                        </div>
                    </div>
                </div>
                <!-- </?php echo descifrar($config['correo_password']); ?> -->
                <div class="tab-pane" id="email" role="tabpanel" aria-labelledby="email-tab">
                    <div class="row">
                        <div class=" col-6">
                            <label for="email">Correo Electrónico</label>
                            <input type="text" class="form-control" name="email" id="email"
                                value="<?php echo $config['correo_email']; ?>">
                        </div>
                        <div class=" col-6">
                            <label for="password">Contraseña</label>
                            <input type="password" class="form-control" name="password" id="password"
                                value="<?php echo $config['correo_password']; ?>">
                        </div>
                        <div class="col-6">
                            <label for="smtp">Smtp</label>
                            <input type="text" class="form-control" name="smtp" id="smtp"
                                value="<?php echo $config['correo_smtp']; ?>">
                        </div>
                        <div class="col-6">
                            <label for="puerto">Puerto</label>
                            <input type="text" class="form-control" name="puerto" id="puerto"
                                value="<?php echo $config['correo_puerto']; ?>">
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="paypal" role="tabpanel" aria-labelledby="paypal-tab">
                    <div class="row">
                        <div class="col-6">
                            <label for="paypal_cliente">Cliente Id</label>
                            <input type="text" class="form-control" name="paypal_cliente" id="paypal_cliente"
                                value="<?php echo $config['paypal_cliente']; ?>">
                        </div>
                        <div class="col-6">
                            <label for="paypal_moneda">Moneda</label>
                            <input type="text" class="form-control" name="paypal_moneda" id="paypal_moneda"
                                value="<?php echo $config['paypal_moneda']; ?>">
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="mercado_pago" role="tabpanel" aria-labelledby="mercado_pago-tab">
                    <div class="row">
                        <div class="col-6">
                            <label for="mp_token">Access Token</label>
                            <input type="text" class="form-control" name="mp_token" id="mp_token"
                                value="<?php echo $config['mp_token']; ?>">
                        </div>
                        <div class="col-6">
                            <label for="mp_clave">Public Key</label>
                            <input type="text" class="form-control" name="mp_clave" id="mp_clave"
                                value="<?php echo $config['mp_clave']; ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class=" row mt-4">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="../inicio.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</main>
<?php require_once '../layout/footer.php'; ?>