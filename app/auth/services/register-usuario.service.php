<?php

namespace App\Auth\Services;

use App\Auth\Dtos\RegisterUsuarioDTO;
use App\Auth\Models\Usuario;
use App\Auth\Repositories\TipoUsuarioRepository;
use App\Auth\Repositories\UsuarioRepository;

require_once __DIR__ . '/../dtos/register-usuario.dto.php';
require_once __DIR__ . '/../models/usuario.model.php';
require_once __DIR__ . '/../repositories/usuario.repository.php';
require_once __DIR__ . '/../repositories/tipo-usuario.repository.php';

class RegisterUsuarioService
{
    private UsuarioRepository $usuarioRepository;
    private TipoUsuarioRepository $tipoUsuarioRepository;

    public function __construct(
        UsuarioRepository $usuarioRepository,
        TipoUsuarioRepository $tipoUsuarioRepository
    ) {
        $this->usuarioRepository = $usuarioRepository;
        $this->tipoUsuarioRepository = $tipoUsuarioRepository;
    }

    // TODO: Posible retorno cuando exista la entidad:
    //      public function execute(RegisterUsuarioDTO $dto): Usuario
    public function execute(RegisterUsuarioDTO $dto): void {
        // Validacion semantica: email unico.
        $emailAlreadyExists = $this->usuarioRepository->existsByEmail($dto->email);

        if ($emailAlreadyExists) {
            throw new \Exception('El email ya se encuentra registrado.', 409);
        }

        $claveHash = password_hash($dto->password, PASSWORD_DEFAULT);

        // Genera un token de verificación único
        $tokenVerificacion = bin2hex(random_bytes(32));
        
        // IDS DE TIPOS DE USUARIOS:
        // 1: administrador
        // 2: ceo
        // 3: cliente

        $tipoUsuario = $this->tipoUsuarioRepository->findByNombre('cliente');

        if (!$tipoUsuario) {
            throw new \Exception('Tipo de usuario no encontrado.', 500);
        }

        $usuario = new Usuario(
            id: null,
            nombre: $dto->nombre,
            apellido: $dto->apellido,
            email: $dto->email,
            telefono: $dto->telefono,
            claveHash: $claveHash,
            tipoUsuario: $tipoUsuario,
            activo: true, // Este dato indica si la cuenta esta bloqueada o no, no tiene que ver con la verificacion del email.
            fechaRegistro: new \DateTime(), // Ahora mismo se hace con UTC 0
            emailVerificado: false,
            tokenVerificacion: $tokenVerificacion,
            tokenRecupero: null,
            tokenExpiracion: null
        );

        // Guardar en base de datos cuando exista el repository.
        $this->usuarioRepository->create($usuario);

        // TODO: enviar mail de verificacion

        // return $usuario;
    }
}
