<?php

use App\Promociones\Models\Promocion;
use App\Vuelos\Models\Vuelo;

$basePath = $basePath ?? '';
$proximosVuelos = $proximosVuelos ?? null;
$promocionActiva = $promocionActiva ?? null;
$proximosVuelos = is_array($proximosVuelos) ? $proximosVuelos : [];
$promocionActiva = ($promocionActiva ?? null) instanceof Promocion ? $promocionActiva : null;
$nombreCeo = trim((string) ($currentUser['nombre'] ?? '')) ?: 'CEO';
$meses = [1 => 'ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];
$fechaCorta = static fn (\DateTimeInterface $fecha): string => $fecha->format('j') . ' ' . $meses[(int) $fecha->format('n')] . ' ' . $fecha->format('Y');

?>
<section class="px-5 py-8 sm:px-8 lg:px-8 lg:py-8">
    <div class="mx-auto max-w-[900px]">
        <p class="font-mono text-[11px] uppercase tracking-[0.18em] text-flyto-muted">Panel de control</p>
        <h1 class="mt-2 font-display text-3xl font-medium tracking-tight">Hola, <?= htmlspecialchars($nombreCeo, ENT_QUOTES, 'UTF-8') ?></h1>

        <div class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(250px,0.95fr)]">
            <section aria-labelledby="proximos-vuelos-title">
                <div class="mb-4 flex items-center justify-between">
                    <h2 id="proximos-vuelos-title" class="font-mono text-sm font-medium uppercase tracking-[0.14em]">Próximos vuelos</h2>
                    <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo/vuelos" class="text-xs font-medium text-flyto-navy hover:underline">Ver todos <span aria-hidden="true">→</span></a>
                </div>

                <div class="divide-y divide-flyto-ink/10 border border-flyto-ink/10 bg-white">
                    <?php if ($proximosVuelos === []): ?>
                        <div class="px-6 py-10 text-center text-sm text-flyto-muted">No hay vuelos próximos para tu aerolínea.</div>
                    <?php endif; ?>

                    <?php foreach ($proximosVuelos as $vuelo): ?>
                        <?php
                        if (!$vuelo instanceof Vuelo) {
                            continue;
                        }
                        $capacidad = max(0, $vuelo->asientosDisponibles);
                        $ocupados = min(max(0, $vuelo->asientosOcupados), $capacidad);
                        $ocupacion = $capacidad > 0 ? (int) round(($ocupados / $capacidad) * 100) : 0;
                        ?>
                        <article class="grid gap-5 px-6 py-5 sm:grid-cols-[minmax(0,1fr)_165px] sm:items-center">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="font-display text-lg font-medium">
                                        <?= htmlspecialchars($vuelo->ciudadOrigen['abreviacionCiudad'], ENT_QUOTES, 'UTF-8') ?>
                                        <span aria-hidden="true">↠</span>
                                        <?= htmlspecialchars($vuelo->ciudadDestino['abreviacionCiudad'], ENT_QUOTES, 'UTF-8') ?>
                                    </h3>
                                    <span class="bg-flyto-navy/10 px-2 py-1 font-mono text-[11px] text-flyto-navy"><?= htmlspecialchars($vuelo->codigoVuelo, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <p class="mt-1 text-xs text-flyto-muted"><?= htmlspecialchars($vuelo->aerolinea->nombre, ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($fechaCorta($vuelo->fechaSalida), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <div>
                                <div class="flex justify-between text-[11px] text-flyto-muted"><span>Ocupación</span><span><?= $ocupacion ?>%</span></div>
                                <div class="mt-1 h-1.5 bg-flyto-mist"><div class="h-full bg-flyto-navy" style="width: <?= $ocupacion ?>%"></div></div>
                                <p class="mt-1 text-xs text-flyto-muted"><?= $ocupados ?>/<?= $capacidad ?> asientos</p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo/vuelos" class="mt-4 flex h-11 items-center justify-center gap-2 border border-flyto-ink/10 text-sm transition hover:border-flyto-navy hover:text-flyto-navy">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m4 13 16-8-6 15-3-6-7-1Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                    Ver todos los vuelos
                </a>
            </section>

            <section aria-labelledby="promocion-activa-title">
                <h2 id="promocion-activa-title" class="mb-4 font-mono text-sm font-medium uppercase tracking-[0.14em]">Promoción activa</h2>
                <?php if ($promocionActiva): ?>
                    <article class="border border-flyto-ink/10 bg-white">
                        <div class="flex items-start gap-3 border-b border-flyto-ink/10 px-5 py-4">
                            <span class="font-mono text-base text-flyto-navy">%</span>
                            <p class="min-w-0 flex-1 text-sm leading-5"><?= htmlspecialchars($promocionActiva->descripcion, ENT_QUOTES, 'UTF-8') ?></p>
                            <strong class="font-mono text-xs font-medium text-flyto-navy">-<?= (int) round($promocionActiva->descuento * 100) ?>%</strong>
                        </div>
                        <dl class="grid grid-cols-2 gap-4 px-5 py-4">
                            <div>
                                <dt class="font-mono text-[9px] uppercase tracking-wider text-flyto-muted">Válida hasta</dt>
                                <dd class="mt-1 text-xs"><?= $promocionActiva->fechaFin ? htmlspecialchars($fechaCorta($promocionActiva->fechaFin), ENT_QUOTES, 'UTF-8') : 'Sin vencimiento' ?></dd>
                            </div>
                            <div>
                                <dt class="font-mono text-[9px] uppercase tracking-wider text-flyto-muted">Estado</dt>
                                <dd class="mt-1 text-xs">Activa</dd>
                            </div>
                        </dl>
                    </article>
                <?php else: ?>
                    <div class="border border-flyto-ink/10 bg-white px-5 py-8 text-center text-sm text-flyto-muted">No hay una promoción activa.</div>
                <?php endif; ?>

                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo/promociones" class="mt-4 flex h-11 items-center justify-center gap-2 border border-flyto-ink/10 text-sm transition hover:border-flyto-navy hover:text-flyto-navy">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 13 13 20 4 11V4h7l9 9Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><circle cx="8" cy="8" r="1" fill="currentColor"/></svg>
                    Ver promociones
                </a>
            </section>
        </div>
    </div>
</section>
