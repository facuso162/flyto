<?php

use App\Promociones\Models\Promocion;

$basePath = $basePath ?? '';
$promocion = $promocion ?? null;
$promocion = $promocion instanceof Promocion ? $promocion : null;

if ($promocion === null) {
    return;
}

$e = static fn (string $valor): string => htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');

$porcentaje = $promocion->descuento * 100;
$porcentajeTexto = rtrim(rtrim(number_format($porcentaje, 2, '.', ''), '0'), '.');

$meses = [1 => 'ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];
$fechaFin = $promocion->fechaFin;
$fechaFinTexto = $fechaFin instanceof \DateTimeInterface
    ? $fechaFin->format('j') . ' ' . $meses[(int) $fechaFin->format('n')] . ' ' . $fechaFin->format('Y')
    : 'Sin definir';

$ceoNombre = trim(($promocion->ceo['nombre'] ?? '') . ' ' . ($promocion->ceo['apellido'] ?? ''));
$ceoNombre = $ceoNombre !== '' ? $ceoNombre : 'Sin asignar';

?>
<article class="border-b border-flyto-ink/10 px-6 py-5 last:border-b-0">
    <div class="flex flex-wrap items-center gap-2.5">
        <h2 class="text-sm font-medium text-flyto-ink"><?= $e($promocion->aerolinea->nombre) ?></h2>
        <span class="bg-flyto-mist px-2 py-0.5 font-mono text-xs text-flyto-ink">-<?= $e($porcentajeTexto) ?>%</span>
    </div>

    <p class="mt-1.5 text-xs leading-5 text-flyto-muted"><?= $e($promocion->descripcion) ?></p>

    <div class="mt-2 flex flex-wrap gap-x-10 gap-y-1 font-mono text-xs text-flyto-muted">
        <span>CEO: <?= $e($ceoNombre) ?></span>
        <span>Finaliza: <?= $e($fechaFinTexto) ?></span>
    </div>

    <div class="mt-3 flex flex-wrap items-center justify-end gap-2">
        <form method="post" action="<?= $e($basePath) ?>/admin/promociones/denegar">
            <input type="hidden" name="id" value="<?= (int) $promocion->id ?>">
            <button type="submit" class="flex items-center gap-1.5 border border-red-700/30 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:border-red-700 hover:bg-red-50">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6 18 18M18 6 6 18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                Denegar
            </button>
        </form>
        <form method="post" action="<?= $e($basePath) ?>/admin/promociones/aprobar">
            <input type="hidden" name="id" value="<?= (int) $promocion->id ?>">
            <button type="submit" class="flex items-center gap-1.5 bg-flyto-navy px-3 py-1.5 text-xs font-medium text-flyto-sand transition hover:bg-flyto-ink">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Aprobar
            </button>
        </form>
    </div>
</article>
