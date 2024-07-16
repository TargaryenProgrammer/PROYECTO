<?php
/**
 * Script para el envio de email
 * Autor: Carlos Romero
 * 
 */
/**
 * use PHPMailer\PHPMailer\PHPMailer;
 * use PHPMailer\PHPMailer\SMTP;
 * use PHPMailer\PHPMailer\Exception;
 */
use PHPMailer\PHPMailer\{PHPMailer, SMTP, Exception};

require_once '../phpmailer/src/PHPMailer.php';
require_once '../phpmailer/src/SMTP.php';
require_once '../phpmailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    /** Configuracion del servidor */
    $mail->SMTPDebug = SMTP::DEBUG_OFF;  /** SMTP::DEBUG_OFF */
    $mail->isSMTP();
    $mail->Host = MAIL_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = MAIL_USER;
    $mail->Password = MAIL_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465; /** use 587 `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS` */

    /** Correo y datos del emisor */
    $mail->setFrom('krlsrmro.683.ventas@gmail.com', 'LoveMe Store');
    $mail->addAddress('krlsrmro.683.contacto@gmail.com', 'Joe User');

    /** Contenido del correo */
    $mail->isHTML(true);
    $mail->Subject = $asunto;
    /** Cuerpo del correo */
    $cuerpo = '<h4>Gracias por su compra!</h4>';
    $cuerpo = '<p>El ID de su compra es <b>' . $id_transaccion . '</b></p>';
    $mail->Body = mb_convert_encoding($cuerpo, 'ISO-8859-1');
    $mail->AltBody = 'Le enviamos los detalles de su compra.';
    $mail->setLanguage('es', '../phpmailer/language/phpmailer.lang-es.php');
    /** Envio del correo */
    $mail->send();
} catch (Exception $e) {
    echo "Error al enviar el correo electrónico de la compra: {$mail->ErrorInfo}";
    return false;
}