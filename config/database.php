<?php
/**
 * Script de configuración y conexión a la base de datos
 * Autor: Carlos Andrés Romero
 * GitHub: https://github.com/KrlsRomero/
 */
class Database
{
    private $hostname = "localhost";
    private $database = "pasarela";
    private $username = "root";
    private $password = "";
    private $charset = "utf8";

    function conectar()
    {
        try {
            $conexion = "mysql:host=" . $this->hostname . "; dbname=" . $this->database . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            ];

            $pdo = new PDO($conexion, $this->username, $this->password, $options);
            return $pdo;
        } catch (PDOException $e) {
            echo 'Error de conexion: ' . $e->getMessage();
            exit;
        }
    }
}