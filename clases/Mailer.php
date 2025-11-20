<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    private string $ultimoError = '';

    public function enviarEmail($email, $asunto, $cuerpo)
    {
        require_once __DIR__ . '/../config/config.php';
        require __DIR__ . '/../phpmailer/src/PHPMailer.php';
        require __DIR__ . '/../phpmailer/src/SMTP.php';
        require __DIR__ . '/../phpmailer/src/Exception.php';

        $mail = new PHPMailer(true);
        $this->ultimoError = '';

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF; //Enable verbose debug output
            $mail->isSMTP();
            $mail->Host = MAIL_HOST;                     //Configure el servidor SMTP para enviar
            $mail->SMTPAuth = true;                      // Habilita la autenticación SMTP
            $mail->Username = MAIL_USER;                 //Usuario SMTP
            $mail->Password = MAIL_PASS;                 //Contraseña SMTP
            $mail->CharSet = 'UTF-8';

            $mail->SMTPSecure = (MAIL_SECURE === 'ssl' || MAIL_SECURE === 'smtps')
                ? PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer::ENCRYPTION_STARTTLS; //Habilitar el cifrado TLS
            $mail->Port = MAIL_PORT;                     //Puerto TCP al que conectarse, si usa 587 agregar `SMTPSecure = PHPMailer :: ENCRYPTION_STARTTLS`

            // Permitir certificados autofirmados en entornos de prueba
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];

            //Correo emisor y nombre
            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            //Correo receptor y nombre
            $mail->addAddress($email);

            //Contenido
            $mail->isHTML(true);   //Establecer el formato de correo electrónico en HTML
            $mail->Subject = $asunto; //Titulo del correo

            //Cuerpo del correo
            $mail->Body = $cuerpo;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], PHP_EOL, $cuerpo));
            //Enviar correo
            return $mail->send();
        } catch (Exception $e) {
            $this->ultimoError = !empty($mail->ErrorInfo) ? $mail->ErrorInfo : $e->getMessage();
            error_log('No se pudo enviar el mensaje: ' . $this->ultimoError);
            return false;
        }
    }

    public function obtenerError(): string
    {
        return $this->ultimoError;
    }
}
