<?php
/**
 * Pantalla principal para recuperar la contraseña de clientes
 * Desarrollado por Carlos Andrés Romero - CARTECH
 * 2024
 */

require_once 'config/config.php';
require_once 'clases/clienteFunciones.php';
$db = new Database();
$con = $db->conectar();

$errors = [];

if (!empty($_POST)) {
    $email = trim($_POST['email']);

    if (esNulo([$email])) {
        $errors[] = "Debe llenar todos los campos";
    }

    if (!esEmail($email)) {
        $errors[] = "La dirección de correo no es válida";
    }
    if (count($errors) == 0) {
        if (emailExiste($email, $con)) {
            $sql = $con->prepare("SELECT usuarios.id, clientes.nombres FROM usuarios INNER JOIN clientes ON usuarios.id_cliente=clientes.id  WHERE clientes.email LIKE ? LIMIT 1");
            $sql->execute([$email]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $user_id = $row['id'];
            $nombres = $row['nombres'];
            $token = solicitaPassword($user_id, $con);
            if ($token !== null) {
                require 'clases/Mailer.php';
                $mailer = new Mailer();
                $url = BASE_URL . '/reset_password.php?id=' . $user_id . '&token=' . $token;
                $asunto = "Recuperar contraseña - LoveMe Store";
                $cuerpo = "Estiamdo $nombres: <br> Si has solicitado el cambio de tu contraseña da click en el siguiente link <a href='$url'>$url</a>.";
                $cuerpo .= "<br>Si no hiciste esta solicitud puedes ignorar este correo.";
                if ($mailer->enviarEmail($email, $asunto, $cuerpo)) {
                    echo "<p><strong>Correo enviado</strong></p>";
                    echo "<p>Hemos enviado un correo electrónico a la dirección $email para restablecer la contraseña.<br> Si no lo ves en recibidos! revisa en la carpeta de spam.</p>";
                    exit;
                }
            }
        } else {
            $errors[] = "No existe una cuenta asociada a esta dirección de correo.";
        }
    }
}

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
    <!-- Contenido -->
    <main class="mx-auto form-login pt-4" style="width: 350px;">
        <h3>Recuperar contraseña</h3>
        <?php mostrarMensajes($errors); ?>
        <form action="recupera.php" method="post" class="row g-3" autocomplete="off">
            <div class="form-floating">
                <input class="form-control" type="email" name="email" id="email" placeholder="Correo electrónico"
                    required>
                <label for="email">Correo electrónico</label>
            </div>
            <div class="d-grid gap-3 col-12">
                <button type="submit" class="btn btn-primary">Continuar</button>
            </div>
            <hr>
            <div class="col-12">
                ¿No tiene una cuenta? <a href="registro.php">Registrate aquí!</a>
            </div>
        </form>
    </main>
    <?php include 'layout/footer.php'; ?>