<?php

use App\Promociones\Models\Promocion;

$basePath = $basePath ?? '';
$promocion = $promocion ?? null;
$promocion = $promocion instanceof Promocion ? $promocion : null;

if ($promocion === null) {
    return;
}

$estado = (string) ($promocion->estado['descripcion'] ?? '');
$esActiva = $promocion->activa && $estado === 'activa';
$estadoTexto = match ($estado) {
    'activa' => 'Activa',
    'pendiente_activacion' => 'Esperando aprobación',
    default => 'Desactivada',
};
$estadoClase = match ($estado) {
    'activa' => 'bg-flyto-navy/10 text-flyto-navy',
    'pendiente_activacion' => 'bg-[#fef3c6] text-[#bb4d00]',
    default => 'bg-[#e5e4e0] text-flyto-muted',
};
$porcentaje = $promocion->descuento * 100;
$porcentajeTexto = rtrim(rtrim(number_format($porcentaje, 2, '.', ''), '0'), '.');
$descuentoTexto = number_format($promocion->descuento, 2, '.', '');
$muestraFechaFin = in_array($estado, ['activa', 'pendiente_activacion'], true);
$fechaFinTexto = $promocion->fechaFin?->format('d/m/Y') ?? 'Sin definir';
$estadoEditable = in_array($estado, ['inactiva', 'activa', 'pendiente_activacion'], true);
$e = static fn (string $valor): string => htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');

?>
<article class="border-b border-flyto-ink/10 px-5 py-5 last:border-b-0 sm:px-6 <?= $esActiva ? 'bg-[rgba(30,45,74,0.03)]' : '' ?>">
    <div class="flex flex-wrap items-center gap-2">
        <span class="px-2.5 py-0.5 font-mono text-xs font-medium <?= $estadoClase ?>"><?= $estadoTexto ?></span>
        <span class="font-mono text-xs text-flyto-muted">Descuento: <?= $e($porcentajeTexto) ?>% (<?= $e($descuentoTexto) ?>)</span>
        <?php if ($muestraFechaFin): ?>
            <span class="font-mono text-xs text-flyto-muted">Fecha fin: <?= $e($fechaFinTexto) ?></span>
        <?php endif; ?>
    </div>

    <p class="mt-1.5 text-sm leading-6 text-flyto-ink"><?= $e($promocion->descripcion) ?></p>

    <div class="mt-3 flex flex-wrap items-center justify-end gap-2">
        <form method="post" action="<?= $e($basePath) ?>/ceo/promociones/borrar">
            <input type="hidden" name="id" value="<?= $promocion->id ?>">
            <button type="submit" class="flex items-center gap-1.5 border border-red-700/30 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:border-red-700 hover:bg-red-50">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Borrar
            </button>
        </form>
        <?php if ($estadoEditable): ?>
        <a href="<?= $e($basePath . '/ceo/promociones/editar?' . http_build_query(['id' => $promocion->id])) ?>" class="flex items-center gap-1.5 border border-flyto-ink/10 px-3 py-1.5 text-xs font-medium text-flyto-ink">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 16-1 4 4-1L19 8l-3-3L5 16Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
            Editar
        </a>
        <?php endif; ?>
        <?php if ($esActiva): ?>
        <form method="post" action="<?= $e($basePath) ?>/ceo/promociones/desactivar">
            <input type="hidden" name="id" value="<?= $promocion->id ?>">
            <button type="submit" class="flex items-center gap-1.5 bg-flyto-navy px-3 py-1.5 text-xs font-medium text-flyto-sand transition hover:bg-flyto-ink">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v9m-5.5-7A8 8 0 1 0 17.5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                Desactivar
            </button>
        </form>
        <?php else: ?>
        <a href="<?= $e($basePath . '/ceo/promociones/solicitar-activacion?' . http_build_query(['id' => $promocion->id])) ?>" class="flex items-center gap-1.5 bg-flyto-navy px-3 py-1.5 text-xs font-medium text-flyto-sand transition hover:bg-flyto-ink">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v9m-5.5-7A8 8 0 1 0 17.5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            Activar
        </a>
        <?php endif; ?>
    </div>
</article>
