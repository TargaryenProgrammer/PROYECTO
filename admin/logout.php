<?php
/**
 * Script de cierre de la sesión
 * Autor: Carlos Andrés Romero
 * 
 */
require_once 'config/config.php';
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_cliente']);
header("Location: index.php");