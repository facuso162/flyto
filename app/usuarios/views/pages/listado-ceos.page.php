<?php

use App\Usuarios\Models\Usuario;

$basePath = $basePath ?? '';
$ceos = $ceos ?? null;
$ceos = is_array($ceos) ? $ceos : [];
$paginaActual = max(1, (int) ($paginaActual ?? 1));
$totalPaginas = max(1, (int) ($totalPaginas ?? 1));
$totalCeos = max(0, (int) ($totalCeos ?? 0));
$flash = $flash ?? null;
$flash = is_array($flash) ? $flash : [];
$urlListado = $basePath . '/admin/ceos';
$urlPagina = static fn (int $pagina): string => $urlListado . '?' . http_build_query(['pagina' => $pagina]);
$meses = [1 => 'ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];
$desde = static fn (\DateTimeInterface $fecha): string => 'desde ' . $meses[(int) $fecha->format('n')] . ' ' . $fecha->format('Y');

?>
<section class="px-5 py-8 sm:px-8">
    <div class="mx-auto max-w-[900px]">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="font-mono text-xs uppercase text-flyto-muted">Administraci&oacute;n</p>
                <h1 class="mt-1 font-display text-3xl font-medium">CEOs</h1>
            </div>

            <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/admin/ceos/crear" class="inline-flex w-fit items-center gap-2 bg-flyto-navy px-4 py-2 text-xs font-medium text-flyto-sand transition hover:bg-flyto-ink">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                Agregar CEO
            </a>
        </div>

        <?php if (!empty($flash['success'])): ?>
            <div class="mt-5 border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status"><?= htmlspecialchars((string) $flash['success'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!empty($flash['error'])): ?>
            <div class="mt-5 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= htmlspecialchars((string) $flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="mt-6 divide-y divide-flyto-ink/10 border border-flyto-ink/10 bg-white">
            <?php if ($totalCeos === 0): ?>
                <div class="px-6 py-16 text-center">
                    <p class="font-display text-lg">No hay CEOs para mostrar</p>
                </div>
            <?php endif; ?>

            <?php foreach ($ceos as $ceo): ?>
                <?php
                if (!$ceo instanceof Usuario) {
                    continue;
                }

                $aerolinea = $ceo->aerolinea?->nombre ?? 'Sin aerolinea';
                ?>
                <article class="grid gap-4 px-6 py-5 sm:grid-cols-[40px_minmax(0,1fr)_minmax(180px,auto)] sm:items-center">
                    <span class="flex h-10 w-10 items-center justify-center bg-flyto-navy/10 text-flyto-navy" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM6 20a6 6 0 0 1 12 0" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                    </span>

                    <div class="min-w-0">
                        <h2 class="truncate font-display text-lg font-medium leading-6 text-flyto-ink"><?= htmlspecialchars($ceo->nombreCompleto(), ENT_QUOTES, 'UTF-8') ?></h2>
                        <p class="truncate text-xs text-flyto-muted"><?= htmlspecialchars($aerolinea, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>

                    <div class="min-w-0 font-mono text-xs sm:text-right">
                        <p class="truncate text-flyto-ink"><?= htmlspecialchars($ceo->email, ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mt-1 text-flyto-muted"><?= htmlspecialchars($desde($ceo->fechaRegistro), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalCeos > 0): ?>
            <nav class="mt-4 flex min-h-9 items-center justify-end gap-3 font-mono text-xs text-flyto-muted" aria-label="Paginacion de CEOs">
                <?php if ($paginaActual > 1): ?>
                    <a href="<?= htmlspecialchars($urlPagina($paginaActual - 1), ENT_QUOTES, 'UTF-8') ?>" class="border border-flyto-ink/10 px-4 py-2 font-sans font-medium text-flyto-muted hover:border-flyto-navy hover:text-flyto-navy">Anterior</a>
                <?php endif; ?>

                <span class="border border-flyto-ink/10 px-4 py-2" aria-label="Pagina <?= $paginaActual ?> de <?= $totalPaginas ?>"><?= $paginaActual ?>/<?= $totalPaginas ?></span>

                <?php if ($paginaActual < $totalPaginas): ?>
                    <a href="<?= htmlspecialchars($urlPagina($paginaActual + 1), ENT_QUOTES, 'UTF-8') ?>" class="flex items-center gap-2 border border-flyto-ink/10 px-4 py-2 font-sans font-medium text-flyto-ink hover:border-flyto-navy hover:text-flyto-navy">
                        Siguiente
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>
</section>
