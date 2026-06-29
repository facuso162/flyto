<?php

namespace App\Auth\Services;

use App\Auth\Models\Usuario;
use App\Shared\Config\Env;
use App\Shared\Http\HttpException;
use App\Shared\Services\EmailService;
use Throwable;

require_once __DIR__ . '/../models/usuario.model.php';
require_once __DIR__ . '/../../shared/config/env.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../../shared/services/email.service.php';

class TokenRecuperacionEmailService
{
    private EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function send(Usuario $usuario, string $token, \DateTime $expiresAt, int $validHours): void
    {
        $recoveryUrl = $this->buildRecoveryUrl();
        $fullName = trim($usuario->nombre . ' ' . $usuario->apellido);

        try {
            $this->emailService->send(
                $usuario->email,
                $fullName,
                'Codigo de recuperacion de Flyto',
                $this->htmlBody($usuario->nombre, $token, $expiresAt, $validHours, $recoveryUrl),
                $this->textBody($token, $expiresAt, $validHours, $recoveryUrl)
            );
        } catch (Throwable) {
            throw new HttpException('No se pudo enviar el email de recuperacion.', 500);
        }
    }

    private function buildRecoveryUrl(): string
    {
        $appUrl = Env::env('APP_URL');

        return rtrim($appUrl, '/') . '/auth/recuperar-contrasena/codigo';
    }

    private function htmlBody(
        string $nombre,
        string $token,
        \DateTime $expiresAt,
        int $validHours,
        string $recoveryUrl
    ): string {
        $safeName = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
        $safeToken = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');
        $safeDate = htmlspecialchars($expiresAt->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8');
        $safeUrl = htmlspecialchars($recoveryUrl, ENT_QUOTES, 'UTF-8');

        return "
            <h1>Recupera tu contrasena</h1>
            <p>Hola {$safeName}, recibimos una solicitud para restablecer tu contrasena.</p>
            <p>Tu codigo de recuperacion es <strong>{$safeToken}</strong>.</p>
            <p>Este codigo vence el {$safeDate} y es valido por {$validHours} horas.</p>
            <p><a href=\"{$safeUrl}\">Ingresar codigo de recuperacion</a></p>
        ";
    }

    private function textBody(string $token, \DateTime $expiresAt, int $validHours, string $recoveryUrl): string
    {
        return "Recupera tu contrasena en Flyto\n"
            . "Codigo: {$token}\n"
            . "Vence: " . $expiresAt->format('d/m/Y H:i') . "\n"
            . "Valido por: {$validHours} horas\n"
            . "Ingresa el codigo en: {$recoveryUrl}";
    }
}
