<?php

use App\Promociones\Models\Promocion;
use App\Usuarios\Models\Usuario as UsuarioPanel;

$basePath = $basePath ?? '';
$currentUser = $currentUser ?? [];
$currentUser = is_array($currentUser) ? $currentUser : [];
$promocionesPendientes = $promocionesPendientes ?? [];
$ceosRegistrados = $ceosRegistrados ?? [];
$promocionesPendientes = is_array($promocionesPendientes) ? $promocionesPendientes : [];
$ceosRegistrados = is_array($ceosRegistrados) ? $ceosRegistrados : [];
$nombreAdmin = trim((string) ($currentUser['nombre'] ?? '')) ?: 'Admin';
$meses = [1 => 'ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];
$fechaCorta = static fn (\DateTimeInterface $fecha): string => $fecha->format('j') . ' ' . $meses[(int) $fecha->format('n')] . ' ' . $fecha->format('Y');
$desde = static fn (\DateTimeInterface $fecha): string => 'desde ' . $meses[(int) $fecha->format('n')] . ' ' . $fecha->format('Y');

?>
<section class="px-5 py-8 sm:px-8 lg:px-8 lg:py-8">
    <div class="max-w-[1024px]">
        <p class="font-mono text-xs uppercase tracking-[0.16em] text-flyto-muted">Panel de control</p>
        <h1 class="mt-2 font-display text-3xl font-medium text-flyto-ink">Hola, <?= htmlspecialchars($nombreAdmin, ENT_QUOTES, 'UTF-8') ?></h1>

        <div class="mt-8 grid gap-6 xl:grid-cols-2">
            <section aria-labelledby="solicitudes-aprobacion-title">
                <h2 id="solicitudes-aprobacion-title" class="font-mono text-sm font-medium uppercase tracking-[0.14em] text-flyto-ink">Solicitudes de aprobacion</h2>

                <div class="mt-4 divide-y divide-flyto-ink/10 border border-flyto-ink/10 bg-white">
                    <?php if ($promocionesPendientes === []): ?>
                        <div class="px-6 py-10 text-center text-sm text-flyto-muted">No hay promociones pendientes de activacion.</div>
                    <?php endif; ?>

                    <?php foreach ($promocionesPendientes as $promocion): ?>
                        <?php
                        if (!$promocion instanceof Promocion) {
                            continue;
                        }

                        $ceo = $promocion->ceo;
                        $nombreCeo = trim((string) (($ceo['nombre'] ?? '') . ' ' . ($ceo['apellido'] ?? ''))) ?: 'CEO';
                        $fechaFin = $promocion->fechaFin instanceof \DateTimeInterface ? $fechaCorta($promocion->fechaFin) : 'sin fecha fin';
                        ?>
                        <article class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-xs font-medium text-flyto-ink"><?= htmlspecialchars((string) ($promocion->aerolinea['nombre'] ?? 'Aerolinea'), ENT_QUOTES, 'UTF-8') ?></h3>
                                <span class="bg-[#fef3c6] px-2 py-1 font-mono text-[11px] leading-none text-[#c05a00]">-<?= (int) round($promocion->descuento * 100) ?>%</span>
                            </div>
                            <p class="mt-2 text-xs leading-5 text-flyto-muted"><?= htmlspecialchars($promocion->descripcion, ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="mt-2 font-mono text-[11px] leading-4 text-flyto-muted">
                                <?= htmlspecialchars($nombreCeo, ENT_QUOTES, 'UTF-8') ?>
                                <span aria-hidden="true"> &middot; </span>
                                solicitud <?= htmlspecialchars($fechaCorta($promocion->fechaCreacion), ENT_QUOTES, 'UTF-8') ?>
                            </p>
                            <p class="mt-1 font-mono text-[11px] leading-4 text-flyto-muted">finaliza <?= htmlspecialchars($fechaFin, ENT_QUOTES, 'UTF-8') ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>

                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/admin/promociones" class="mt-4 flex h-11 items-center justify-center gap-2 border border-flyto-ink/10 bg-transparent text-sm font-medium transition hover:border-flyto-navy hover:text-flyto-navy">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 13 13 20 4 11V4h7l9 9Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><circle cx="8" cy="8" r="1" fill="currentColor"/></svg>
                    Ver todas las promociones
                </a>
            </section>

            <section aria-labelledby="ceos-registrados-title">
                <h2 id="ceos-registrados-title" class="font-mono text-sm font-medium uppercase tracking-[0.14em] text-flyto-ink">CEOs registrados</h2>

                <div class="mt-4 divide-y divide-flyto-ink/10 border border-flyto-ink/10 bg-white">
                    <?php if ($ceosRegistrados === []): ?>
                        <div class="px-6 py-10 text-center text-sm text-flyto-muted">No hay CEOs confirmados para mostrar.</div>
                    <?php endif; ?>

                    <?php foreach ($ceosRegistrados as $ceo): ?>
                        <?php
                        if (!$ceo instanceof UsuarioPanel) {
                            continue;
                        }

                        $aerolinea = is_array($ceo->aerolinea) ? (string) ($ceo->aerolinea['nombre'] ?? 'Sin aerolinea') : 'Sin aerolinea';
                        ?>
                        <article class="grid gap-4 px-6 py-4 sm:grid-cols-[32px_minmax(0,1fr)_108px] sm:items-center">
                            <span class="flex h-8 w-8 items-center justify-center bg-flyto-navy/10 text-flyto-navy" aria-hidden="true">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM6 20a6 6 0 0 1 12 0" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                            </span>
                            <div class="min-w-0">
                                <h3 class="truncate text-sm font-medium text-flyto-ink"><?= htmlspecialchars($ceo->nombreCompleto(), ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="truncate font-mono text-xs text-flyto-muted"><?= htmlspecialchars($aerolinea, ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <p class="font-mono text-xs text-flyto-muted sm:text-right"><?= htmlspecialchars($desde($ceo->fechaRegistro), ENT_QUOTES, 'UTF-8') ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>

                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/admin/ceos" class="mt-4 flex h-11 items-center justify-center gap-2 border border-flyto-ink/10 bg-transparent text-sm font-medium transition hover:border-flyto-navy hover:text-flyto-navy">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM2.5 20a5.5 5.5 0 0 1 11 0M17 11a2.5 2.5 0 1 0 0-5M15 15.5a4.5 4.5 0 0 1 6.5 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                    Ver todos los CEOs
                </a>
            </section>
        </div>
    </div>
</section>
