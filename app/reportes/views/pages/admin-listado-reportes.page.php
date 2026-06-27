<?php

$basePath = $basePath ?? '';
$reportes = $reportes ?? null;
$reportes = is_array($reportes) ? $reportes : [];

?>
<section class="px-5 py-8 sm:px-8">
    <div class="max-w-[1024px]">
        <div>
            <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Administracion</p>
            <h1 class="mt-1 font-display text-3xl font-medium tracking-tight">Reportes</h1>
        </div>

        <div class="mt-8 border border-flyto-ink/10 bg-white">
            <?php if ($reportes === []): ?>
                <div class="px-6 py-16 text-center">
                    <p class="font-display text-lg">No hay reportes disponibles</p>
                </div>
            <?php endif; ?>

            <?php foreach ($reportes as $reporte): ?>
                <?php
                    $titulo = (string) ($reporte['titulo'] ?? '');
                    $descripcion = (string) ($reporte['descripcion'] ?? '');
                    $slug = trim((string) ($reporte['slug'] ?? ''), '/');
                    $urlReporte = $basePath . '/admin/reportes/' . rawurlencode($slug);
                ?>
                <article class="grid gap-4 border-b border-flyto-ink/10 px-5 py-6 last:border-b-0 sm:grid-cols-[40px_minmax(0,1fr)_auto] sm:items-center sm:px-6">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center bg-flyto-navy/10 text-flyto-navy" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M5 20v-7m7 7V4m7 16V9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <h2 class="font-display text-[16.8px] font-medium leading-[25.2px] text-flyto-ink"><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>
                        <p class="mt-1 text-xs leading-[19.5px] text-flyto-muted"><?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>

                    <a href="<?= htmlspecialchars($urlReporte, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex w-fit items-center justify-center gap-2 bg-flyto-navy px-4 py-2 text-xs font-medium text-flyto-sand transition hover:bg-flyto-ink">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 16.5 9 11l4 3 7-7M15 7h5v5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Generar
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
