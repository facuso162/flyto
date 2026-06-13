<?php

namespace App\Contacto\Services;

use App\Shared\Http\HttpException;

require_once __DIR__ . '/contacto-email.service.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class EnviarMensajeService
{
    private ContactoEmailService $contactoEmailService;

    public function __construct(ContactoEmailService $contactoEmailService)
    {
        $this->contactoEmailService = $contactoEmailService;
    }

    public function execute(array $data): void
    {
        $mensaje = [
            'nombre' => $this->getStringValue($data, 'nombre'),
            'apellido' => $this->getStringValue($data, 'apellido'),
            'email' => $this->getStringValue($data, 'email'),
            'asunto' => $this->getStringValue($data, 'asunto'),
            'mensaje' => $this->getStringValue($data, 'mensaje'),
        ];

        $this->validate($mensaje);
        $this->contactoEmailService->send($mensaje);
    }

    private function validate(array $mensaje): void
    {
        if ($mensaje['nombre'] === '') {
            throw new HttpException('El nombre es obligatorio.', 400, ['field' => 'nombre']);
        }

        if (strlen($mensaje['nombre']) > 80) {
            throw new HttpException('El nombre no puede superar los 80 caracteres.', 400, ['field' => 'nombre']);
        }

        if ($mensaje['apellido'] === '') {
            throw new HttpException('El apellido es obligatorio.', 400, ['field' => 'apellido']);
        }

        if (strlen($mensaje['apellido']) > 80) {
            throw new HttpException('El apellido no puede superar los 80 caracteres.', 400, ['field' => 'apellido']);
        }

        if ($mensaje['email'] === '' || filter_var($mensaje['email'], FILTER_VALIDATE_EMAIL) === false) {
            throw new HttpException('El email es obligatorio y debe tener un formato valido.', 400, ['field' => 'email']);
        }

        if ($mensaje['asunto'] === '') {
            throw new HttpException('El asunto es obligatorio.', 400, ['field' => 'asunto']);
        }

        if (strlen($mensaje['asunto']) > 120) {
            throw new HttpException('El asunto no puede superar los 120 caracteres.', 400, ['field' => 'asunto']);
        }

        if ($mensaje['mensaje'] === '') {
            throw new HttpException('El mensaje es obligatorio.', 400, ['field' => 'mensaje']);
        }

        if (strlen($mensaje['mensaje']) > 2000) {
            throw new HttpException('El mensaje no puede superar los 2000 caracteres.', 400, ['field' => 'mensaje']);
        }
    }

    private function getStringValue(array $data, string $key): string
    {
        if (!array_key_exists($key, $data) || $data[$key] === null) {
            return '';
        }

        return trim((string) $data[$key]);
    }
}
