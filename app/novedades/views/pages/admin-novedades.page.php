<?php

$basePath = $basePath ?? '';
$novedades = $novedades ?? null;
$novedades = is_array($novedades) ? $novedades : [];
$paginaActual = max(1, (int) ($paginaActual ?? 1));
$totalPaginas = max(1, (int) ($totalPaginas ?? 1));
$totalNovedades = max(0, (int) ($totalNovedades ?? 0));
$flash = $flash ?? null;
$flash = is_array($flash) ? $flash : [];

$html = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$fechaCorta = static function ($value): string {
    if ($value instanceof DateTimeInterface) {
        return $value->format('Y-m-d');
    }

    $value = trim((string) $value);

    if ($value === '') {
        return '';
    }

    try {
        return (new DateTime($value))->format('Y-m-d');
    } catch (Throwable) {
        return substr($value, 0, 10);
    }
};
$estaExpirada = static function (array $novedad): bool {
    if (($novedad['estado'] ?? '') === 'expirada') {
        return true;
    }

    try {
        return new DateTime((string) ($novedad['fechaExpiracion'] ?? '')) <= new DateTime();
    } catch (Throwable) {
        return false;
    }
};
$urlPagina = static fn (int $pagina): string => $basePath . '/admin/novedades?' . http_build_query(['pagina' => $pagina]);

?>
<section class="px-5 py-8 sm:px-8">
    <div class="mx-auto max-w-[900px]">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Administraci&oacute;n</p>
                <h1 class="mt-1 font-display text-3xl font-medium tracking-tight text-flyto-ink">Novedades</h1>
            </div>

            <a href="<?= $html($basePath) ?>/admin/novedades/crear" class="flex w-fit items-center gap-2 bg-flyto-navy px-4 py-2 text-xs font-medium text-flyto-sand transition hover:bg-flyto-ink">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                Agregar novedad
            </a>
        </div>

        <?php if (!empty($flash['success'])): ?>
            <div class="mt-6 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status"><?= $html($flash['success']) ?></div>
        <?php elseif (!empty($flash['error'])): ?>
            <div class="mt-6 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= $html($flash['error']) ?></div>
        <?php endif; ?>

        <div class="mt-6 border border-flyto-ink/10 bg-white">
            <?php if ($totalNovedades === 0): ?>
                <div class="px-6 py-16 text-center">
                    <p class="font-display text-lg text-flyto-ink">No hay novedades para mostrar</p>
                </div>
            <?php endif; ?>

            <?php foreach ($novedades as $index => $novedad): ?>
                <?php
                $expirada = $estaExpirada($novedad);
                $fechaPublicacion = $fechaCorta($novedad['fechaPublicacion'] ?? '');
                $fechaExpiracion = $fechaCorta($novedad['fechaExpiracion'] ?? '');
                ?>
                <article class="<?= $index < count($novedades) - 1 ? 'border-b border-flyto-ink/10' : '' ?> px-6 py-5">
                    <div class="flex flex-col gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                <h2 class="font-display text-base font-medium leading-6 text-flyto-ink"><?= $html($novedad['titulo'] ?? '') ?></h2>
                                <?php if ($expirada): ?>
                                    <span class="bg-red-700/10 px-2 py-0.5 font-mono text-xs font-medium text-red-700">Expirada</span>
                                <?php endif; ?>
                            </div>

                            <p class="mt-1 font-mono text-xs uppercase tracking-[0.08em] text-flyto-muted"><?= $html($novedad['categoria'] ?? '') ?></p>
                            <p class="mt-2 text-xs leading-[19.5px] text-flyto-muted"><?= $html($novedad['texto'] ?? '') ?></p>
                            <p class="mt-2 font-mono text-xs leading-4 text-flyto-muted">
                                Publicada: <?= $html($fechaPublicacion) ?> &middot; Expira: <?= $html($fechaExpiracion) ?>
                            </p>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" class="flex items-center gap-1.5 border border-red-700/30 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-50">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Borrar
                            </button>
                            <button type="button" class="flex items-center gap-1.5 bg-flyto-navy px-3 py-1.5 text-xs font-medium text-flyto-sand transition hover:bg-flyto-ink">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15.5 5.5 3 3M4 20l4.5-1 9-9a2.12 2.12 0 0 0-3-3l-9 9L4 20Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Editar
                            </button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalNovedades > 0): ?>
            <nav class="mt-4 flex min-h-9 items-center justify-end gap-3 font-mono text-xs text-flyto-muted" aria-label="Paginaci&oacute;n de novedades">
                <?php if ($paginaActual > 1): ?>
                    <a href="<?= $html($urlPagina($paginaActual - 1)) ?>" class="border border-flyto-ink/10 px-4 py-2 font-sans font-medium text-flyto-muted transition hover:border-flyto-navy hover:text-flyto-navy">Anterior</a>
                <?php endif; ?>

                <span class="border border-flyto-ink/10 px-4 py-2" aria-label="P&aacute;gina <?= $paginaActual ?> de <?= $totalPaginas ?>"><?= $paginaActual ?>/<?= $totalPaginas ?></span>

                <?php if ($paginaActual < $totalPaginas): ?>
                    <a href="<?= $html($urlPagina($paginaActual + 1)) ?>" class="flex items-center gap-2 border border-flyto-ink/10 px-4 py-2 font-sans font-medium text-flyto-ink transition hover:border-flyto-navy hover:text-flyto-navy">
                        Siguiente
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m9 5 7 7-7 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>
</section>
