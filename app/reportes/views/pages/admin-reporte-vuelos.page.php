<?php

use App\Reportes\Models\ReporteVuelosAdmin;

/** @var ReporteVuelosAdmin $reporte */

$basePath = $basePath ?? '';
$meses = [
    1 => 'enero',
    2 => 'febrero',
    3 => 'marzo',
    4 => 'abril',
    5 => 'mayo',
    6 => 'junio',
    7 => 'julio',
    8 => 'agosto',
    9 => 'septiembre',
    10 => 'octubre',
    11 => 'noviembre',
    12 => 'diciembre',
];

$html = fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$number = fn (int $value): string => number_format($value, 0, ',', '.');
$decimal = fn (float $value): string => number_format($value, 1, ',', '.');
$periodo = ucfirst($meses[(int) $reporte->periodo->format('n')] ?? $reporte->periodo->format('F')) . ' ' . $reporte->periodo->format('Y');
$generado = $reporte->generadoEn->format('d') . ' de '
    . ($meses[(int) $reporte->generadoEn->format('n')] ?? $reporte->generadoEn->format('F'))
    . ' de ' . $reporte->generadoEn->format('Y')
    . ' a las ' . $reporte->generadoEn->format('H:i');

?>
<section class="px-5 py-7 sm:px-8">
    <div class="mx-auto max-w-[896px]">
        <header class="grid gap-5 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-start">
            <div class="min-w-0">
                <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Reportes</p>
                <h1 class="mt-1 font-display text-[28.8px] font-medium leading-[43.2px] text-flyto-ink">Reporte de vuelos</h1>
                <p class="mt-1 font-mono text-xs leading-4 text-flyto-muted">Generado el <?= $html($generado) ?></p>
            </div>

            <a href="<?= $html($basePath) ?>/admin/reportes" class="inline-flex h-10 w-fit items-center gap-2 border border-flyto-ink/10 bg-transparent px-5 text-sm font-medium text-flyto-ink transition hover:border-flyto-ink/30 hover:bg-white">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Volver
            </a>
        </header>

        <div class="mt-6 border border-flyto-ink/10 bg-white">
            <div class="border-b border-flyto-ink/10 bg-flyto-sand/20 px-6 py-3">
                <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Resumen general</p>
            </div>

            <dl class="divide-y divide-flyto-ink/10">
                <div class="grid gap-1 px-6 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                    <dt class="text-sm text-flyto-muted">Periodo analizado</dt>
                    <dd class="text-left font-mono text-sm text-flyto-ink sm:text-right"><?= $html($periodo) ?></dd>
                </div>
                <div class="grid gap-1 px-6 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                    <dt class="text-sm text-flyto-muted">Total de vuelos operados</dt>
                    <dd class="text-left font-mono text-sm text-flyto-ink sm:text-right"><?= $html($number($reporte->totalVuelos)) ?></dd>
                </div>
                <div class="grid gap-1 px-6 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                    <dt class="text-sm text-flyto-muted">Ocupaci&oacute;n promedio global</dt>
                    <dd class="text-left font-mono text-sm text-flyto-ink sm:text-right"><?= $html($decimal($reporte->ocupacionPromedioGlobal)) ?>%</dd>
                </div>
                <div class="grid gap-1 px-6 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                    <dt class="text-sm text-flyto-muted">Vuelos con ocupaci&oacute;n &gt; 90%</dt>
                    <dd class="text-left font-mono text-sm text-flyto-ink sm:text-right">
                        <?= $html($number($reporte->vuelosOcupacionAlta)) ?> (<?= $html($decimal($reporte->porcentajeVuelosOcupacionAlta)) ?>%)
                    </dd>
                </div>
                <div class="grid gap-1 px-6 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                    <dt class="text-sm text-flyto-muted">Vuelos con ocupaci&oacute;n &lt; 50%</dt>
                    <dd class="text-left font-mono text-sm text-flyto-ink sm:text-right">
                        <?= $html($number($reporte->vuelosOcupacionBaja)) ?> (<?= $html($decimal($reporte->porcentajeVuelosOcupacionBaja)) ?>%)
                    </dd>
                </div>
                <div class="grid gap-1 px-6 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                    <dt class="text-sm text-flyto-muted">Total de asientos disponibles</dt>
                    <dd class="text-left font-mono text-sm text-flyto-ink sm:text-right"><?= $html($number($reporte->totalAsientosDisponibles)) ?></dd>
                </div>
                <div class="grid gap-1 px-6 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                    <dt class="text-sm text-flyto-muted">Total de asientos vendidos</dt>
                    <dd class="text-left font-mono text-sm text-flyto-ink sm:text-right"><?= $html($number($reporte->totalAsientosVendidos)) ?></dd>
                </div>
            </dl>
        </div>

        <div class="mt-5 border border-flyto-ink/10 bg-white">
            <div class="border-b border-flyto-ink/10 bg-flyto-sand/20 px-6 py-3">
                <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Top 5 aerol&iacute;neas</p>
            </div>

            <?php if ($reporte->topAerolineas === []): ?>
                <div class="px-6 py-10 text-sm text-flyto-muted">No hay vuelos operados en este periodo.</div>
            <?php else: ?>
                <div class="divide-y divide-flyto-ink/10">
                    <?php foreach ($reporte->topAerolineas as $aerolinea): ?>
                        <div class="grid gap-2 px-6 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                            <p class="min-w-0 text-sm text-flyto-muted"><?= $html($aerolinea->nombre) ?></p>
                            <p class="text-left font-mono text-sm text-flyto-ink sm:text-right">
                                <?= $html($number($aerolinea->totalVuelos)) ?> vuelos &middot; <?= $html($decimal($aerolinea->ocupacionPromedio)) ?>% ocupaci&oacute;n
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
