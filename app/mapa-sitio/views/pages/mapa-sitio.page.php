<?php

$basePath = $basePath ?? '';
$mapTitle = $mapTitle ?? 'Mapa de Sitio';
$mapDescription = $mapDescription ?? '';
$categories = $categories ?? [];
$categories = is_array($categories) ? $categories : [];
$e = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

?>
<section class="px-6 py-12 sm:py-16">
    <div class="mx-auto max-w-7xl">
        <div class="max-w-2xl">
            <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Navegación</p>
            <h1 class="mt-2 font-display text-4xl font-medium tracking-tight text-flyto-navy"><?= $e((string) $mapTitle) ?></h1>
            <?php if ($mapDescription !== ''): ?>
                <p class="mt-4 text-sm leading-6 text-flyto-muted"><?= $e((string) $mapDescription) ?></p>
            <?php endif; ?>
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($categories as $category): ?>
                <?php
                    $categoryTitle = (string) ($category['title'] ?? '');
                    $links = isset($category['links']) && is_array($category['links'])
                        ? $category['links']
                        : [];
                ?>
                <section class="border border-flyto-ink/10 bg-white p-6" aria-labelledby="map-category-<?= $e(md5($categoryTitle)) ?>">
                    <h2 id="map-category-<?= $e(md5($categoryTitle)) ?>" class="font-display text-xl font-medium text-flyto-ink">
                        <?= $e($categoryTitle) ?>
                    </h2>
                    <ul class="mt-5 space-y-3">
                        <?php foreach ($links as $link): ?>
                            <?php
                                $label = (string) ($link['label'] ?? '');
                                $href = (string) ($link['href'] ?? '/');
                            ?>
                            <li>
                                <a href="<?= $e($basePath . $href) ?>" class="group inline-flex items-center gap-2 text-sm text-flyto-muted transition hover:text-flyto-navy">
                                    <svg class="h-3.5 w-3.5 shrink-0 text-flyto-gold transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 12h14m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?= $e($label) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</section>
