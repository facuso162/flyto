<?php

$currentPath = $currentPath ?? '/ceo';
$basePath = $basePath ?? '';
$items = [
    ['/ceo', 'Menú principal', '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>'],
    ['/ceo/vuelos', 'Vuelos', '<path d="m4 13 16-8-6 15-3-6-7-1Z"/>'],
    ['/ceo/promociones', 'Promociones', '<path d="M20 13 13 20 4 11V4h7l9 9Z"/><circle cx="8" cy="8" r="1" fill="currentColor" stroke="none"/>'],
    ['/ceo/reportes', 'Reportes', '<path d="M5 20v-7m7 7V4m7 16V9"/>'],
];

?>
<aside class="hidden w-56 shrink-0 flex-col border-r border-flyto-ink/10 bg-white lg:flex" aria-label="Navegación del panel CEO">
    <p class="px-5 pb-2 pt-5 font-mono text-[9px] uppercase tracking-[0.16em] text-flyto-muted/60">Navegación</p>
    <nav class="flex-1">
        <?php foreach ($items as [$href, $label, $icon]): ?>
            <?php $active = $currentPath === $href; ?>
            <a href="<?= htmlspecialchars($basePath . $href, ENT_QUOTES, 'UTF-8') ?>" class="flex h-10 items-center gap-3 border-r-2 px-5 text-sm <?= $active ? 'border-flyto-navy bg-flyto-navy/10 font-medium text-flyto-navy' : 'border-transparent text-flyto-muted hover:bg-flyto-sand hover:text-flyto-ink' ?>">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $icon ?></svg>
                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <form method="post" action="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/auth/logout" class="border-t border-flyto-ink/10 p-5">
        <button type="submit" class="flex items-center gap-3 text-sm text-red-500 transition hover:text-red-700">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 5H5v14h5M14 8l4 4-4 4M8 12h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Cerrar sesión
        </button>
    </form>
</aside>
