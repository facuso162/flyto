<?php

namespace App\Auth\Services;

use App\Auth\Dtos\EnviarTokenRecuperacionDTO;
use App\Auth\Repositories\UsuarioRepository;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../dtos/enviar-token-recuperacion.dto.php';
require_once __DIR__ . '/../repositories/usuario.repository.php';
require_once __DIR__ . '/token-recuperacion-email.service.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class EnviarTokenRecuperacionService
{
    private const TOKEN_VALID_HOURS = 6;

    private UsuarioRepository $usuarioRepository;
    private TokenRecuperacionEmailService $emailService;

    public function __construct(
        UsuarioRepository $usuarioRepository,
        TokenRecuperacionEmailService $emailService
    ) {
        $this->usuarioRepository = $usuarioRepository;
        $this->emailService = $emailService;
    }

    public function execute(EnviarTokenRecuperacionDTO $dto): void
    {
        $usuario = $this->usuarioRepository->findByEmail($dto->email);

        if (!$usuario) {
            throw new HttpException('No encontramos un usuario con ese email.', 404, ['field' => 'email']);
        }

        $token = (string) random_int(100000, 999999);
        $expiresAt = new \DateTime('+' . self::TOKEN_VALID_HOURS . ' hours');

        $this->usuarioRepository->updateTokenRecuperacion((int) $usuario->id, $token, $expiresAt);
        $this->emailService->send($usuario, $token, $expiresAt, self::TOKEN_VALID_HOURS);
    }
}
