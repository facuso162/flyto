<?php

$basePath = $basePath ?? '';

?>

<footer class="bg-flyto-navy px-6 py-5 text-white/45">
    <div class="mx-auto flex max-w-[960px] flex-col items-center justify-between gap-4 text-xs sm:flex-row">
        <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo" class="flex items-center gap-2 font-display text-sm text-white/90">
            <span class="flex h-5 w-5 items-center justify-center bg-white/15" aria-hidden="true">
                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none"><path d="M4 12 20 4l-5 16-4-7-7-1Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
            </span>
            Flyto · Panel CEO
        </a>
        <nav class="flex gap-7" aria-label="Enlaces del pie CEO">
            <a class="hover:text-white" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo/vuelos">Vuelos</a>
            <a class="hover:text-white" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo/promociones">Promociones</a>
            <a class="hover:text-white" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo/reportes">Reportes</a>
        </nav>
        <span class="font-mono">© <?= date('Y') ?> Flyto S.A.</span>
    </div>
</footer>
