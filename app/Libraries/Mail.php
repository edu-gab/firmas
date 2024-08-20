<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;
use Exception;

class Mail
{
    /**
     * Permite enviar un correo
     *
     * @param string $to_email Correo del destinatario
     * @param string $msg Cuerpo del correo
     * @param string $subject Asunto del correo
     * @param bool $isRequest Representa el estado del correo, si es una solicitud o es informativo
     * @return bool True si se envío y false si no se pudo enviar
     * @throws Exception
     */
    public function sendMail(string $to_email, string $msg, string $subject, bool $isRequest = false):bool
    {
        $email = new Email();

        # Seteamos las cabeceras
        $email->setHeader('MIME-Version', '1.0');
        $email->setHeader('Content-Type', 'text/html');

        # Seteamos el correo de aplicaciones
        $email->setFrom('aplicaciones@grupoberlin.com', 'Notificación Grupo Berlin');

        # Verificamos que el correo destinatario no sea nulo
        if(strlen($to_email) == 0) { throw new Exception("Debe existir un email destinatario"); }
        $email->setTo($to_email);

        # Seteamos los correos ocultos
        if($isRequest) {
            $email->setCC("andres.revelo@grupoberlin.com, mauricio.pico@grupoberlin.com");
            $email->setBCC("ronny.garcia@grupoberlin.com");
        } else {
            $email->setBCC("ronny.garcia@grupoberlin.com, andres.revelo@grupoberlin.com, mauricio.pico@grupoberlin.com, jose.suarez@grupoberlin.com");
        }

        # Seteamos el asunto del correo
        if(strlen($subject) == 0) { throw new Exception("Debe existir un asunto para el correo"); }
        $email->setSubject($subject);

        # Verificamos que el cuerpo del correo no esté vacío
        if(strlen($msg) == 0) { throw new Exception("Debe existir un cuerpo para el correo"); }
        $email->setMessage($msg);

        return $email->send();
    }
}