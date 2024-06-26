<?php
/**
 * Script para capturar los detalles de pago por medio de Paypal
 * Autor: Carlos Andrés Romero
 * 
 */
require '../config/config.php';
require '../config/database.php';

$db = new Database();
$con = $db->conectar();

$json = file_get_contents('php://input');
$datos = json_decode($json, true);

if (is_array($datos)) {
    $idCliente = $_SESSION['user_cliente'];
    $sqlProd = $con->prepare("SELECT email FROM clientes WHERE id = ? AND estatus = 1");
    $sqlProd->execute([$idCliente]);
    $row_cliente = $sqlProd->fetch(PDO::FETCH_ASSOC);

    $status = $datos['detalles']['status'];
    $fecha = $datos['detalles']['update_time'];
    $time = date("Y-m-d H:i:s", strtotime($fecha));
    $email = $row_cliente['email'];
    $monto = $datos['detalles']['purchase_units'][0]['amount']['value'];
    $idTransaccion = $datos['detalles']['purchase_units'][0]['payments']['captures'][0]['id'];

    // $email = $datos['details']['payer']['email_address'];
    // $idCliente = $datos['details']['payer']['payer_id']; 
    // $fecha_nueva = date('Y-m-d H:i:s', strtotime($fecha)); 
    // $idCliente = $datos['details']['payer']['payer_id'];

    $comando = $con->prepare("INSERT INTO compra(fecha, status, email, id_cliente, total, id_transaccion, medio_pago) VALUES(?,?,?,?,?,?,?)");
    $comando->execute([$time, $status, $email, $idCliente, $monto, $idTransaccion, 'Paypal']);
    $id = $con->lastInsertId();
    if ($id > 0) {
        $productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;
        if ($productos != null) {
            foreach ($productos as $clave => $cantidad) {
                $sqlProd = $con->prepare("SELECT id, nombre, precio, descuento FROM productos WHERE id = ? AND activo = 1");
                $sqlProd->execute([$clave]);
                $row_prod = $sqlProd->fetch(PDO::FETCH_ASSOC);
                $precio = $row_prod['precio'];
                $descuento = $row_prod['descuento'];
                $precio_desc = $precio - (($precio * $descuento) / 100);

                $sql_insert = $con->prepare("INSERT INTO detalle_compra(id_compra, id_producto, nombre, cantidad, precio) VALUES(?,?,?,?,?)");
                $sql_insert->execute([$id, $row_prod['id'], $row_prod['nombre'], $cantidad, $precio_desc]);
            }

            require 'Mailer.php';
            $asunto = "Detalles de su pedido";
            $cuerpo = "<h4>Gracias por su compra!</h4>";
            $cuerpo .= '<p>El ID de su compra es: <b>' . $idTransaccion . '</b></p>';

            $mailer = new Mailer();
            $mailer->enviarEmail($email, $asunto, $cuerpo);
        }
        unset($_SESSION['carrito']);
        header("Location: " . BASE_URL . "/completado.php?key=" . $idTransaccion);
    }
}