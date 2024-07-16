<?php
/**
 * Pantalla principal para el registro de un nuevo cliente
 * Desarrollado por Carlos Andrés Romero - CARTECH
 * 2024
 */

require_once 'config/config.php';
require_once 'clases/clienteFunciones.php';
$db = new Database();
$con = $db->conectar();

$errors = [];

if (!empty($_POST)) {
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $nip = trim($_POST['nip']);
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if (esNulo([$nombres, $apellidos, $email, $telefono, $nip, $password, $repassword])) {
        $errors[] = "Debe llenar todos los campos.";
    }

    if (!esEmail($email)) {
        $errors[] = "La dirección de correo no es válida.";
    }

    if (!validaPassword($password, $repassword)) {
        $errors[] = "Las contraseñas no coinciden.";
    }

    if (usuarioExiste($usuario, $con)) {
        $errors[] = "El nombre de usuario $usuario ya existe.";
    }

    if (emailExiste($email, $con)) {
        $errors[] = "El correo electrónico $email ya existe.";
    }
    if (count($errors) == 0) {
        $id = registraCliente([$nombres, $apellidos, $email, $telefono, $nip], $con);
        if ($id > 0) {
            require_once 'clases/Mailer.php';
            $mailer = new Mailer();
            $token = generarToken();
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $idUsuario = registraUsuario([$usuario, $pass_hash, $token, $id], $con);
            if ($idUsuario > 0) {
                $url = BASE_URL . '/activa_cliente.php?id=' . $idUsuario . '&token=' . $token;
                /** http://localhost/lovemestore/activa_cliente.php?id=3&360217b4484e357399a583a96f8f85a4 */
                $asunto = "Activar cuenta - Tienda Online";
                $cuerpo = "Estimado $nombres: <br> Para continuar con el proceso de registro es necesario que de click en el siguiente enlace para <a href='$url'>Activar cuenta.</a>";
                if ($mailer->enviarEmail($email, $asunto, $cuerpo)) {
                    echo "Para terminar el proceso de registro, siga las instrucciones que le hemos enviado a la dirección de correo electrónico $email.";
                    exit;
                } else {
                    $errors[] = "Error al registrar usuario.";
                }
            } else {
                $errors[] = "Error al registrar cliente.";
            }
        }
    }
}

/**
 * phpinfo();
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
                <h2>Datos del cliente</h2>
                <?php mostrarMensajes($errors); ?>
                <form class="row g3" action="registro.php" method="post" autocomplete="off">
                    <div class="col-md-6">
                        <label for="nombres"><span class="text-danger">*</span> Nombres</label>
                        <input type="text" name="nombres" id="nombres" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="apellidos"><span class="text-danger">*</span> Apellidos</label>
                        <input type="text" name="apellidos" id="apellidos" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email"><span class="text-danger">*</span> Correo electrónico</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                        <span id="validaEmail" class="text-danger"></span>
                    </div>
                    <div class="col-md-6">
                        <label for="telefono"><span class="text-danger">*</span> Teléfono</label>
                        <input type="tel" name="telefono" id="telefono" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="nip"><span class="text-danger">*</span> NIP</label>
                        <input type="text" name="nip" id="nip" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="usuario"><span class="text-danger">*</span> Usuario</label>
                        <input type="text" name="usuario" id="usuario" class="form-control" required>
                        <span id="validaUsuario" class="text-danger"></span>
                    </div>
                    <div class="col-md-6">
                        <label for="password"><span class="text-danger">*</span> Contraseña</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="repassword"><span class="text-danger">*</span> Repetir contraseña</label>
                        <input type="password" name="repassword" id="repassword" class="form-control" required>
                    </div>
                    <i class="text-danger"><b>Nota:</b> Los campos con (*) son obligatorios</i>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <?php include 'layout/footer.php'; ?>
    <script>
    let txtUsuario = document.getElementById('usuario')
    txtUsuario.addEventListener("blur", function() {
        existeUsuario(txtUsuario.value)
    }, false)

    function existeUsuario(usuario) {
        let url = "clases/clienteAjax.php"
        let formData = new FormData()
        formData.append("action", "existeUsuario")
        formData.append("usuario", usuario)
        fetch(url, {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(data => {
                if (data.ok) {
                    document.getElementById('usuario').value = ''
                    document.getElementById('validaUsuario').innerHTML = 'Usuario no disponible.'
                } else {
                    document.getElementById('validaUsuario').innerHTML = ''
                }
            })
    }

    let txtEmail = document.getElementById('email')
    txtEmail.addEventListener("blur", function() {
        existeEmail(txtEmail.value)
    }, false)

    function existeEmail(email) {
        let url = "clases/clienteAjax.php"
        let formData = new FormData()
        formData.append("action", "existeEmail")
        formData.append("email", email)
        fetch(url, {
                method: 'POST',
                body: formData
            }).then(response => response.json())
            .then(data => {
                if (data.ok) {
                    document.getElementById('email').value = ''
                    document.getElementById('validaEmail').innerHTML = 'Email no disponible.'
                } else {
                    document.getElementById('validaEmail').innerHTML = ''
                }
            })
    }
    </script>