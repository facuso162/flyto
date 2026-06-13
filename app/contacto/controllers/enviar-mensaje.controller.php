<?php

namespace App\Contacto\Controllers;

use App\Contacto\Services\EnviarMensajeService;
use App\Shared\Http\JsonRequest;
use App\Shared\Http\JsonResponse;
use Throwable;

require_once __DIR__ . '/../services/enviar-mensaje.service.php';
require_once __DIR__ . '/../../shared/http/json-request.php';
require_once __DIR__ . '/../../shared/http/json-response.php';

class EnviarMensajeController
{
    private EnviarMensajeService $enviarMensajeService;

    public function __construct(
        EnviarMensajeService $enviarMensajeService
    )
    {
        $this->enviarMensajeService = $enviarMensajeService;
    }

    public function enviar(array $params = [], array $query = []): void
    {
        try {
            $data = $this->isJsonRequest() ? JsonRequest::body() : $_POST;

            $this->enviarMensajeService->execute($data);

            if ($this->expectsJson()) {
                JsonResponse::success([
                    'message' => 'Mensaje enviado correctamente.',
                ], 201);

                return;
            }

            $this->redirectToContact('enviado');
        } catch (Throwable $exception) {
            if ($this->expectsJson()) {
                JsonResponse::exception($exception);

                return;
            }

            $this->redirectToContact('error');
        }
    }

    private function isJsonRequest(): bool
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        return str_contains($contentType, 'application/json');
    }

    private function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

        return $this->isJsonRequest() || str_contains($accept, 'application/json');
    }

    private function redirectToContact(string $estado): void
    {
        header('Location: ' . $this->basePath() . '/contacto?contacto=' . rawurlencode($estado) . '#contacto', true, 303);
    }

    private function basePath(): string
    {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

        return $scriptDir === '/' ? '' : rtrim($scriptDir, '/');
    }
}
