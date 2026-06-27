<?php

namespace App\Usuarios\Services;

use App\Shared\Http\HttpException;
use App\Usuarios\Dtos\CrearCeoDto;
use App\Usuarios\Repositories\UsuarioRepository;

require_once __DIR__ . '/../../shared/http/http-exception.php';
require_once __DIR__ . '/../dtos/crear-ceo.dto.php';
require_once __DIR__ . '/../repositories/usuario.repository.php';

class UsuarioService
{
    private const TIPO_CEO = 'ceo';

    public function __construct(private UsuarioRepository $usuarioRepository)
    {
    }

    public function getConfirmadosByTipo(string $tipo): array
    {
        return $this->usuarioRepository->getConfirmadosByTipo($tipo);
    }

    public function getByTipo(string $tipo): array
    {
        return $this->usuarioRepository->getByTipo($tipo);
    }

    public function crearCeo(CrearCeoDto $dto): int
    {
        if ($this->usuarioRepository->existsByEmail($dto->email)) {
            throw new HttpException('El correo electronico ya se encuentra registrado.', 409, ['field' => 'email']);
        }

        if (!$this->usuarioRepository->aerolineaDisponibleParaCeo($dto->aerolineaId)) {
            throw new HttpException(
                'La aerolinea seleccionada no existe o ya tiene un CEO asignado.',
                409,
                ['field' => 'aerolineaId']
            );
        }

        $tipoUsuarioId = $this->usuarioRepository->getTipoUsuarioIdPorNombre(self::TIPO_CEO);

        if ($tipoUsuarioId === null) {
            throw new HttpException('Tipo de usuario CEO no encontrado.', 500);
        }

        return $this->usuarioRepository->crearCeo($dto, $tipoUsuarioId);
    }
}
