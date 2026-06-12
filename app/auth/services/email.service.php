<?php

namespace App\Auth\Services;

use App\Shared\Http\HttpException;
use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

require_once __DIR__ . '/../../shared/http/http-exception.php';

class EmailService
{
    public function send(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody): void
    {
        if (!class_exists(PHPMailer::class)) {
            throw new HttpException('PHPMailer no esta instalado. Ejecuta composer install antes de enviar emails.', 500);
        }

        $host = $this->env('SMTP_HOST');
        $port = (int) $this->env('SMTP_PORT', '1025');
        $auth = $this->envBool('SMTP_AUTH', false);
        $username = $this->env('SMTP_USER', '');
        $password = $this->env('SMTP_PASS', '');
        $fromEmail = $this->env('SMTP_FROM_EMAIL');
        $fromName = $this->env('SMTP_FROM_NAME', 'Flyto');
        $secure = $this->env('SMTP_SECURE', '');

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

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody;

            $mail->send();
        } catch (Throwable $exception) {
            throw new HttpException('No se pudo enviar el email de confirmacion.', 500);
        }
    }

    private function env(string $key, ?string $default = null): string
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            if ($default !== null) {
                return $default;
            }

            throw new HttpException("Falta configurar {$key}.", 500);
        }

        return $value;
    }

    private function envBool(string $key, bool $default = false): bool
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
    }
}
