<?php

$basePath = $basePath ?? '';
$vuelos = $vuelos ?? null;
$vuelos = is_array($vuelos) ? $vuelos : [];
$estadoSeleccionado = $estadoSeleccionado ?? null;
$estadoSeleccionado = is_string($estadoSeleccionado) ? $estadoSeleccionado : null;
$paginaActual = max(1, (int) ($paginaActual ?? 1));
$totalPaginas = max(1, (int) ($totalPaginas ?? 1));
$totalVuelos = max(0, (int) ($totalVuelos ?? 0));
$flash = $flash ?? null;
$flash = is_array($flash) ? $flash : [];
$filtros = [
    null => 'Todos',
    'completado' => 'Completados',
    'pendiente' => 'Pendientes',
    'cancelado' => 'Cancelados',
];
$urlListado = $basePath . '/ceo/vuelos';
$urlPagina = static function (int $pagina) use ($urlListado, $estadoSeleccionado): string {
    $query = ['pagina' => $pagina];
    if ($estadoSeleccionado !== null) {
        $query['estado'] = $estadoSeleccionado;
    }

    return $urlListado . '?' . http_build_query($query);
};

?>
<section class="px-5 py-8 sm:px-8 lg:py-8">
    <div class="mx-auto max-w-[900px]">
        <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Administración</p>
        <h1 class="mt-1 font-display text-3xl font-medium tracking-tight">Vuelos</h1>

        <?php if (!empty($flash['success'])): ?>
            <div class="mt-5 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                <?= htmlspecialchars((string) $flash['success'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php elseif (!empty($flash['error'])): ?>
            <div class="mt-5 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                <?= htmlspecialchars((string) $flash['error'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <nav class="flex w-fit max-w-full overflow-x-auto border border-flyto-ink/10" aria-label="Filtrar vuelos por estado">
                <?php foreach ($filtros as $estado => $etiqueta): ?>
                    <?php
                    $activo = $estadoSeleccionado === $estado;
                    $href = $urlListado . ($estado !== null ? '?' . http_build_query(['estado' => $estado]) : '');
                    ?>
                    <a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>" class="whitespace-nowrap border-r border-flyto-ink/10 px-3 py-2 text-xs font-medium last:border-r-0 <?= $activo ? 'bg-flyto-navy text-flyto-sand' : 'text-flyto-muted hover:bg-white hover:text-flyto-ink' ?>" <?= $activo ? 'aria-current="page"' : '' ?>>
                        <?= $etiqueta ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo/vuelos/crear" class="flex w-fit items-center gap-2 bg-flyto-navy px-4 py-2 text-xs font-medium text-flyto-sand transition hover:bg-flyto-ink">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                Agregar vuelo
            </a>
        </div>

        <div class="mt-5 border border-flyto-ink/10 bg-white">
            <?php if ($vuelos === []): ?>
                <div class="px-6 py-16 text-center">
                    <p class="font-display text-lg">No hay vuelos para mostrar</p>
                    <p class="mt-1 text-sm text-flyto-muted">Probá seleccionando otro estado.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($vuelos as $vuelo): ?>
                <?php require __DIR__ . '/../components/vuelo-item.php'; ?>
            <?php endforeach; ?>
        </div>

        <div class="mt-4 flex min-h-9 items-center justify-end gap-3 font-mono text-xs text-flyto-muted">
            <?php if ($paginaActual > 1): ?>
                <a href="<?= htmlspecialchars($urlPagina($paginaActual - 1), ENT_QUOTES, 'UTF-8') ?>" class="border border-flyto-ink/10 px-4 py-2 font-sans font-medium text-flyto-muted hover:border-flyto-navy hover:text-flyto-navy">Anterior</a>
            <?php endif; ?>

            <span class="px-1" aria-label="Página <?= $paginaActual ?> de <?= $totalPaginas ?>"><?= $paginaActual ?>/<?= $totalPaginas ?></span>

            <?php if ($paginaActual < $totalPaginas): ?>
                <a href="<?= htmlspecialchars($urlPagina($paginaActual + 1), ENT_QUOTES, 'UTF-8') ?>" class="flex items-center gap-2 border border-flyto-ink/10 px-4 py-2 font-sans font-medium text-flyto-muted hover:border-flyto-navy hover:text-flyto-navy">
                    Siguiente
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
