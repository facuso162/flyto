<?php

use App\Reportes\Models\ReporteVentas;

/** @var ReporteVentas $reporte */

$basePath = $basePath ?? '';
$meses = [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre',
];

$html = fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$money = fn (float $value): string => 'ARS ' . number_format($value, 0, ',', '.');
$number = fn (int $value): string => number_format($value, 0, ',', '.');
$decimal = fn (float $value): string => number_format($value, 1, ',', '.');
$periodo = ($meses[(int) $reporte->periodo->format('n')] ?? $reporte->periodo->format('F')) . ' ' . $reporte->periodo->format('Y');
$generado = $reporte->generadoEn->format('d/m/Y H:i');

?>
<section class="px-5 py-7 sm:px-8">
    <div class="mx-auto max-w-[896px]">
        <a href="<?= $html($basePath) ?>/ceo/reportes" class="inline-flex h-10 items-center gap-2 border border-flyto-ink/10 bg-transparent px-5 text-sm font-medium text-flyto-ink transition hover:border-flyto-ink/30 hover:bg-white">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Volver
        </a>

        <header class="mt-3">
            <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Reportes</p>
            <h1 class="mt-1 font-display text-[28.8px] font-medium leading-[43.2px] text-flyto-ink">Reporte de ventas</h1>
            <p class="mt-1 font-mono text-xs leading-4 text-flyto-muted">Generado el <?= $html($generado) ?></p>
        </header>

        <div class="mt-6 border border-flyto-ink/10 bg-white">
            <div class="border-b border-flyto-ink/10 bg-flyto-sand/20 px-6 py-3">
                <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Resumen general</p>
            </div>

            <dl class="divide-y divide-flyto-ink/10">
                <div class="flex items-center justify-between gap-5 px-6 py-3">
                    <dt class="text-sm text-flyto-muted">Periodo analizado</dt>
                    <dd class="text-right font-mono text-sm text-flyto-ink"><?= $html($periodo) ?></dd>
                </div>
                <div class="flex items-center justify-between gap-5 px-6 py-3">
                    <dt class="text-sm text-flyto-muted">Total de reservas completadas</dt>
                    <dd class="text-right font-mono text-sm text-flyto-ink"><?= $html($number($reporte->totalReservasCompletadas)) ?></dd>
                </div>
                <div class="flex items-center justify-between gap-5 px-6 py-3">
                    <dt class="text-sm text-flyto-muted">Ingresos totales</dt>
                    <dd class="text-right font-mono text-sm text-flyto-ink"><?= $html($money($reporte->ingresosTotales)) ?></dd>
                </div>
                <div class="flex items-center justify-between gap-5 px-6 py-3">
                    <dt class="text-sm text-flyto-muted">Reservas canceladas</dt>
                    <dd class="text-right font-mono text-sm text-flyto-ink">
                        <?= $html($number($reporte->reservasCanceladas)) ?> (<?= $html($decimal($reporte->porcentajeCanceladas)) ?>%)
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-5 px-6 py-3">
                    <dt class="text-sm text-flyto-muted">Reembolsos emitidos</dt>
                    <dd class="text-right font-mono text-sm text-flyto-ink"><?= $html($money($reporte->montoCancelaciones)) ?></dd>
                </div>
            </dl>
        </div>

        <div class="mt-5 border border-flyto-ink/10 bg-white">
            <div class="border-b border-flyto-ink/10 bg-flyto-sand/20 px-6 py-3">
                <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Top 5 vuelos</p>
            </div>

            <?php if ($reporte->topVuelos === []): ?>
                <div class="px-6 py-10 text-sm text-flyto-muted">No hay vuelos con reservas completadas en este periodo.</div>
            <?php else: ?>
                <div class="divide-y divide-flyto-ink/10">
                    <?php foreach ($reporte->topVuelos as $vuelo): ?>
                        <div class="grid gap-2 px-6 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                            <p class="min-w-0 text-sm text-flyto-muted">
                                <?= $html($vuelo->codigoVuelo) ?> - <?= $html($vuelo->origen) ?> &rarr; <?= $html($vuelo->destino) ?>
                            </p>
                            <p class="text-left font-mono text-sm text-flyto-ink sm:text-right">
                                <?= $html($money($vuelo->ingresos)) ?> · <?= $html($number($vuelo->reservas)) ?> reservas
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
