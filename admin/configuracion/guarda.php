<?php
require '../config/config.php';
include '../layout/header.php';
require '../clases/cifrado.php';

$db = new Database();
$con = $db->conectar();

$nombre = $_POST['nombre'];
$telefono = $_POST['telefono'];
$moneda = $_POST['moneda'];
$direccion = $_POST['direccion'];

$smtp = $_POST['smtp'];
$puerto = $_POST['puerto'];
$email = $_POST['email'];
$password = cifrar($_POST['password']);

$paypal_cliente = $_POST['paypal_cliente'];
$paypal_moneda = $_POST['paypal_moneda'];

$mp_token = $_POST['mp_token'];
$mp_clave = $_POST['mp_clave'];


// $passwordBd = '';
// $sqlConfig = $con->query("SELECT valor FROM configuracion WHERE nombre = 'correo_password");
// $sqlConfig->execute();
// if ($row_config = $sqlConfig->fetch(PDO::FETCH_ASSOC)) {
//     $passwordBd = $row_config['valor'];
// }

$sql = $con->prepare("UPDATE configuracion SET valor = ? WHERE nombre = ?");
$sql->execute([$nombre, 'tienda_nombre']);
$sql->execute([$telefono, 'tienda_telefono']);
$sql->execute([$moneda, 'tienda_moneda']);
$sql->execute([$direccion, 'tienda_direccion']);
$sql->execute([$smtp, 'correo_smtp']);
$sql->execute([$puerto, 'correo_puerto']);
$sql->execute([$email, 'correo_email']);
$sql->execute([$password, 'correo_password']);
$sql->execute([$paypal_cliente, 'paypal_cliente']);
$sql->execute([$paypal_moneda, 'paypal_moneda']);
$sql->execute([$mp_token, 'mp_token']);
$sql->execute([$mp_clave, 'mp_clave']);

// if ($passwordBd != $password) {
//     $password = cifrado($password, ['key' => 'ABC.1234-*', 'method' => 'aes-128-cbc']);
//     $sql->execute([$password, 'correo_password']);
// }

?>
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Configuración actualizada</h1>
        <ol class="breadcrumb mb-4">
            <a href="index.php" class="btn btn-secondary btn-sm">Regresar</a>
        </ol>
    </div>
</main>

<?php require_once '../layout/footer.php'; ?>