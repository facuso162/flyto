<?php

namespace App\Auth\Services;

use App\Auth\Dtos\RegisterUsuarioDTO;
use App\Auth\Models\Usuario;
use App\Auth\Repositories\TipoUsuarioRepository;
use App\Auth\Repositories\UsuarioRepository;
use App\Shared\Http\HttpException;

require_once __DIR__ . '/../dtos/register-usuario.dto.php';
require_once __DIR__ . '/../models/usuario.model.php';
require_once __DIR__ . '/../repositories/tipo-usuario.repository.php';
require_once __DIR__ . '/../repositories/usuario.repository.php';
require_once __DIR__ . '/confirmacion-usuario-email.service.php';
require_once __DIR__ . '/../../shared/http/http-exception.php';

class RegisterUsuarioService
{
    private UsuarioRepository $usuarioRepository;
    private TipoUsuarioRepository $tipoUsuarioRepository;
    private ConfirmacionUsuarioEmailService $confirmacionEmailService;

    public function __construct(
        UsuarioRepository $usuarioRepository,
        TipoUsuarioRepository $tipoUsuarioRepository,
        ConfirmacionUsuarioEmailService $confirmacionEmailService
    ) {
        $this->usuarioRepository = $usuarioRepository;
        $this->tipoUsuarioRepository = $tipoUsuarioRepository;
        $this->confirmacionEmailService = $confirmacionEmailService;
    }

    public function execute(RegisterUsuarioDTO $dto): void
    {
        if ($this->usuarioRepository->existsByEmail($dto->email)) {
            throw new HttpException('El email ya se encuentra registrado.', 409);
        }

        $tipoUsuario = $this->tipoUsuarioRepository->findByNombre('cliente'); //TODO: enum de tipos

        if (!$tipoUsuario) {
            throw new HttpException('Tipo de usuario no encontrado.', 500);
        }

        $usuario = new Usuario(
            id: null,
            nombre: $dto->nombre,
            apellido: $dto->apellido,
            email: $dto->email,
            telefono: $dto->telefono,
            claveHash: password_hash($dto->password, PASSWORD_DEFAULT),
            tipoUsuario: $tipoUsuario,
            activo: true,
            fechaRegistro: new \DateTime(),
            emailVerificado: false,
            tokenVerificacion: bin2hex(random_bytes(32)),
            tokenRecupero: null,
            tokenExpiracion: null
        );

        // No usar pdo en un servicio, para eso estan los repositories
        $this->usuarioRepository->register($usuario);

        $this->confirmacionEmailService->send($usuario);        
    }
}
