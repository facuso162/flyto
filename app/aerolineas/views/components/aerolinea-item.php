<?php

use App\Aerolineas\Models\Aerolinea;

if (!isset($aerolinea) || !$aerolinea instanceof Aerolinea) {
    return;
}

$nombre = $aerolinea->nombre;
$pais = $aerolinea->pais->nombre;
$descripcion = $aerolinea->descripcion;
$codigoIata = $aerolinea->codigoIata;
$basePath = $basePath ?? '';
$editarUrl = $basePath . '/admin/aerolineas/editar?' . http_build_query(['id' => $aerolinea->id]);
$puedeBorrarse = $aerolinea->ceo === null;

?>
<article class="border-b border-flyto-ink/10 px-5 py-5 last:border-b-0 sm:px-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center bg-flyto-navy/10 font-mono text-xs font-medium text-flyto-navy">
            <?= htmlspecialchars($codigoIata, ENT_QUOTES, 'UTF-8') ?>
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <h2 class="font-display text-[17px] font-medium leading-6 text-flyto-ink">
                    <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>
                </h2>

                <?php if ($pais !== ''): ?>
                    <span class="bg-flyto-mist px-2 py-0.5 font-mono text-xs text-flyto-ink">
                        <?= htmlspecialchars($pais, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                <?php endif; ?>

                <?php if ($aerolinea->ceo === null): ?>
                    <span class="border border-amber-500/40 bg-amber-50 px-2 py-0.5 font-mono text-xs text-amber-800">
                        Sin CEO asignado
                    </span>
                <?php endif; ?>
            </div>

            <p class="mt-1 text-xs leading-[1.65] text-flyto-muted">
                <?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?>
            </p>

            <div class="mt-3 flex flex-wrap justify-start gap-2 sm:justify-end">
                <?php if ($puedeBorrarse): ?>
                    <form method="post" action="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/admin/aerolineas/borrar">
                        <input type="hidden" name="aerolineaId" value="<?= (int) $aerolinea->id ?>">
                        <button type="submit" class="inline-flex items-center gap-1.5 border border-red-700/30 px-3 py-1.5 text-xs font-medium text-red-700 transition hover:bg-red-50" aria-label="Borrar <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 5h6m-8 4h10m-9 0 .7 10h6.6L16 9M10 5l.5-1h3L14 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Borrar
                        </button>
                    </form>
                <?php endif; ?>
                <a href="<?= htmlspecialchars($editarUrl, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center gap-1.5 bg-flyto-navy px-3 py-1.5 text-xs font-medium text-flyto-sand transition hover:bg-flyto-ink">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m4 16.5-.5 4 4-.5L18.7 8.8a2.1 2.1 0 0 0 0-3L18.2 5.3a2.1 2.1 0 0 0-3 0L4 16.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="m13.5 7 3.5 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    Editar
                </a>
            </div>
        </div>
    </div>
</article>
