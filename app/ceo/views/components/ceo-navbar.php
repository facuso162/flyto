<?php

$nombreCompleto = trim((string) (($currentUser['nombre'] ?? '') . ' ' . ($currentUser['apellido'] ?? '')));
$nombreCompleto = $nombreCompleto !== '' ? $nombreCompleto : 'CEO';
$basePath = $basePath ?? '';

?>
<header class="relative z-40 h-14 border-b border-flyto-ink/10 bg-white">
    <a class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-3 focus:z-50 focus:bg-white focus:px-4 focus:py-2" href="#contenido-principal">
        Saltar al contenido principal
    </a>
    <div class="flex h-full items-center justify-between px-4 sm:px-6">
        <div class="flex items-center gap-2">
            <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo" class="flex items-center gap-2" aria-label="Panel CEO de Flyto">
                <span class="flex h-7 w-7 items-center justify-center bg-flyto-navy text-white" aria-hidden="true">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M4 12 20 4l-5 16-4-7-7-1Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                </span>
                <span class="font-display text-lg font-semibold text-flyto-navy">Flyto</span>
            </a>
            <span class="ml-2 border border-flyto-ink/10 px-2 py-1 font-mono text-[11px] text-flyto-muted">Panel CEO</span>
        </div>

        <div class="hidden items-center gap-4 sm:flex">
            <a href="<?= htmlspecialchars($basePath ?: '/', ENT_QUOTES, 'UTF-8') ?>" class="flex items-center gap-2 text-sm text-flyto-muted transition hover:text-flyto-ink">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M3.5 12h17M12 3c2.3 2.45 3.5 5.45 3.5 9S14.3 18.55 12 21c-2.3-2.45-3.5-5.45-3.5-9S9.7 5.45 12 3Z" stroke="currentColor" stroke-width="1.5"/></svg>
                Sitio público
            </a>
            <span class="h-4 w-px bg-flyto-ink/10" aria-hidden="true"></span>
            <span class="text-sm font-medium"><?= htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <details class="relative sm:hidden">
            <summary class="flex h-9 w-9 cursor-pointer list-none items-center justify-center text-flyto-navy" aria-label="Abrir menú CEO">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            </summary>
            <div class="absolute right-0 mt-2 w-52 border border-flyto-ink/10 bg-white p-2 shadow-flyto">
                <p class="border-b border-flyto-ink/10 px-3 py-2 text-sm font-medium"><?= htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8') ?></p>
                <a href="<?= htmlspecialchars($basePath ?: '/', ENT_QUOTES, 'UTF-8') ?>" class="block px-3 py-2 text-sm text-flyto-muted">Ir al sitio público</a>
            </div>
        </details>
    </div>
</header>
