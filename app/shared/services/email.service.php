<?php

namespace App\Shared\Services;

use App\Shared\Http\HttpException;
use PHPMailer\PHPMailer\PHPMailer;
use App\Shared\Config\Env;
use Throwable;

require_once __DIR__ . '/../http/http-exception.php';

class EmailService
{
    public function send(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody,
        string $textBody,
        ?string $replyToEmail = null,
        ?string $replyToName = null
    ): void {
        if (!class_exists(PHPMailer::class)) {
            throw new HttpException('PHPMailer no esta instalado. Ejecuta composer install antes de enviar emails.', 500);
        }

        $host = Env::env('SMTP_HOST');
        $port = (int) Env::env('SMTP_PORT', '1025');
        $authEnvValue = Env::env('SMTP_AUTH', 'false');

        $auth = filter_var(
            $authEnvValue,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        if ($auth === null) {
            throw new HttpException('SMTP_AUTH debe ser un valor booleano valido.', 500);
        }

        $username = Env::env('SMTP_USER', '');
        $password = Env::env('SMTP_PASS', '');
        $fromEmail = Env::env('SMTP_FROM_EMAIL');
        $fromName = Env::env('SMTP_FROM_NAME', 'Flyto');
        $secure = Env::env('SMTP_SECURE', '');

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $host;
            $mail->Port = $port;
            $mail->CharSet = 'UTF-8';

            $mail->SMTPAuth = $auth;

            if ($auth) {
                $mail->Username = $username;
                $mail->Password = $password;
            }

            if ($secure !== '') {
                $mail->SMTPSecure = $secure;
            } else {
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;
            }

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toEmail, $toName);

            if ($replyToEmail !== null && $replyToEmail !== '') {
                $mail->addReplyTo($replyToEmail, $replyToName ?? $replyToEmail);
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody;

            $mail->send();
        } catch (Throwable $exception) {
            throw new HttpException('No se pudo enviar el email.', 500);
        }
    }
}
