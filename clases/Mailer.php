<?php
/**
 * Script para el envio de email
 * Autor: Carlos Romero
 * 
 */
/** use PHPMailer\PHPMailer\{PHPMailer, SMTP, Exception}; */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    function enviarEmail($email, $asunto, $cuerpo)
    {
        require_once __DIR__ . '/../config/config.php';
        require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/../phpmailer/src/SMTP.php';
        require_once __DIR__ . '/../phpmailer/src/Exception.php';

        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;  /** SMTP::DEBUG_OFF */
            $mail->isSMTP();
            $mail->Host = MAIL_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = MAIL_USER;
            $mail->Password = MAIL_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = MAIL_PORT; /** use 587 `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS` */

            /** Correo emisor y nombre del emisor */
            $mail->setFrom(MAIL_USER, 'LoveMe Store');
            /**Correo receptor y nombre */
            $mail->addAddress($email);
            /** Contenido */
            $mail->isHTML(true);
            $mail->Subject = mb_convert_encoding($asunto, 'ISO-8859-1', 'UTF-8');
            /** Cuerpo del correo */
            $mail->Body = mb_convert_encoding($cuerpo, 'ISO-8859-1', 'UTF-8');
            $mail->setLanguage('es', __DIR__ . '/../phpmailer/language/phpmailer.lang-es.php');
            /** Enviar correo */
            if ($mail->send()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo "Error al enviar el correo electrónico: {$mail->ErrorInfo}";
            return false;
        }
    }
}