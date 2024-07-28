<?php
/** 
 * Parámetros de configuración del sistema
 * Autor: Carlos Andrés Romero
 * GitHub: https://github.com/KrlsRomero/
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
define("TITLE", "LoveMe Store");

session_name('admin-session');
session_start();