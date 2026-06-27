<?php

use App\Reportes\Models\ReporteCeosAdmin;

/** @var ReporteCeosAdmin $reporte */

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
$money = fn (float $value): string => 'ARS ' . number_format($value, 0, ',', '.');
$number = fn (int $value): string => number_format($value, 0, ',', '.');
$periodo = $meses[(int) $reporte->periodo->format('n')] ?? $reporte->periodo->format('F');
$periodoCompleto = $periodo . ' ' . $reporte->periodo->format('Y');
$generado = $reporte->generadoEn->format('d') . ' de '
    . ($meses[(int) $reporte->generadoEn->format('n')] ?? $reporte->generadoEn->format('F'))
    . ' de ' . $reporte->generadoEn->format('Y')
    . ' a las ' . $reporte->generadoEn->format('H:i');
$ceoDelMes = $reporte->ceoDelMes;
$ceoDelMesLabel = $ceoDelMes === null
    ? 'Sin reservas confirmadas'
    : trim($ceoDelMes->nombre . ' ' . $ceoDelMes->apellido) . ' - ' . $money($ceoDelMes->ingresos);

?>
<section class="px-5 py-7 sm:px-8">
    <div class="mx-auto max-w-[896px]">
        <header class="grid gap-5 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-start">
            <div class="min-w-0">
                <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Reportes</p>
                <h1 class="mt-1 font-display text-[28.8px] font-medium leading-[43.2px] text-flyto-ink">Reporte de CEOs</h1>
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
                <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">CEOs registrados</p>
            </div>

            <dl class="divide-y divide-flyto-ink/10">
                <div class="grid gap-1 px-6 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                    <dt class="text-sm text-flyto-muted">Total de CEOs</dt>
                    <dd class="text-left font-mono text-sm text-flyto-ink sm:text-right"><?= $html($number($reporte->totalCeos)) ?></dd>
                </div>
                <div class="grid gap-1 px-6 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                    <dt class="text-sm text-flyto-muted">CEOs nuevos en <?= $html($periodoCompleto) ?></dt>
                    <dd class="text-left font-mono text-sm text-flyto-ink sm:text-right"><?= $html($number($reporte->ceosNuevosMes)) ?></dd>
                </div>
                <div class="grid gap-1 px-6 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                    <dt class="text-sm text-flyto-muted">CEOs con promoci&oacute;n activa</dt>
                    <dd class="text-left font-mono text-sm text-flyto-ink sm:text-right"><?= $html($number($reporte->ceosConPromocionActiva)) ?></dd>
                </div>
                <div class="grid gap-1 px-6 py-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                    <dt class="text-sm text-flyto-muted">CEO del mes</dt>
                    <dd class="max-w-full text-left font-mono text-sm text-flyto-ink sm:max-w-[420px] sm:text-right"><?= $html($ceoDelMesLabel) ?></dd>
                </div>
            </dl>
        </div>
    </div>
</section>
