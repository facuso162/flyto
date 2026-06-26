<?php

$basePath = $basePath ?? '';
$aerolineas = $aerolineas ?? null;
$aerolineas = is_array($aerolineas) ? $aerolineas : [];
$paginaActual = max(1, (int) ($paginaActual ?? 1));
$totalPaginas = max(1, (int) ($totalPaginas ?? 1));
$totalAerolineas = max(0, (int) ($totalAerolineas ?? 0));
$flash = $flash ?? null;
$flash = is_array($flash) ? $flash : [];
$urlListado = $basePath . '/admin/aerolineas';
$urlPagina = static fn (int $pagina): string => $urlListado . '?' . http_build_query(['pagina' => $pagina]);

?>
<section class="px-5 py-8 sm:px-8">
    <div class="mx-auto max-w-[900px]">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Administraci&oacute;n</p>
                <h1 class="mt-1 font-display text-3xl font-medium tracking-tight">Aerol&iacute;neas</h1>
            </div>

            <button type="button" class="inline-flex w-fit items-center gap-2 bg-flyto-navy px-4 py-2 text-xs font-medium text-flyto-sand transition hover:bg-flyto-ink">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                Agregar aerol&iacute;nea
            </button>
        </div>

        <?php if (!empty($flash['success'])): ?>
            <div class="mt-6 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status"><?= htmlspecialchars((string) $flash['success'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php elseif (!empty($flash['error'])): ?>
            <div class="mt-6 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= htmlspecialchars((string) $flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="mt-6 border border-flyto-ink/10 bg-white">
            <?php if ($totalAerolineas === 0): ?>
                <div class="px-6 py-16 text-center">
                    <p class="font-display text-lg">No hay aerol&iacute;neas para mostrar</p>
                </div>
            <?php endif; ?>

            <?php foreach ($aerolineas as $aerolinea): ?>
                <?php require __DIR__ . '/../components/aerolinea-item.php'; ?>
            <?php endforeach; ?>
        </div>

        <?php if ($totalAerolineas > 0): ?>
            <nav class="mt-4 flex min-h-9 items-center justify-end gap-3 font-mono text-xs text-flyto-muted" aria-label="Paginacion de aerolineas">
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
