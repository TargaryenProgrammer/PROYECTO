<?php

/** 
 * Parámetros de configuración del sistema
 * Autor: Carlos Andrés Romero
 */

$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;

require_once $path . 'database.php';
require_once $path . '../admin/clases/cifrado.php';

$db = new Database();
$con = $db->conectar();

$sql = "SELECT nombre, valor FROM configuracion";
$resultado = $con->query($sql);
$datos = $resultado->fetchAll(PDO::FETCH_ASSOC);

$config = [];

foreach ($datos as $dato) {
    $config[$dato['nombre']] = $dato['valor'];
}

/** URL de la tienda */
define("BASE_URL", "http://localhost/lovemestore/");
/** Nombre de la tienda para el título */
define("TITLE", "Tienda Online");
/** Token de cifrado */
define("KEY_TOKEN", "APR.wcpq-345*");
/** Key de cifrado */
// define("KEY_CIFRADO", "ADASAD1*531-5S");
/** Metodo de cifrado */
define("METODO_CIFRADO", "AES-128-CBC");
/** Simbolo de moneda */
define("MONEDA", $config['tienda_moneda']);

/** Configuración PayPal */
define("CLIENT_ID", $config['paypal_cliente']);
define("CURRENCY", $config['paypal_moneda']);

/** Configuración Mercado Pago */
define("TOKEN_MP", $config['mp_token']);
define("PUBLIC_KEY", $config['mp_clave']);
define("LOCALE_MP", "es-CO");

/** Datos para el envio de correo electrónico */
define("MAIL_HOST", $config['correo_smtp']);
define("MAIL_USER", $config['correo_email']);
define("MAIL_PASS", descifrar($config['correo_password']));
// define("MAIL_PASS", descifrar($config['correo_password'], ['key' => KEY_CIFRADO, 'method' => METODO_CIFRADO]));
define("MAIL_PORT", $config['correo_puerto']);

// Destruir la variable
unset($config);

// Configuración para roles de session
// Sesión para la tienda
session_name('ecommerce_session');
session_start();

$num_cart = 0;
if (isset($_SESSION['carrito']['productos'])) {
    $num_cart = count($_SESSION['carrito']['productos']);
}