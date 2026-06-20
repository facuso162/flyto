<?php

use App\Vuelos\Models\Vuelo;

$basePath = $basePath ?? '';

$vuelo = $vuelo ?? null;
$vuelo = $vuelo instanceof Vuelo ? $vuelo : null;

if ($vuelo === null) {
    return;
}

$estado = strtolower($vuelo->estado);
$estadoTexto = match ($estado) {
    'completado' => 'Completado',
    'cancelado' => 'Cancelado',
    default => 'Pendiente',
};
$estadoClase = match ($estado) {
    'cancelado' => 'bg-red-50 text-red-700',
    'pendiente' => 'bg-flyto-navy/10 text-flyto-navy',
    default => 'bg-flyto-ink/10 text-flyto-muted',
};
$capacidad = max(0, $vuelo->asientosDisponibles);
$ocupados = min(max(0, $vuelo->asientosOcupados), $capacidad);
$tieneAsientosOcupados = $vuelo->asientosOcupados > 0;
$estadoVuelo = $vuelo->estado;
$ocupacion = $capacidad > 0 ? (int) round(($ocupados / $capacidad) * 100) : 0;
$e = static fn (string $valor): string => htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');

?>
<article class="border-b border-flyto-ink/10 px-5 py-5 last:border-b-0 sm:px-6">
    <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="font-display text-lg font-medium">
                    <?= $e($vuelo->ciudadOrigen['abreviacionCiudad']) ?>
                    <span aria-hidden="true">→</span>
                    <?= $e($vuelo->ciudadDestino['abreviacionCiudad']) ?>
                </h2>
                <span class="bg-flyto-mist px-2 py-0.5 font-mono text-xs text-flyto-ink"><?= $e($vuelo->codigoVuelo) ?></span>
                <span class="px-2.5 py-0.5 font-mono text-xs <?= $estadoClase ?>"><?= $estadoTexto ?></span>
            </div>
            <p class="mt-1 text-xs text-flyto-muted">
                <?= $e($vuelo->ciudadOrigen['nombreCiudad']) ?>
                <span aria-hidden="true">→</span>
                <?= $e($vuelo->ciudadDestino['nombreCiudad']) ?>
                <span aria-hidden="true">·</span>
                <?= $e($vuelo->aerolinea['nombreAerolinea']) ?>
            </p>
            <p class="mt-1.5 font-mono text-xs text-flyto-muted">
                <?= $e($vuelo->fechaSalida->format('Y-m-d H:i')) ?>
                <span class="mx-2 font-sans" aria-hidden="true">·</span>
                USD <?= $e(number_format($vuelo->precio, 0, ',', '.')) ?>
                <span class="mx-2 font-sans" aria-hidden="true">·</span>
                <?= $ocupacion ?>% ocupado
                <span class="mx-2 font-sans" aria-hidden="true">·</span>
                <?= $capacidad ?>/<?= $ocupados ?> asientos
            </p>
        </div>

        <div class="flex shrink-0 items-center justify-end gap-2 sm:self-end">
            <?php if (!$tieneAsientosOcupados): ?>
                <form method="post" action="<?= $e($basePath) ?>/ceo/vuelos/borrar">
                    <input type="hidden" name="vueloId" value="<?= $vuelo->id ?>">
                    <button type="submit" aria-label="Borrar <?= $e($vuelo->codigoVuelo) ?>" class="flex items-center gap-1.5 border border-red-700/30 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:border-red-700 hover:bg-red-50">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Borrar
                    </button>
                </form>
            <?php endif; ?>
            <?php if (!($ocupados > 0 || $estadoVuelo === 'completado' || $estadoVuelo === 'cancelado')): ?>
                <a href="<?= $e($basePath) ?>/ceo/vuelos/editar?id=<?= $vuelo->id ?>" aria-label="Editar <?= $e($vuelo->codigoVuelo) ?>" class="flex items-center gap-1.5 bg-flyto-navy px-3 py-1.5 text-xs font-medium text-flyto-sand transition hover:bg-flyto-ink">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 16-1 4 4-1L19 8l-3-3L5 16Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                    Editar
                </a>
            <?php endif; ?>
        </div>
    </div>
</article>
