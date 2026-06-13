<?php

namespace App\Contacto\Services;

use App\Shared\Http\HttpException;
use App\Shared\Services\EmailService;
use App\Shared\Config\Env;
use Throwable;

require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/services/email.service.php';

class ContactoEmailService
{
    private const DEFAULT_TO_EMAIL = 'contacto@flyto.com.ar';
    private const DEFAULT_TO_NAME = 'Flyto Contacto';

    private EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function send(array $mensaje): void
    {
        $nombreCompleto = trim($mensaje['nombre'] . ' ' . $mensaje['apellido']);
        $toEmail = Env::env('CONTACTO_TO_EMAIL', self::DEFAULT_TO_EMAIL);
        $toName = Env::env('CONTACTO_TO_NAME', self::DEFAULT_TO_NAME);

        try {
            $this->emailService->send(
                $toEmail,
                $toName,
                'Consulta de contacto: ' . $mensaje['asunto'],
                $this->htmlBody($mensaje, $nombreCompleto),
                $this->textBody($mensaje, $nombreCompleto),
                $mensaje['email'],
                $nombreCompleto
            );
        } catch (Throwable $exception) {
            throw new HttpException('No se pudo enviar el mensaje de contacto.', 500);
        }
    }

    private function htmlBody(array $mensaje, string $nombreCompleto): string
    {
        $safeName = htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8');
        $safeEmail = htmlspecialchars($mensaje['email'], ENT_QUOTES, 'UTF-8');
        $safeSubject = htmlspecialchars($mensaje['asunto'], ENT_QUOTES, 'UTF-8');
        $safeMessage = nl2br(htmlspecialchars($mensaje['mensaje'], ENT_QUOTES, 'UTF-8'));

        return "
            <h1>Nueva consulta de contacto</h1>
            <p><strong>Nombre:</strong> {$safeName}</p>
            <p><strong>Email:</strong> {$safeEmail}</p>
            <p><strong>Asunto:</strong> {$safeSubject}</p>
            <p><strong>Mensaje:</strong></p>
            <p>{$safeMessage}</p>
        ";
    }

    private function textBody(array $mensaje, string $nombreCompleto): string
    {
        return "Nueva consulta de contacto\n"
            . "Nombre: {$nombreCompleto}\n"
            . "Email: {$mensaje['email']}\n"
            . "Asunto: {$mensaje['asunto']}\n\n"
            . $mensaje['mensaje'];
    }
}
