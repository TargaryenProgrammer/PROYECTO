<?php

/** 
 * Parámetros de configuración del sistema
 * Autor: Carlos Andrés Romero
 */
$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$basePath = dirname($path, 2);

require $basePath . '/config/database.php';


/**
 * URL de la tienda
 * Nombre de la tienda para el título
 * Token de cifrado
 * Simbolo de moneda
 */

define("ADMIN_URL", "http://localhost/lovemestore/admin/");
define("TITLE", "Admin Tienda Online");
// define("BASE_URL", "http://localhost/lovemestore/");
// define("KEY_CIFRADO", "ABCD.1234-");
// define("METODO_CIFRADO", "aes-128-cbc");

/** Configuración PayPal 
 * 
 * 
 * define("CLIENT_ID", "Aa1Y8hbUvDzUQSMndPFFgRv7QoKNFUiZEAdSDYeceX2D_zVSe4XxSvYzMkrt-vnhmBsP8g4EumR3lh-k");
 * define("CURRENCY", "USD");
 * 
 */



/** Configuración Mercado Pago 
 * 
 * define("ACCESS_TOKEN", "TEST-7999496333983920-042519-9226c42041f936e14ef01e85e07b44ab-1280778030");
 * define("PUBLIC_KEY", "TEST-4e809d84-ab6a-4773-ae9a-33921d5588a3");
 * define("LOCALE_MP", "es-CO");
 * 
 * 
 */


/** Datos para el envio de correo electrónico */
// define("MAIL_HOST", $config['correo_smtp']);
// define("MAIL_USER", $config['correo_email']);
// define("MAIL_PASS", $config['correo_password']);
// define("MAIL_PORT", $config['correo_puerto']);

session_name('admin-session');
session_start();

// $num_cart = 0;
// if (isset($_SESSION['carrito']['productos'])) {
//     $num_cart = count($_SESSION['carrito']['productos']);
// }